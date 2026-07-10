<?php

namespace Tests\Feature\Academics;

use App\Domains\Academics\Exceptions\SectionSubjectTeacherHasGradesException;
use App\Domains\Academics\Exceptions\SubjectInUseException;
use App\Domains\Academics\Exceptions\SubjectTeacherInUseException;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Academics\Services\SectionSubjectTeacher\DeleteSectionSubjectTeacherService;
use App\Domains\Academics\Services\Subjects\DeleteSubjectService;
use App\Domains\Academics\Services\SubjectTeacher\DeleteSubjectTeacherService;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
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
        $sst = SectionSubjectTeacher::factory()->create();
        $column = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);
        $student = Student::factory()->inSection($sst->section)->create();
        $enrollment = $student->enrollments()->where('section_id', $sst->section_id)->first();
        Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);

        try {
            app(DeleteSectionSubjectTeacherService::class)->handle($sst);

            $this->fail('Expected SectionSubjectTeacherHasGradesException was not thrown.');
        } catch (SectionSubjectTeacherHasGradesException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('SECTION_SUBJECT_TEACHER_HAS_GRADES', $e->errorCode());
            $this->assertSame(
                'No se puede eliminar esta asignación porque tiene calificaciones registradas.',
                $e->getMessage()
            );
        }

        $this->assertSame(1, SectionSubjectTeacher::count());
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
