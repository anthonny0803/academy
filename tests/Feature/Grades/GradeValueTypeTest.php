<?php

namespace Tests\Feature\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Exceptions\GradeValueNotNumericException;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Services\Grades\BatchGradeService;
use App\Domains\Grades\Services\Grades\StoreGradeService;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The grade services own the rule that a value must be a number, instead of
 * borrowing it from the `numeric` rule of the form request. Calling them
 * directly is what any second write path — a job, a console command — does.
 */
class GradeValueTypeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_store_service_rejects_a_non_numeric_value(): void
    {
        [$column, $enrollment] = $this->gradableCell();

        try {
            app(StoreGradeService::class)->handle($column, $enrollment, ['value' => 'diez']);

            $this->fail('Expected GradeValueNotNumericException was not thrown.');
        } catch (GradeValueNotNumericException $e) {
            $this->assertSame(422, $e->statusCode());
            $this->assertSame('GRADE_VALUE_NOT_NUMERIC', $e->errorCode());
            $this->assertSame('La nota debe ser un valor numérico.', $e->getMessage());
        }

        $this->assertDatabaseCount('grades', 0);
    }

    public function test_batch_service_rejects_a_non_numeric_value(): void
    {
        [$column, $enrollment] = $this->gradableCell();

        try {
            app(BatchGradeService::class)->handle($column, [
                ['enrollment_id' => $enrollment->id, 'value' => 'diez'],
            ]);

            $this->fail('Expected GradeValueNotNumericException was not thrown.');
        } catch (GradeValueNotNumericException $e) {
            $this->assertSame(422, $e->statusCode());
        }

        $this->assertDatabaseCount('grades', 0);
    }

    public function test_store_service_rejects_a_missing_value(): void
    {
        [$column, $enrollment] = $this->gradableCell();

        $this->expectException(GradeValueNotNumericException::class);

        app(StoreGradeService::class)->handle($column, $enrollment, []);
    }

    public function test_a_numeric_string_is_still_a_valid_value(): void
    {
        [$column, $enrollment] = $this->gradableCell();

        $grade = app(StoreGradeService::class)->handle($column, $enrollment, ['value' => '8.5']);

        $this->assertEquals(8.5, (float) $grade->value);
    }

    /**
     * @return array{0: GradeColumn, 1: Enrollment}
     */
    private function gradableCell(): array
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);
        $enrollment = Student::factory()->inSection($sst->section)->create()
            ->enrollments()
            ->where('section_id', $sst->section_id)
            ->first();

        return [$column, $enrollment];
    }
}
