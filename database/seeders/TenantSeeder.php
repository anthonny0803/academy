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
        Tenant::firstOrCreate(
            ['slug' => self::DEMO_SLUG],
            [
                'name' => 'Academy Demo',
                'plan' => TenantPlan::Free->value,
                'status' => TenantStatus::Active->value,
            ]
        );
    }
}
