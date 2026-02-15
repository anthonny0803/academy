<?php

namespace Tests\Unit\Models;

use App\Models\Grade;
use App\Models\GradeColumn;
use PHPUnit\Framework\TestCase;

class GradeTest extends TestCase
{
    private function makeGradeWithColumn(float $value, float $weight): Grade
    {
        $column = new GradeColumn();
        $column->weight = $weight;

        $grade = new Grade();
        $grade->value = $value;
        $grade->setRelation('gradeColumn', $column);

        return $grade;
    }

    public function test_get_weight_returns_column_weight(): void
    {
        $grade = $this->makeGradeWithColumn(8.5, 40);

        $this->assertSame(40.0, $grade->getWeight());
    }

    public function test_weighted_value_calculation(): void
    {
        // Nota 8 con peso 40% => (8 * 40) / 100 = 3.2
        $grade = $this->makeGradeWithColumn(8, 40);

        $this->assertSame(3.2, $grade->getWeightedValue());
    }

    public function test_weighted_value_with_full_weight(): void
    {
        // Nota 7.5 con peso 100% => (7.5 * 100) / 100 = 7.5
        $grade = $this->makeGradeWithColumn(7.5, 100);

        $this->assertSame(7.5, $grade->getWeightedValue());
    }

    public function test_weighted_value_with_zero_value(): void
    {
        // Nota 0 con peso 30% => 0
        $grade = $this->makeGradeWithColumn(0, 30);

        $this->assertSame(0.0, $grade->getWeightedValue());
    }

    public function test_weighted_value_with_decimal_precision(): void
    {
        // Nota 9.75 con peso 25% => (9.75 * 25) / 100 = 2.4375
        $grade = $this->makeGradeWithColumn(9.75, 25);

        $this->assertEqualsWithDelta(2.4375, $grade->getWeightedValue(), 0.0001);
    }

    public function test_multiple_weighted_values_sum_to_final_grade(): void
    {
        // Simular: Examen parcial (40%) = 7, Examen final (60%) = 9
        // Esperado: (7*40/100) + (9*60/100) = 2.8 + 5.4 = 8.2
        $parcial = $this->makeGradeWithColumn(7, 40);
        $final = $this->makeGradeWithColumn(9, 60);

        $total = $parcial->getWeightedValue() + $final->getWeightedValue();

        $this->assertEqualsWithDelta(8.2, $total, 0.0001);
    }

    public function test_observation_mutator_uppercases_and_trims(): void
    {
        $grade = new Grade();
        $grade->observation = '  falta justificada  ';

        $this->assertSame('FALTA JUSTIFICADA', $grade->observation);
    }

    public function test_observation_null_stays_null(): void
    {
        $grade = new Grade();
        $grade->observation = null;

        $this->assertNull($grade->observation);
    }
}
