<?php

namespace App\Domains\Academics\Services\AcademicPeriods;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Enrollments\Enums\EnrollmentStatus;

class AcademicPeriodStatsService
{
    public function handle(AcademicPeriod $academicPeriod): array
    {
        $academicPeriod->load([
            'sections' => fn ($q) => $q->withCount([
                'enrollments',
                'enrollments as active_enrollments_count' => fn ($q) => $q->where('status', EnrollmentStatus::Active->value),
                'enrollments as completed_enrollments_count' => fn ($q) => $q->where('status', EnrollmentStatus::Completed->value),
            ]),
        ]);

        return [
            'total_sections' => $academicPeriod->sections->count(),
            'active_sections' => $academicPeriod->sections->where('is_active', true)->count(),
            'total_enrollments' => $academicPeriod->sections->sum('enrollments_count'),
            'active_enrollments' => $academicPeriod->sections->sum('active_enrollments_count'),
            'completed_enrollments' => $academicPeriod->sections->sum('completed_enrollments_count'),
        ];
    }
}
