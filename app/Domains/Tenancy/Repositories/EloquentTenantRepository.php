<?php

namespace App\Domains\Tenancy\Repositories;

use App\Domains\Tenancy\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentTenantRepository implements TenantRepository
{
    public function findBySlug(string $slug): ?Tenant
    {
        return Tenant::where('slug', $slug)->first();
    }

    public function findByPublicApiTokenHash(string $hash): ?Tenant
    {
        return Tenant::where('public_api_token_hash', $hash)->first();
    }

    public function create(array $attributes): Tenant
    {
        $tenant = new Tenant;
        $tenant->name = $attributes['name'];
        $tenant->slug = $attributes['slug'];
        $tenant->plan = $attributes['plan'];
        $tenant->status = $attributes['status'];
        $tenant->save();

        return $tenant;
    }

    public function update(Tenant $tenant, array $attributes): Tenant
    {
        $tenant->name = $attributes['name'];
        $tenant->slug = $attributes['slug'];
        $tenant->plan = $attributes['plan'];
        $tenant->status = $attributes['status'];
        $tenant->save();

        return $tenant;
    }

    public function updatePublicApiTokenHash(Tenant $tenant, string $hash): Tenant
    {
        $tenant->public_api_token_hash = $hash;
        $tenant->save();

        return $tenant;
    }

    public function delete(Tenant $tenant): void
    {
        $tenant->delete();
    }

    public function paginateForListing(string $search, ?string $status, int $perPage = 15): LengthAwarePaginator
    {
        return Tenant::query()
            ->when($search !== '', fn ($query) => $query->search($search))
            ->when(! is_null($status), fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate($perPage);
    }
}
