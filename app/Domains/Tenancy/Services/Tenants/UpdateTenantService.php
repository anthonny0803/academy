<?php

namespace App\Domains\Tenancy\Services\Tenants;

use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Repositories\TenantRepository;
use Illuminate\Support\Facades\DB;

class UpdateTenantService
{
    public function __construct(private TenantRepository $tenantRepository) {}

    public function handle(Tenant $tenant, array $data): Tenant
    {
        return DB::transaction(function () use ($tenant, $data) {
            return $this->tenantRepository->update($tenant, [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'plan' => $data['plan'],
                'status' => $data['status'],
            ])->fresh();
        });
    }
}
