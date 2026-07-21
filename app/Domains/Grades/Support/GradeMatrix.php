<?php

namespace App\Domains\Grades\Support;

use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use Illuminate\Support\Collection;

final class GradeMatrix
{
    private const AVERAGE_PRECISION = 2;

    /**
     * @param  array<string, array<string, Grade>>  $grades
     */
    private function __construct(private array $grades) {}

    /**
     * @param  Collection<int, Grade>  $grades
     */
    public static function fromGrades(Collection $grades): self
    {
        $indexed = [];

        foreach ($grades as $grade) {
            $indexed[$grade->enrollment_id][$grade->grade_column_id] = $grade;
        }

        return new self($indexed);
    }

    public function has(string $enrollmentId, string $columnId): bool
    {
        return isset($this->grades[$enrollmentId][$columnId]);
    }

    public function find(string $enrollmentId, string $columnId): ?Grade
    {
        return $this->grades[$enrollmentId][$columnId] ?? null;
    }

    /**
     * @param  Collection<int, GradeColumn>  $columns
     */
    public function weightedAverage(string $enrollmentId, Collection $columns): ?float
    {
        $weightedSum = 0.0;
        $totalWeight = 0.0;

        foreach ($columns as $column) {
            $grade = $this->find($enrollmentId, $column->id);

            if (! $grade) {
                continue;
            }

            $weight = (float) $column->weight;
            $weightedSum += (float) $grade->value * $weight;
            $totalWeight += $weight;
        }

        if ($totalWeight <= 0.0) {
            return null;
        }

        return round($weightedSum / $totalWeight, self::AVERAGE_PRECISION);
    }

    /**
     * @return array<string, array<string, Grade>>
     */
    public function toArray(): array
    {
        return $this->grades;
    }
}
