<?php

namespace App\Domains\Tenancy\Services\Tenants;

use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Repositories\TenantRepository;
use Illuminate\Support\Facades\DB;

class DeleteTenantService
{
    public function __construct(private TenantRepository $tenantRepository) {}

    public function handle(Tenant $tenant): void
    {
        DB::transaction(function () use ($tenant) {
            $this->tenantRepository->delete($tenant);
        });
    }
}
