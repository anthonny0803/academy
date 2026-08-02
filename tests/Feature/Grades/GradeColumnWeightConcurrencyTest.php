<?php

namespace Tests\Feature\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Grades\Exceptions\GradeColumnWeightExceededException;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Services\GradeColumns\StoreGradeColumnService;
use App\Domains\Grades\Services\GradeColumns\UpdateGradeColumnService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CountsQueries;
use Tests\TestCase;

class GradeColumnWeightConcurrencyTest extends TestCase
{
    use CountsQueries;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_store_sums_the_stored_columns_not_the_loaded_ones(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();

        // A caller that loaded the assignment while it had no columns: the web
        // controller hands over exactly this, and getTotalWeight() prefers the
        // loaded relation over the database.
        $sst->load('gradeColumns');
        $this->columnFor($sst, 60);

        try {
            app(StoreGradeColumnService::class)->handle($sst, ['name' => 'Examen final', 'weight' => 50]);

            $this->fail('Expected GradeColumnWeightExceededException was not thrown.');
        } catch (GradeColumnWeightExceededException $e) {
            $this->assertSame('No se puede agregar esta evaluación. Peso restante disponible: 40%', $e->getMessage());
        }

        $this->assertSame(1, GradeColumn::count());
    }

    public function test_update_subtracts_the_stored_weight_not_the_loaded_one(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $this->columnFor($sst, 50);
        $column = $this->columnFor($sst, 50);

        // Someone lowered this column to 10 after it was loaded. Subtracting the
        // stale 50 from a total that already counts 10 frees weight twice.
        GradeColumn::query()->whereKey($column->id)->update(['weight' => 10]);

        try {
            app(UpdateGradeColumnService::class)->handle($column, ['name' => $column->name, 'weight' => 60]);

            $this->fail('Expected GradeColumnWeightExceededException was not thrown.');
        } catch (GradeColumnWeightExceededException $e) {
            $this->assertSame('El peso total excedería el 100%. Máximo permitido para esta evaluación: 50%', $e->getMessage());
        }

        $this->assertSame(10.0, (float) $column->fresh()->weight);
    }

    public function test_store_locks_the_assignment_row(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();

        $queries = $this->recordQueries(
            fn () => app(StoreGradeColumnService::class)->handle($sst, ['name' => 'Examen final', 'weight' => 50])
        );

        $this->assertTrue($this->locksTheAssignment($queries));
    }

    public function test_update_locks_the_assignment_row(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = $this->columnFor($sst, 50);

        $queries = $this->recordQueries(
            fn () => app(UpdateGradeColumnService::class)->handle($column, ['name' => $column->name, 'weight' => 60])
        );

        $this->assertTrue($this->locksTheAssignment($queries));
    }

    private function columnFor(SectionSubjectTeacher $sst, int $weight): GradeColumn
    {
        return GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => $weight,
        ]);
    }

    /**
     * @param  list<array{query: string}>  $queries
     */
    private function locksTheAssignment(array $queries): bool
    {
        foreach (array_column($queries, 'query') as $query) {
            if (str_contains($query, 'section_subject_teacher') && str_contains($query, 'for update')) {
                return true;
            }
        }

        return false;
    }
}
