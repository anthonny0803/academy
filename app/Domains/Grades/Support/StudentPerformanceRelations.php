<?php

namespace App\Domains\Grades\Support;

use Closure;

final class StudentPerformanceRelations
{
    /**
     * Relation tree consumed by StudentPerformanceSummaryService, declared
     * relative to Student. Callers rooted elsewhere prefix it.
     *
     * @return array<int|string, string|Closure>
     */
    public static function forStudent(): array
    {
        return [
            'user',
            'enrollments.section.academicPeriod',
            'enrollments.section.sectionSubjectTeachers' => self::gradedSectionSubjectTeachers(),
            'enrollments.grades.gradeColumn',
        ];
    }

    /**
     * @return array<int|string, string|Closure>
     */
    public static function prefixed(string $prefix): array
    {
        $prefixed = [];

        foreach (self::forStudent() as $relation => $constraint) {
            if (is_int($relation)) {
                $prefixed[] = "{$prefix}.{$constraint}";

                continue;
            }

            $prefixed["{$prefix}.{$relation}"] = $constraint;
        }

        return $prefixed;
    }

    private static function gradedSectionSubjectTeachers(): Closure
    {
        return fn ($query) => $query->with([
            'subject',
            'teacher.user',
            'gradeColumns' => fn ($query) => $query->orderBy('display_order'),
        ]);
    }
}
