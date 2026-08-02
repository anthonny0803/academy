<?php

namespace Tests\Feature\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Exceptions\GradeAlreadyExistsException;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Services\Grades\DeleteGradeService;
use App\Domains\Grades\Services\Grades\StoreGradeService;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Deleting a grade is a soft delete, so the row keeps living in a table whose
 * unique index does not exclude trashed rows. These tests fix the behaviour
 * the cell must have afterwards: it is free again, and the deleted grade
 * survives as history.
 */
class GradeRegradeAfterDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * @return array{0: SectionSubjectTeacher, 1: GradeColumn, 2: Enrollment}
     */
    private function gradableGraph(): array
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);
        $student = Student::factory()->inSection($sst->section)->create();
        $enrollment = $student->enrollments()->where('section_id', $sst->section_id)->first();

        return [$sst, $column, $enrollment];
    }

    private function deleteGradeFor(GradeColumn $column, Enrollment $enrollment): Grade
    {
        $grade = Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
            'value' => 4,
        ]);

        app(DeleteGradeService::class)->handle($grade);

        return $grade;
    }

    private function tokenForOwnerTeacher(SectionSubjectTeacher $sst): string
    {
        return $sst->teacher->user->createToken('api')->plainTextToken;
    }

    public function test_store_accepts_a_grade_for_a_cell_whose_grade_was_deleted(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $this->deleteGradeFor($column, $enrollment);

        $response = $this->withToken($this->tokenForOwnerTeacher($sst))->postJson(
            "/api/v1/grade-columns/{$column->id}/grades",
            ['enrollment_id' => $enrollment->id, 'value' => 8.5]
        );

        $response->assertCreated()
            ->assertJsonPath('data.enrollmentId', $enrollment->id)
            ->assertJsonPath('data.value', 8.5);
    }

    public function test_batch_accepts_a_grade_for_a_cell_whose_grade_was_deleted(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $this->deleteGradeFor($column, $enrollment);

        $response = $this->withToken($this->tokenForOwnerTeacher($sst))->postJson(
            "/api/v1/grade-columns/{$column->id}/grades/batch",
            ['grades' => [['enrollment_id' => $enrollment->id, 'value' => 8.5]]]
        );

        $response->assertOk()
            ->assertJsonPath('data.created', 1)
            ->assertJsonPath('data.updated', 0);
    }

    public function test_deleted_grade_is_preserved_as_history_after_regrading(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $deleted = $this->deleteGradeFor($column, $enrollment);

        $response = $this->withToken($this->tokenForOwnerTeacher($sst))->postJson(
            "/api/v1/grade-columns/{$column->id}/grades",
            ['enrollment_id' => $enrollment->id, 'value' => 8.5]
        );

        $newGradeId = $response->assertCreated()->json('data.id');

        $this->assertNotSame($deleted->id, $newGradeId);
        $this->assertNotNull(Grade::withTrashed()->find($deleted->id)->deleted_at);
        $this->assertNull(Grade::find($newGradeId)->deleted_at);
    }

    public function test_store_service_rejects_a_second_live_grade_for_the_same_cell(): void
    {
        [, $column, $enrollment] = $this->gradableGraph();
        Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
            'value' => 4,
        ]);

        // Calling the service directly is what a concurrent write does: it
        // reaches the index without the form request having ruled first.
        try {
            app(StoreGradeService::class)->handle($column, $enrollment, ['value' => 8.5]);

            $this->fail('Expected GradeAlreadyExistsException was not thrown.');
        } catch (GradeAlreadyExistsException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('GRADE_ALREADY_EXISTS', $e->errorCode());
            // The 409 names the concurrent write; the ordinary case is the 422
            // the form request answers with its own text.
            $this->assertSame('Otro usuario acaba de calificar a este estudiante en esta evaluación.', $e->getMessage());
        }

        $this->assertSame(1, Grade::where('enrollment_id', $enrollment->id)
            ->where('grade_column_id', $column->id)
            ->count());
    }
}
