<?php

namespace Tests\Unit\Models;

use App\Domains\Tenancy\Models\Tenant;
use Tests\TestCase;

class TenantMutatorsTest extends TestCase
{
    public function test_slug_is_lowercased_and_url_safe(): void
    {
        $tenant = new Tenant;
        $tenant->slug = 'Academy Demo';

        $this->assertSame('academy-demo', $tenant->slug);
    }

    public function test_slug_strips_accents(): void
    {
        $tenant = new Tenant;
        $tenant->slug = 'Colegio Bolívar';

        $this->assertSame('colegio-bolivar', $tenant->slug);
    }

    public function test_slug_is_idempotent(): void
    {
        $tenant = new Tenant;
        $tenant->slug = 'academy-demo';

        $this->assertSame('academy-demo', $tenant->slug);
    }
}
