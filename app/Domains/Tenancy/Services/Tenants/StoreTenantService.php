<?php

namespace App\Domains\Tenancy\Services\Tenants;

use App\Domains\Tenancy\Enums\TenantPlan;
use App\Domains\Tenancy\Enums\TenantStatus;
use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Repositories\TenantRepository;
use Illuminate\Support\Facades\DB;

class StoreTenantService
{
    public function __construct(private TenantRepository $tenantRepository) {}

    public function handle(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            return $this->tenantRepository->create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'plan' => $data['plan'] ?? TenantPlan::Free->value,
                'status' => $data['status'] ?? TenantStatus::Active->value,
            ]);
        });
    }
}
