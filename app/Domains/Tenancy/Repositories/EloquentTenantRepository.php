<?php

namespace App\Domains\Tenancy\Repositories;

use App\Domains\Tenancy\Models\Tenant;

class EloquentTenantRepository implements TenantRepository
{
    public function findBySlug(string $slug): ?Tenant
    {
        return Tenant::where('slug', $slug)->first();
    }
}
