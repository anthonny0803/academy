<?php

namespace Tests\Unit\Support;

use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Support\GradeMatrix;
use Illuminate\Support\Collection;
use Tests\TestCase;

class GradeMatrixTest extends TestCase
{
    private const ENROLLMENT_ID = 'enrollment-1';

    public function test_finds_a_grade_by_enrollment_and_column(): void
    {
        $matrix = GradeMatrix::fromGrades(collect([
            $this->grade('column-1', 8),
        ]));

        $this->assertTrue($matrix->has(self::ENROLLMENT_ID, 'column-1'));
        $this->assertSame('8.00', $matrix->find(self::ENROLLMENT_ID, 'column-1')->value);
    }

    public function test_reports_missing_grades(): void
    {
        $matrix = GradeMatrix::fromGrades(collect([
            $this->grade('column-1', 8),
        ]));

        $this->assertFalse($matrix->has(self::ENROLLMENT_ID, 'column-2'));
        $this->assertFalse($matrix->has('other-enrollment', 'column-1'));
        $this->assertNull($matrix->find(self::ENROLLMENT_ID, 'column-2'));
    }

    public function test_weighted_average_applies_column_weights(): void
    {
        $matrix = GradeMatrix::fromGrades(collect([
            $this->grade('column-1', 10),
            $this->grade('column-2', 5),
        ]));

        $columns = $this->columns(['column-1' => 40, 'column-2' => 60]);

        $this->assertSame(7.0, $matrix->weightedAverage(self::ENROLLMENT_ID, $columns));
    }

    public function test_weighted_average_only_counts_graded_columns(): void
    {
        $matrix = GradeMatrix::fromGrades(collect([
            $this->grade('column-1', 10),
        ]));

        $columns = $this->columns(['column-1' => 40, 'column-2' => 60]);

        $this->assertSame(10.0, $matrix->weightedAverage(self::ENROLLMENT_ID, $columns));
    }

    public function test_weighted_average_is_null_without_grades(): void
    {
        $matrix = GradeMatrix::fromGrades(collect());

        $columns = $this->columns(['column-1' => 100]);

        $this->assertNull($matrix->weightedAverage(self::ENROLLMENT_ID, $columns));
    }

    public function test_weighted_average_is_null_when_total_weight_is_zero(): void
    {
        $matrix = GradeMatrix::fromGrades(collect([
            $this->grade('column-1', 10),
        ]));

        $columns = $this->columns(['column-1' => 0]);

        $this->assertNull($matrix->weightedAverage(self::ENROLLMENT_ID, $columns));
    }

    public function test_to_array_indexes_grades_by_enrollment_and_column(): void
    {
        $matrix = GradeMatrix::fromGrades(collect([
            $this->grade('column-1', 8),
            $this->grade('column-2', 9),
        ]));

        $indexed = $matrix->toArray();

        $this->assertSame(
            ['column-1', 'column-2'],
            array_keys($indexed[self::ENROLLMENT_ID])
        );
    }

    private function grade(string $columnId, float $value): Grade
    {
        return new Grade([
            'enrollment_id' => self::ENROLLMENT_ID,
            'grade_column_id' => $columnId,
            'value' => $value,
        ]);
    }

    /**
     * @param  array<string, float>  $weightsById
     * @return Collection<int, GradeColumn>
     */
    private function columns(array $weightsById): Collection
    {
        return collect($weightsById)->map(function (float $weight, string $id) {
            $column = new GradeColumn(['weight' => $weight]);
            $column->id = $id;

            return $column;
        })->values();
    }
}
