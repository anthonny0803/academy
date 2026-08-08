<?php

namespace App\Domains\Enrollments\Repositories;

use App\Domains\Enrollments\Models\Enrollment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface EnrollmentRepository
{
    public function create(array $attributes): Enrollment;

    public function update(Enrollment $enrollment, array $attributes): Enrollment;

    public function delete(Enrollment $enrollment): void;

    public function findOrFail(string $id): Enrollment;

    public function paginateForListing(string $search, ?string $status, ?string $academicPeriodId, ?string $sectionId, int $perPage = 6): LengthAwarePaginator;

    public function lockStudentEnrollments(string $studentId): void;

    public function hasActiveEnrollmentInPeriod(string $studentId, string $academicPeriodId): bool;

    public function studentIdsForActivePeriod(string $academicPeriodId): Collection;
}
