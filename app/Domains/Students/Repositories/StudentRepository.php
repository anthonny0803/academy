<?php

namespace App\Domains\Students\Repositories;

use App\Domains\Students\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface StudentRepository
{
    public function create(array $attributes): Student;

    public function update(Student $student, array $attributes): Student;

    public function lockCodeSequence(string $prefix): void;

    public function lastCodeForPrefix(string $prefix): ?string;

    public function paginateForListing(string $search, ?bool $isActive, ?string $academicPeriodId, ?string $sectionId, int $perPage = 6): LengthAwarePaginator;

    public function representativeIdsFor(array $studentIds): Collection;

    public function deactivateWithoutActiveEnrollments(array $studentIds): int;
}
