<?php

namespace App\Domains\Representatives\Repositories;

use App\Domains\Representatives\Models\Representative;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface RepresentativeRepository
{
    public function create(array $attributes): Representative;

    public function firstOrCreateForUser(string $userId): Representative;

    public function paginateForListing(string $search, ?bool $isActive, ?bool $hasStudents, int $perPage = 6): LengthAwarePaginator;

    public function paginateForSearch(string $search, int $perPage = 5): LengthAwarePaginator;

    public function idsToActivate(Collection $ids): Collection;

    public function idsToDeactivate(Collection $ids): Collection;

    public function updateActiveStatus(Collection $ids, bool $isActive): void;
}
