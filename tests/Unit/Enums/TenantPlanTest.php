<?php

namespace Tests\Unit\Enums;

use App\Domains\Tenancy\Enums\TenantPlan;
use PHPUnit\Framework\TestCase;

class TenantPlanTest extends TestCase
{
    public function test_cases_expose_expected_values(): void
    {
        $this->assertSame('free', TenantPlan::Free->value);
        $this->assertSame('pro', TenantPlan::Pro->value);
        $this->assertSame('enterprise', TenantPlan::Enterprise->value);
    }

    public function test_to_array_returns_all_values(): void
    {
        $this->assertSame(
            ['free', 'pro', 'enterprise'],
            TenantPlan::toArray()
        );
    }
}
