<?php

namespace Tests\Feature\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Grades\Exceptions\EnrollmentNotGradableException;
use App\Domains\Grades\Exceptions\GradeOutOfRangeException;
use App\Domains\Grades\Exceptions\GradingConfigurationIncompleteException;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Services\Grades\StoreGradeService;
use App\Domains\Grades\Services\Grades\UpdateGradeService;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeDomainExceptionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * @return array{0: SectionSubjectTeacher, 1: GradeColumn, 2: \App\Domains\Enrollments\Models\Enrollment}
     */
    private function gradableGraph(int $columnWeight = 100): array
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => $columnWeight,
        ]);
        $student = Student::factory()->inSection($sst->section)->create();
        $enrollment = $student->enrollments()->where('section_id', $sst->section_id)->first();

        return [$sst, $column, $enrollment];
    }

    public function test_store_throws_when_the_configuration_is_incomplete(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph(columnWeight: 60);

        try {
            app(StoreGradeService::class)->handle($column, $enrollment, ['value' => 8.5]);

            $this->fail('Expected GradingConfigurationIncompleteException was not thrown.');
        } catch (GradingConfigurationIncompleteException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('GRADING_CONFIGURATION_INCOMPLETE', $e->errorCode());
            $this->assertSame(
                'La configuración de evaluaciones debe sumar 100% antes de calificar.',
                $e->getMessage()
            );
        }

        $this->assertSame(0, Grade::count());
    }

    public function test_store_throws_when_the_enrollment_is_outside_the_section(): void
    {
        [$sst, $column] = $this->gradableGraph();
        [, , $foreignEnrollment] = $this->gradableGraph();

        try {
            app(StoreGradeService::class)->handle($column, $foreignEnrollment, ['value' => 8.5]);

            $this->fail('Expected EnrollmentNotGradableException was not thrown.');
        } catch (EnrollmentNotGradableException $e) {
            $this->assertSame(422, $e->statusCode());
            $this->assertSame('ENROLLMENT_NOT_GRADABLE', $e->errorCode());
            $this->assertSame('El estudiante no pertenece a esta sección.', $e->getMessage());
        }

        $this->assertSame(0, Grade::count());
    }

    public function test_store_throws_when_the_enrollment_is_not_active(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $enrollment->status = EnrollmentStatus::Completed->value;
        $enrollment->save();

        try {
            app(StoreGradeService::class)->handle($column, $enrollment, ['value' => 8.5]);

            $this->fail('Expected EnrollmentNotGradableException was not thrown.');
        } catch (EnrollmentNotGradableException $e) {
            $this->assertSame(422, $e->statusCode());
            $this->assertSame('ENROLLMENT_NOT_GRADABLE', $e->errorCode());
            $this->assertSame('La inscripción del estudiante no está activa.', $e->getMessage());
        }

        $this->assertSame(0, Grade::count());
    }

    public function test_store_throws_when_the_value_is_out_of_range(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();

        try {
            app(StoreGradeService::class)->handle($column, $enrollment, ['value' => 999]);

            $this->fail('Expected GradeOutOfRangeException was not thrown.');
        } catch (GradeOutOfRangeException $e) {
            $this->assertSame(422, $e->statusCode());
            $this->assertSame('GRADE_OUT_OF_RANGE', $e->errorCode());
            $this->assertSame('La nota debe estar entre 0.00 y 10.00.', $e->getMessage());
        }

        $this->assertSame(0, Grade::count());
    }

    public function test_update_throws_when_the_value_is_out_of_range(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $grade = Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
            'value' => 8.5,
        ]);

        try {
            app(UpdateGradeService::class)->handle($grade, ['value' => 999]);

            $this->fail('Expected GradeOutOfRangeException was not thrown.');
        } catch (GradeOutOfRangeException $e) {
            $this->assertSame(422, $e->statusCode());
            $this->assertSame('GRADE_OUT_OF_RANGE', $e->errorCode());
            $this->assertSame('La nota debe estar entre 0.00 y 10.00.', $e->getMessage());
        }

        $this->assertSame(8.5, (float) $grade->fresh()->value);
    }
}
