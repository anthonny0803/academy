<?php

namespace App\Domains\Tenancy\Repositories;

use App\Domains\Tenancy\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TenantRepository
{
    public function findBySlug(string $slug): ?Tenant;

    public function create(array $attributes): Tenant;

    public function update(Tenant $tenant, array $attributes): Tenant;

    public function delete(Tenant $tenant): void;

    public function paginateForListing(string $search, ?string $status, int $perPage = 15): LengthAwarePaginator;
}
