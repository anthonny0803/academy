<?php

namespace Tests\Unit\Models;

use App\Domains\Tenancy\Enums\TenantStatus;
use App\Domains\Tenancy\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class TenantTest extends TestCase
{
    public function test_is_active_returns_true_for_active_status(): void
    {
        $tenant = new Tenant(['status' => TenantStatus::Active->value]);

        $this->assertTrue($tenant->isActive());
    }

    public function test_is_active_returns_false_for_non_active_status(): void
    {
        $tenant = new Tenant(['status' => TenantStatus::Suspended->value]);

        $this->assertFalse($tenant->isActive());
    }

    public function test_entity_name_is_organizacion(): void
    {
        $this->assertSame('Organización', (new Tenant)->getEntityName());
    }

    public function test_users_relationship_is_has_many(): void
    {
        $this->assertInstanceOf(HasMany::class, (new Tenant)->users());
    }
}
