<?php

namespace App\Domains\Academics\Repositories;

use App\Domains\Academics\Models\AcademicPeriod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AcademicPeriodRepository
{
    public function create(array $attributes): AcademicPeriod;

    public function update(AcademicPeriod $academicPeriod, array $attributes): AcademicPeriod;

    public function delete(AcademicPeriod $academicPeriod): void;

    public function paginateForListing(string $search, ?bool $isActive, int $perPage = 6): LengthAwarePaginator;

    public function activeOrderedByStartDate(): Collection;

    public function activeWithActiveSections(): Collection;
}
