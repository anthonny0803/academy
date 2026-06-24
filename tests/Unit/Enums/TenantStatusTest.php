<?php

namespace Tests\Unit\Enums;

use App\Domains\Tenancy\Enums\TenantStatus;
use PHPUnit\Framework\TestCase;

class TenantStatusTest extends TestCase
{
    public function test_cases_expose_expected_values(): void
    {
        $this->assertSame('activo', TenantStatus::Active->value);
        $this->assertSame('suspendido', TenantStatus::Suspended->value);
        $this->assertSame('cancelado', TenantStatus::Cancelled->value);
    }

    public function test_to_array_returns_all_values(): void
    {
        $this->assertSame(
            ['activo', 'suspendido', 'cancelado'],
            TenantStatus::toArray()
        );
    }
}
