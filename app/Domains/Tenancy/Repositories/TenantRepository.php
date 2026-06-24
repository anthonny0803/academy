<?php

namespace App\Domains\Tenancy\Repositories;

use App\Domains\Tenancy\Models\Tenant;

interface TenantRepository
{
    public function findBySlug(string $slug): ?Tenant;
}
