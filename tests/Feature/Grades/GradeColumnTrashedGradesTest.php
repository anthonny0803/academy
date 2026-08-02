<?php

namespace Tests\Feature\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Exceptions\GradeColumnHasGradesException;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Services\GradeColumns\DeleteGradeColumnService;
use App\Domains\Grades\Services\Grades\DeleteGradeService;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CountsQueries;
use Tests\TestCase;

/**
 * A grade column is deleted physically, and `grades.grade_column_id` is
 * `onDelete('restrict')`. The soft delete of a grade therefore does not free
 * the column: these tests fix that the guards see the trashed history the
 * foreign key sees, so the answer is a domain error and never a 23503.
 */
class GradeColumnTrashedGradesTest extends TestCase
{
    use CountsQueries;
    use RefreshDatabase;

    private const DELETE_DENIED = 'No puedes eliminar una evaluación con notas en su historial, incluidas las eliminadas.';

    private const UPDATE_DENIED = 'No puedes modificar una evaluación con notas en su historial, incluidas las eliminadas.';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function enrollmentFor(SectionSubjectTeacher $sst): Enrollment
    {
        return Student::factory()
            ->inSection($sst->section)
            ->create()
            ->enrollments()
            ->where('section_id', $sst->section_id)
            ->first();
    }

    private function columnWithDeletedGrade(SectionSubjectTeacher $sst, Enrollment $enrollment, int $weight): GradeColumn
    {
        $column = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => $weight,
        ]);

        $grade = Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);

        app(DeleteGradeService::class)->handle($grade);

        return $column;
    }

    public function test_the_delete_service_rejects_a_column_whose_only_grade_is_deleted(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = $this->columnWithDeletedGrade($sst, $this->enrollmentFor($sst), 100);

        try {
            app(DeleteGradeColumnService::class)->handle($column);

            $this->fail('Expected GradeColumnHasGradesException was not thrown.');
        } catch (GradeColumnHasGradesException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('GRADE_COLUMN_HAS_GRADES', $e->errorCode());
            $this->assertSame(
                'No se puede eliminar esta evaluación porque tiene notas en su historial, incluidas las eliminadas.',
                $e->getMessage()
            );
        }

        $this->assertDatabaseHas('grade_columns', ['id' => $column->id]);
    }

    public function test_the_delete_endpoint_denies_a_column_whose_only_grade_is_deleted(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = $this->columnWithDeletedGrade($sst, $this->enrollmentFor($sst), 100);

        $response = $this->actingAs($sst->teacher->user)
            ->delete(route('grade-columns.destroy', $column));

        $response->assertRedirect();
        $response->assertSessionHas('error', self::DELETE_DENIED);
        $this->assertDatabaseHas('grade_columns', ['id' => $column->id]);
    }

    public function test_the_update_endpoint_denies_a_column_whose_only_grade_is_deleted(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = $this->columnWithDeletedGrade($sst, $this->enrollmentFor($sst), 100);

        $response = $this->actingAs($sst->teacher->user)->put(
            route('grade-columns.update', $column),
            ['name' => 'EXAMEN FINAL', 'weight' => 50]
        );

        $response->assertRedirect();
        $response->assertSessionHas('error', self::UPDATE_DENIED);
        $this->assertDatabaseHas('grade_columns', [
            'id' => $column->id,
            'name' => $column->name,
        ]);
    }

    public function test_the_index_sees_the_deleted_grades_with_a_single_query(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $enrollment = $this->enrollmentFor($sst);

        foreach ([30, 30, 40] as $weight) {
            $this->columnWithDeletedGrade($sst, $enrollment, $weight);
        }

        $user = $sst->teacher->user;

        $queries = $this->recordQueries(function () use ($user, $sst) {
            $this->actingAs($user)
                ->get(route('grade-columns.index', $sst))
                ->assertOk()
                ->assertSeeText('Con notas');
        });

        $gradeQueries = array_filter(
            $queries,
            fn (array $query): bool => str_contains($query['query'], '"grades"')
        );

        $this->assertCount(1, $gradeQueries);
    }
}
