<?php

namespace Database\Factories;

use App\Domains\Tenancy\Enums\TenantPlan;
use App\Domains\Tenancy\Enums\TenantStatus;
use App\Domains\Tenancy\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => fake()->unique()->domainWord(),
            'plan' => TenantPlan::Free->value,
            'status' => TenantStatus::Active->value,
        ];
    }

    public function suspended(): static
    {
        return $this->state(['status' => TenantStatus::Suspended->value]);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => TenantStatus::Cancelled->value]);
    }
}
