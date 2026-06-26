<?php

namespace Tests;

use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Support\CurrentTenant;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected ?Tenant $tenant = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (! $this->usesRefreshDatabase()) {
            return;
        }

        $this->tenant = Tenant::factory()->create();
        app(CurrentTenant::class)->set($this->tenant);
    }

    protected function tearDown(): void
    {
        if ($this->usesRefreshDatabase()) {
            app(CurrentTenant::class)->forget();
        }

        parent::tearDown();
    }

    /**
     * Resolve a different tenant for the duration of the callback, restoring
     * the previous one afterwards. Lets isolation tests seed a second tenant.
     */
    protected function withinTenant(Tenant $tenant, Closure $callback): mixed
    {
        $currentTenant = app(CurrentTenant::class);
        $previous = $currentTenant->get();
        $currentTenant->set($tenant);

        try {
            return $callback();
        } finally {
            $previous === null ? $currentTenant->forget() : $currentTenant->set($previous);
        }
    }

    private function usesRefreshDatabase(): bool
    {
        return in_array(RefreshDatabase::class, class_uses_recursive($this), true);
    }
}
