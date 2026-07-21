<?php

namespace Tests\Feature\AcademicPeriods;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Services\AcademicPeriods\CloseAcademicPeriodService;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Students\Enums\StudentSituation;
use App\Domains\Students\Models\Student;
use Closure;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CloseAcademicPeriodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_close_period_completes_enrollments_with_correct_pass_fail(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $academicPeriod = AcademicPeriod::factory()->create([
            'passing_grade' => 5,
        ]);

        $section = Section::factory()->create([
            'academic_period_id' => $academicPeriod->id,
        ]);

        // Subject assignment with grade column (100% weight)
        $sst = SectionSubjectTeacher::factory()->create([
            'section_id' => $section->id,
        ]);
        $gradeColumn = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);

        // Shared representative
        $representative = Representative::factory()->create();

        // Student who PASSES (grade 8 >= passing 5)
        $passingStudent = Student::factory()->inSection($section)->create([
            'representative_id' => $representative->id,
        ]);
        $passingEnrollment = $passingStudent->enrollments()
            ->where('section_id', $section->id)->first();
        Grade::factory()->create([
            'enrollment_id' => $passingEnrollment->id,
            'grade_column_id' => $gradeColumn->id,
            'value' => 8,
        ]);

        // Student who FAILS (grade 3 < passing 5)
        $failingStudent = Student::factory()->inSection($section)->create([
            'representative_id' => $representative->id,
        ]);
        $failingEnrollment = $failingStudent->enrollments()
            ->where('section_id', $section->id)->first();
        Grade::factory()->create([
            'enrollment_id' => $failingEnrollment->id,
            'grade_column_id' => $gradeColumn->id,
            'value' => 3,
        ]);

        // Representative should be active after student creation
        $representative->refresh();
        $this->assertTrue($representative->is_active);

        // Act: Close the period
        $response = $this->actingAs($supervisor)->patch(
            route('academic-periods.close', $academicPeriod)
        );

        $response->assertRedirect(route('academic-periods.index'));
        $response->assertSessionHas('success');

        // Passing student: completed + passed=true
        $passingEnrollment->refresh();
        $this->assertEquals(EnrollmentStatus::Completed->value, $passingEnrollment->status);
        $this->assertTrue($passingEnrollment->passed);

        // Failing student: completed + passed=false
        $failingEnrollment->refresh();
        $this->assertEquals(EnrollmentStatus::Completed->value, $failingEnrollment->status);
        $this->assertFalse($failingEnrollment->passed);

        // Section and period deactivated
        $section->refresh();
        $this->assertFalse($section->is_active);

        $academicPeriod->refresh();
        $this->assertFalse($academicPeriod->is_active);

        // Students deactivated (no other active enrollments)
        $passingStudent->refresh();
        $this->assertFalse($passingStudent->is_active);
        $this->assertEquals(StudentSituation::Inactive, $passingStudent->situation);

        $failingStudent->refresh();
        $this->assertFalse($failingStudent->is_active);

        // Representative deactivated (no active students)
        $representative->refresh();
        $this->assertFalse($representative->is_active);
    }

    public function test_cannot_close_period_with_incomplete_grades(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $academicPeriod = AcademicPeriod::factory()->create();
        $section = Section::factory()->create([
            'academic_period_id' => $academicPeriod->id,
        ]);

        // SST with grade column but NO grades for the student
        $sst = SectionSubjectTeacher::factory()->create([
            'section_id' => $section->id,
        ]);
        GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);

        Student::factory()->inSection($section)->create();

        $response = $this->actingAs($supervisor)
            ->from(route('academic-periods.show', $academicPeriod))
            ->patch(route('academic-periods.close', $academicPeriod));

        // Service throws exception, controller catches and redirects with error
        $response->assertRedirect(route('academic-periods.show', $academicPeriod));
        $response->assertSessionHas(
            'error',
            'No se puede cerrar el período. Hay inscripciones con datos incompletos.'
        );
    }

    public function test_validation_and_preview_query_count_does_not_grow_with_enrollments(): void
    {
        $smallPeriod = $this->periodWithGradedEnrollments(2);
        $largePeriod = $this->periodWithGradedEnrollments(6);

        $service = app(CloseAcademicPeriodService::class);

        $this->assertSame(
            $this->countQueries(fn () => $service->validateForClose($smallPeriod)),
            $this->countQueries(fn () => $service->validateForClose($largePeriod))
        );

        $this->assertSame(
            $this->countQueries(fn () => $service->getClosePreview($smallPeriod)),
            $this->countQueries(fn () => $service->getClosePreview($largePeriod))
        );
    }

    public function test_validation_lists_every_missing_column_per_student(): void
    {
        $academicPeriod = AcademicPeriod::factory()->create();
        $section = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $assignment = SectionSubjectTeacher::factory()->create(['section_id' => $section->id]);
        $gradedColumn = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $assignment->id,
            'name' => 'Primer corte',
            'weight' => 40,
        ]);
        GradeColumn::factory()->create([
            'section_subject_teacher_id' => $assignment->id,
            'name' => 'Segundo corte',
            'weight' => 60,
        ]);

        $student = Student::factory()->inSection($section)->create();
        Grade::factory()->create([
            'enrollment_id' => $this->enrollmentFor($student, $section)->id,
            'grade_column_id' => $gradedColumn->id,
            'value' => 8,
        ]);

        $validation = app(CloseAcademicPeriodService::class)->validateForClose($academicPeriod);

        $this->assertFalse($validation['can_close']);
        $this->assertSame(1, $validation['summary']['enrollments_with_issues']);

        $subjectIssues = $validation['issues']['sections'][$section->name][$assignment->subject->name];

        $this->assertSame('grades', $subjectIssues[0]['type']);
        $this->assertSame(['SEGUNDO CORTE'], $subjectIssues[0]['students'][0]['missing']);
    }

    public function test_validation_reports_incomplete_configuration(): void
    {
        $academicPeriod = AcademicPeriod::factory()->create();
        $section = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $assignment = SectionSubjectTeacher::factory()->create(['section_id' => $section->id]);
        GradeColumn::factory()->create([
            'section_subject_teacher_id' => $assignment->id,
            'weight' => 50,
        ]);

        Student::factory()->inSection($section)->create();

        $validation = app(CloseAcademicPeriodService::class)->validateForClose($academicPeriod);

        $this->assertFalse($validation['can_close']);

        $subjectIssues = $validation['issues']['sections'][$section->name][$assignment->subject->name];

        $this->assertSame('configuration', $subjectIssues[0]['type']);
        $this->assertSame('Configuración incompleta: 50% de 100%', $subjectIssues[0]['message']);
    }

    public function test_pass_fail_uses_the_weighted_average_of_every_column(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $academicPeriod = AcademicPeriod::factory()->create(['passing_grade' => 5]);
        $section = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $assignment = SectionSubjectTeacher::factory()->create(['section_id' => $section->id]);
        $lightColumn = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $assignment->id,
            'weight' => 40,
        ]);
        $heavyColumn = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $assignment->id,
            'weight' => 60,
        ]);

        // Weighted average 7.0: passes despite failing the lighter column
        $passingEnrollment = $this->enrollmentWithGrades($section, [
            $lightColumn->id => 4,
            $heavyColumn->id => 9,
        ]);

        // Weighted average 4.62: fails despite passing the lighter column
        $failingEnrollment = $this->enrollmentWithGrades($section, [
            $lightColumn->id => 9,
            $heavyColumn->id => 1.7,
        ]);

        $this->actingAs($supervisor)->patch(route('academic-periods.close', $academicPeriod));

        $this->assertTrue($passingEnrollment->refresh()->passed);
        $this->assertFalse($failingEnrollment->refresh()->passed);
    }

    public function test_preview_counts_students_across_every_section(): void
    {
        $academicPeriod = AcademicPeriod::factory()->create(['passing_grade' => 5]);

        $firstSection = $this->gradedSection($academicPeriod, 8);
        $secondSection = $this->gradedSection($academicPeriod, 3);

        $preview = app(CloseAcademicPeriodService::class)->getClosePreview($academicPeriod);

        $this->assertSame(2, $preview['sections_to_deactivate']);
        $this->assertSame(1, $preview['passed']);
        $this->assertSame(1, $preview['failed']);

        $sectionNames = array_column($preview['details'], 'name');
        $this->assertContains($firstSection->name, $sectionNames);
        $this->assertContains($secondSection->name, $sectionNames);
    }

    private function countQueries(Closure $callback): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $callback();

        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $queries;
    }

    private function periodWithGradedEnrollments(int $enrollments): AcademicPeriod
    {
        $academicPeriod = AcademicPeriod::factory()->create(['passing_grade' => 5]);
        $section = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $columns = collect([40, 60])->map(fn (int $weight) => GradeColumn::factory()->create([
            'section_subject_teacher_id' => SectionSubjectTeacher::factory()->create([
                'section_id' => $section->id,
            ])->id,
            'weight' => $weight,
        ]));

        for ($i = 0; $i < $enrollments; $i++) {
            $this->enrollmentWithGrades($section, $columns->mapWithKeys(
                fn (GradeColumn $column) => [$column->id => 8]
            )->all());
        }

        return $academicPeriod;
    }

    private function gradedSection(AcademicPeriod $academicPeriod, float $value): Section
    {
        $section = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $column = GradeColumn::factory()->create([
            'section_subject_teacher_id' => SectionSubjectTeacher::factory()->create([
                'section_id' => $section->id,
            ])->id,
            'weight' => 100,
        ]);

        $this->enrollmentWithGrades($section, [$column->id => $value]);

        return $section;
    }

    /**
     * @param  array<string, float>  $valuesByColumnId
     */
    private function enrollmentWithGrades(Section $section, array $valuesByColumnId): Enrollment
    {
        $student = Student::factory()->inSection($section)->create();
        $enrollment = $this->enrollmentFor($student, $section);

        foreach ($valuesByColumnId as $columnId => $value) {
            Grade::factory()->create([
                'enrollment_id' => $enrollment->id,
                'grade_column_id' => $columnId,
                'value' => $value,
            ]);
        }

        return $enrollment;
    }

    private function enrollmentFor(Student $student, Section $section): Enrollment
    {
        return $student->enrollments()->where('section_id', $section->id)->firstOrFail();
    }
}
