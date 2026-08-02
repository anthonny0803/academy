<?php

namespace Tests\Feature\Academics;

use App\Domains\Academics\Exceptions\AcademicPeriodClosedException;
use App\Domains\Academics\Exceptions\AcademicPeriodHasActiveSectionsException;
use App\Domains\Academics\Exceptions\AcademicPeriodHasEnrollmentsException;
use App\Domains\Academics\Exceptions\SectionSubjectTeacherHasGradesException;
use App\Domains\Academics\Exceptions\SubjectInUseException;
use App\Domains\Academics\Exceptions\SubjectTeacherInUseException;
use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Academics\Services\AcademicPeriods\DeleteAcademicPeriodService;
use App\Domains\Academics\Services\SectionSubjectTeacher\DeleteSectionSubjectTeacherService;
use App\Domains\Academics\Services\Subjects\DeleteSubjectService;
use App\Domains\Academics\Services\SubjectTeacher\DeleteSubjectTeacherService;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Services\Grades\DeleteGradeService;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteInUseDomainExceptionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function inactiveSubjectWithTeacher(): Subject
    {
        $subject = Subject::factory()->create(['is_active' => false]);
        $subject->teachers()->attach(Teacher::factory()->create()->id);

        return $subject;
    }

    /**
     * A deletable period per the policy: active, with an inactive section that
     * still holds a withdrawn enrollment and its grade.
     */
    private function periodWithGradedHistory(): AcademicPeriod
    {
        $period = AcademicPeriod::factory()->create();
        $section = Section::factory()->inactive()->create(['academic_period_id' => $period->id]);

        $student = Student::factory()->inSection($section)->create();
        $enrollment = $student->enrollments()->where('section_id', $section->id)->firstOrFail();
        $enrollment->update(['status' => EnrollmentStatus::Withdrawn->value]);

        $sst = SectionSubjectTeacher::factory()->create(['section_id' => $section->id]);
        $column = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);
        Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);

        return $period;
    }

    /**
     * @return array{0: SectionSubjectTeacher, 1: Grade}
     */
    private function gradedAssignment(): array
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);
        $student = Student::factory()->inSection($sst->section)->create();
        $enrollment = $student->enrollments()->where('section_id', $sst->section_id)->first();

        $grade = Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);

        return [$sst, $grade];
    }

    private function assertDeletingTheAssignmentIsRejected(SectionSubjectTeacher $sst): void
    {
        try {
            app(DeleteSectionSubjectTeacherService::class)->handle($sst);

            $this->fail('Expected SectionSubjectTeacherHasGradesException was not thrown.');
        } catch (SectionSubjectTeacherHasGradesException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('SECTION_SUBJECT_TEACHER_HAS_GRADES', $e->errorCode());
            $this->assertSame(
                'No se puede eliminar esta asignación porque tiene calificaciones en su historial, incluidas las eliminadas.',
                $e->getMessage()
            );
        }

        $this->assertSame(1, SectionSubjectTeacher::count());
    }

    private function assertHistorySurvived(AcademicPeriod $period): void
    {
        $this->assertDatabaseHas('academic_periods', ['id' => $period->id]);
        $this->assertSame(1, $period->sections()->count());
        $this->assertSame(1, $period->enrollments()->count());
        $this->assertDatabaseCount('grades', 1);
    }

    public function test_delete_subject_throws_when_it_has_section_assignments(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $subject = $sst->subject;

        try {
            app(DeleteSubjectService::class)->handle($subject);

            $this->fail('Expected SubjectInUseException was not thrown.');
        } catch (SubjectInUseException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('SUBJECT_IN_USE', $e->errorCode());
            $this->assertSame(
                'No se puede eliminar una asignatura con asignaciones en secciones.',
                $e->getMessage()
            );
        }

        $this->assertSame(1, Subject::count());
    }

    public function test_delete_subject_throws_when_it_has_assigned_teachers(): void
    {
        $subject = $this->inactiveSubjectWithTeacher();

        try {
            app(DeleteSubjectService::class)->handle($subject);

            $this->fail('Expected SubjectInUseException was not thrown.');
        } catch (SubjectInUseException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('SUBJECT_IN_USE', $e->errorCode());
            $this->assertSame(
                'No se puede eliminar una asignatura con profesores asignados.',
                $e->getMessage()
            );
        }

        $this->assertSame(1, Subject::count());
    }

    public function test_delete_subject_teacher_throws_when_it_has_section_assignments(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();

        try {
            app(DeleteSubjectTeacherService::class)->handle($sst->teacher, $sst->subject);

            $this->fail('Expected SubjectTeacherInUseException was not thrown.');
        } catch (SubjectTeacherInUseException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('SUBJECT_TEACHER_IN_USE', $e->errorCode());
            $this->assertSame(
                'No se puede eliminar una asignación con registros asociados.',
                $e->getMessage()
            );
        }
    }

    public function test_delete_section_subject_teacher_throws_when_it_has_grades(): void
    {
        [$sst] = $this->gradedAssignment();

        $this->assertDeletingTheAssignmentIsRejected($sst);
    }

    /**
     * Grade columns cascade with their assignment, and `grades` restricts their
     * deletion. A soft-deleted grade still holds that restriction, so the guard
     * has to see the trashed history the foreign key sees.
     */
    public function test_delete_section_subject_teacher_throws_when_its_only_grades_are_deleted(): void
    {
        [$sst, $grade] = $this->gradedAssignment();
        app(DeleteGradeService::class)->handle($grade);

        $this->assertDeletingTheAssignmentIsRejected($sst);
    }

    public function test_delete_academic_period_throws_when_a_section_has_enrollments(): void
    {
        $period = $this->periodWithGradedHistory();

        try {
            app(DeleteAcademicPeriodService::class)->handle($period);

            $this->fail('Expected AcademicPeriodHasEnrollmentsException was not thrown.');
        } catch (AcademicPeriodHasEnrollmentsException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('ACADEMIC_PERIOD_HAS_ENROLLMENTS', $e->errorCode());
            $this->assertSame(
                "No se puede eliminar el período académico '{$period->name}' porque tiene inscripciones con historial académico.",
                $e->getMessage()
            );
        }

        $this->assertHistorySurvived($period);
    }

    public function test_delete_academic_period_throws_when_the_period_is_closed(): void
    {
        $period = AcademicPeriod::factory()->inactive()->create();

        try {
            app(DeleteAcademicPeriodService::class)->handle($period);

            $this->fail('Expected AcademicPeriodClosedException was not thrown.');
        } catch (AcademicPeriodClosedException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('ACADEMIC_PERIOD_CLOSED', $e->errorCode());
            $this->assertSame(
                "No se puede eliminar el período académico '{$period->name}' porque está cerrado y contiene datos históricos.",
                $e->getMessage()
            );
        }

        $this->assertDatabaseHas('academic_periods', ['id' => $period->id]);
    }

    public function test_delete_academic_period_throws_when_it_has_active_sections(): void
    {
        $period = AcademicPeriod::factory()->create();
        $section = Section::factory()->create(['academic_period_id' => $period->id]);

        try {
            app(DeleteAcademicPeriodService::class)->handle($period);

            $this->fail('Expected AcademicPeriodHasActiveSectionsException was not thrown.');
        } catch (AcademicPeriodHasActiveSectionsException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('ACADEMIC_PERIOD_HAS_ACTIVE_SECTIONS', $e->errorCode());
            $this->assertSame(
                "No se puede eliminar el período académico '{$period->name}' porque tiene secciones activas. Desactívalas primero.",
                $e->getMessage()
            );
        }

        $this->assertDatabaseHas('academic_periods', ['id' => $period->id]);
        $this->assertDatabaseHas('sections', ['id' => $section->id]);
    }

    public function test_web_destroy_closed_academic_period_is_denied_by_the_policy(): void
    {
        $period = AcademicPeriod::factory()->inactive()->create();
        $supervisor = User::factory()->supervisor()->create();

        // The policy still answers first: the domain guard covers the callers
        // that never reach an authorization layer.
        $response = $this->actingAs($supervisor)
            ->delete(route('academic-periods.destroy', $period));

        $response->assertSessionHas(
            'error',
            'No puedes eliminar un período académico cerrado. Contiene datos históricos importantes.'
        );
        $this->assertDatabaseHas('academic_periods', ['id' => $period->id]);
    }

    public function test_delete_academic_period_removes_empty_inactive_sections(): void
    {
        $period = AcademicPeriod::factory()->create();
        $section = Section::factory()->inactive()->create(['academic_period_id' => $period->id]);
        $sst = SectionSubjectTeacher::factory()->create(['section_id' => $section->id]);

        $results = app(DeleteAcademicPeriodService::class)->handle($period);

        $this->assertSame(['sections_deleted' => 1, 'assignments_deleted' => 1], $results);
        $this->assertDatabaseMissing('academic_periods', ['id' => $period->id]);
        $this->assertDatabaseMissing('sections', ['id' => $section->id]);
        $this->assertDatabaseMissing('section_subject_teacher', ['id' => $sst->id]);
    }

    public function test_web_destroy_academic_period_reports_what_it_removed(): void
    {
        $period = AcademicPeriod::factory()->create();
        $section = Section::factory()->inactive()->create(['academic_period_id' => $period->id]);
        SectionSubjectTeacher::factory()->create(['section_id' => $section->id]);
        $supervisor = User::factory()->supervisor()->create();

        $response = $this->actingAs($supervisor)
            ->delete(route('academic-periods.destroy', $period));

        $response->assertSessionHas(
            'success',
            '¡Período académico eliminado correctamente! Incluye 1 sección y 1 asignación asociadas.'
        );
    }

    public function test_web_destroy_empty_academic_period_reports_nothing_extra(): void
    {
        $period = AcademicPeriod::factory()->create();
        $supervisor = User::factory()->supervisor()->create();

        $response = $this->actingAs($supervisor)
            ->delete(route('academic-periods.destroy', $period));

        $response->assertSessionHas('success', '¡Período académico eliminado correctamente!');
    }

    public function test_academic_period_enrollments_resolve_through_its_sections(): void
    {
        $period = $this->periodWithGradedHistory();
        Enrollment::factory()->create();

        $enrollments = $period->enrollments()->get();

        $this->assertCount(1, $enrollments);
        $this->assertSame(
            Section::where('academic_period_id', $period->id)->value('id'),
            $enrollments->first()->section_id
        );
    }

    public function test_api_destroy_academic_period_with_enrollments_returns_a_conflict(): void
    {
        $period = $this->periodWithGradedHistory();
        $token = User::factory()->supervisor()->create()->createToken('api')->plainTextToken;

        $response = $this->withToken($token)->deleteJson("/api/v1/academic-periods/{$period->id}");

        $response->assertStatus(409)
            ->assertJsonPath('error.code', 'ACADEMIC_PERIOD_HAS_ENROLLMENTS')
            ->assertJsonPath(
                'error.message',
                "No se puede eliminar el período académico '{$period->name}' porque tiene inscripciones con historial académico."
            );
        $this->assertHistorySurvived($period);
    }

    public function test_web_destroy_academic_period_with_enrollments_flashes_the_domain_error(): void
    {
        $period = $this->periodWithGradedHistory();
        $supervisor = User::factory()->supervisor()->create();

        $response = $this->actingAs($supervisor)
            ->from(route('academic-periods.index'))
            ->delete(route('academic-periods.destroy', $period));

        $response->assertRedirect(route('academic-periods.index'));
        $response->assertSessionHas(
            'error',
            "No se puede eliminar el período académico '{$period->name}' porque tiene inscripciones con historial académico."
        );
        $this->assertHistorySurvived($period);
    }

    public function test_api_destroy_subject_in_use_returns_a_conflict(): void
    {
        $subject = $this->inactiveSubjectWithTeacher();
        $token = User::factory()->developer()->create()->createToken('api')->plainTextToken;

        $response = $this->withToken($token)->deleteJson("/api/v1/subjects/{$subject->id}");

        $response->assertStatus(409)
            ->assertJsonPath('error.code', 'SUBJECT_IN_USE')
            ->assertJsonPath(
                'error.message',
                'No se puede eliminar una asignatura con profesores asignados.'
            );
        $this->assertSame(1, Subject::count());
    }

    public function test_web_destroy_subject_in_use_flashes_the_domain_error(): void
    {
        $subject = $this->inactiveSubjectWithTeacher();
        $supervisor = User::factory()->supervisor()->create();

        $response = $this->actingAs($supervisor)
            ->from(route('subjects.index'))
            ->delete(route('subjects.destroy', $subject));

        $response->assertRedirect(route('subjects.index'));
        $response->assertSessionHas(
            'error',
            'No se puede eliminar una asignatura con profesores asignados.'
        );
        $this->assertSame(1, Subject::count());
    }
}
