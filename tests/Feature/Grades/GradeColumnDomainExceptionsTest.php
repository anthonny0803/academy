<?php

namespace Tests\Feature\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Grades\Exceptions\GradeColumnHasGradesException;
use App\Domains\Grades\Exceptions\GradeColumnWeightExceededException;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Services\GradeColumns\DeleteGradeColumnService;
use App\Domains\Grades\Services\GradeColumns\StoreGradeColumnService;
use App\Domains\Grades\Services\GradeColumns\UpdateGradeColumnService;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeColumnDomainExceptionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function columnFor(SectionSubjectTeacher $sst, int $weight): GradeColumn
    {
        return GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => $weight,
        ]);
    }

    public function test_store_throws_when_the_weight_exceeds_the_remaining_budget(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $this->columnFor($sst, 60);

        try {
            app(StoreGradeColumnService::class)->handle($sst, ['name' => 'Examen final', 'weight' => 50]);

            $this->fail('Expected GradeColumnWeightExceededException was not thrown.');
        } catch (GradeColumnWeightExceededException $e) {
            $this->assertSame(422, $e->statusCode());
            $this->assertSame('GRADE_COLUMN_WEIGHT_EXCEEDED', $e->errorCode());
            $this->assertSame(
                'No se puede agregar esta evaluación. Peso restante disponible: 40%',
                $e->getMessage()
            );
        }

        $this->assertSame(1, GradeColumn::count());
    }

    public function test_update_throws_when_the_new_weight_exceeds_the_total(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $this->columnFor($sst, 60);
        $column = $this->columnFor($sst, 40);

        try {
            app(UpdateGradeColumnService::class)->handle($column, ['name' => $column->name, 'weight' => 50]);

            $this->fail('Expected GradeColumnWeightExceededException was not thrown.');
        } catch (GradeColumnWeightExceededException $e) {
            $this->assertSame(422, $e->statusCode());
            $this->assertSame('GRADE_COLUMN_WEIGHT_EXCEEDED', $e->errorCode());
            $this->assertSame(
                'El peso total excedería el 100%. Máximo permitido para esta evaluación: 40%',
                $e->getMessage()
            );
        }

        $this->assertSame(40.0, (float) $column->fresh()->weight);
    }

    public function test_delete_throws_when_the_column_has_grades(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = $this->columnFor($sst, 100);
        $student = Student::factory()->inSection($sst->section)->create();
        $enrollment = $student->enrollments()->where('section_id', $sst->section_id)->first();
        Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);

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

        $this->assertSame(1, GradeColumn::count());
    }
}
