<?php

namespace App\Domains\Enrollments\Repositories;

use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentEnrollmentRepository implements EnrollmentRepository
{
    public function create(array $attributes): Enrollment
    {
        return Enrollment::create($attributes);
    }

    public function update(Enrollment $enrollment, array $attributes): Enrollment
    {
        $enrollment->update($attributes);

        return $enrollment;
    }

    public function delete(Enrollment $enrollment): void
    {
        $enrollment->delete();
    }

    public function findOrFail(string $id): Enrollment
    {
        return Enrollment::findOrFail($id);
    }

    public function paginateForListing(string $search, ?string $status, ?string $academicPeriodId, ?string $sectionId, int $perPage = 6): LengthAwarePaginator
    {
        return Enrollment::query()
            ->with(['student.user', 'section.academicPeriod'])
            ->when($search !== '', fn ($query) => $query->search($search))
            ->when($status, fn ($query) => $query->byStatus($status))
            ->when($academicPeriodId, fn ($query) => $query->whereHas('section', fn ($q) => $q->where('academic_period_id', $academicPeriodId)))
            ->when($sectionId, fn ($query) => $query->forSection($sectionId))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function hasActiveEnrollmentInPeriod(string $studentId, string $academicPeriodId): bool
    {
        return Enrollment::where('student_id', $studentId)
            ->where('status', EnrollmentStatus::Active->value)
            ->whereHas('section', fn ($query) => $query->where('academic_period_id', $academicPeriodId))
            ->exists();
    }

    public function studentIdsForActivePeriod(string $academicPeriodId): Collection
    {
        return Enrollment::whereHas('section', fn ($query) => $query->where('academic_period_id', $academicPeriodId))
            ->where('status', EnrollmentStatus::Active->value)
            ->pluck('student_id');
    }
}
