<?php

namespace Tests\Unit\Models;

use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use PHPUnit\Framework\TestCase;

class GradeTest extends TestCase
{
    private function makeGradeWithColumn(float $value, float $weight): Grade
    {
        $column = new GradeColumn;
        $column->weight = $weight;

        $grade = new Grade;
        $grade->value = $value;
        $grade->setRelation('gradeColumn', $column);

        return $grade;
    }

    public function test_get_weight_returns_column_weight(): void
    {
        $grade = $this->makeGradeWithColumn(8.5, 40);

        $this->assertSame(40.0, $grade->getWeight());
    }

    public function test_observation_mutator_uppercases_and_trims(): void
    {
        $grade = new Grade;
        $grade->observation = '  falta justificada  ';

        $this->assertSame('FALTA JUSTIFICADA', $grade->observation);
    }

    public function test_observation_null_stays_null(): void
    {
        $grade = new Grade;
        $grade->observation = null;

        $this->assertNull($grade->observation);
    }
}
