<?php

namespace Database\Seeders;

use App\Domains\Tenancy\Enums\TenantPlan;
use App\Domains\Tenancy\Enums\TenantStatus;
use App\Domains\Tenancy\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public const DEMO_SLUG = 'academy-demo';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Tenant::where('slug', self::DEMO_SLUG)->exists()) {
            return;
        }

        $tenant = new Tenant;
        $tenant->name = 'Academy Demo';
        $tenant->slug = self::DEMO_SLUG;
        $tenant->plan = TenantPlan::Free->value;
        $tenant->status = TenantStatus::Active->value;
        $tenant->save();
    }
}
