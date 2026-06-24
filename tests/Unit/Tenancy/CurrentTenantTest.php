<?php

namespace Tests\Unit\Tenancy;

use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Support\CurrentTenant;
use Tests\TestCase;

class CurrentTenantTest extends TestCase
{
    public function test_starts_without_a_tenant(): void
    {
        $current = new CurrentTenant;

        $this->assertFalse($current->has());
        $this->assertNull($current->get());
        $this->assertNull($current->id());
    }

    public function test_set_stores_the_tenant(): void
    {
        $tenant = new Tenant;
        $tenant->id = 'tenant-uuid';

        $current = new CurrentTenant;
        $current->set($tenant);

        $this->assertTrue($current->has());
        $this->assertSame($tenant, $current->get());
        $this->assertSame('tenant-uuid', $current->id());
    }

    public function test_forget_clears_the_tenant(): void
    {
        $tenant = new Tenant;
        $tenant->id = 'tenant-uuid';

        $current = new CurrentTenant;
        $current->set($tenant);
        $current->forget();

        $this->assertFalse($current->has());
        $this->assertNull($current->get());
        $this->assertNull($current->id());
    }
}
