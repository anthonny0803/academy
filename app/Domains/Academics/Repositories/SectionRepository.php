<?php

namespace App\Domains\Academics\Repositories;

use App\Domains\Academics\Models\Section;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SectionRepository
{
    public function create(array $attributes): Section;

    public function update(Section $section, array $attributes): Section;

    public function delete(Section $section): void;

    public function find(string $id): ?Section;

    public function paginateForListing(string $search, ?bool $isActive, ?string $academicPeriodId, int $perPage = 6): LengthAwarePaginator;

    public function activeForPeriodExcept(string $academicPeriodId, string $exceptSectionId): Collection;
}
