<?php

namespace Tests\Unit\Repositories;

use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Repositories\EloquentTenantRepository;
use App\Domains\Tenancy\Repositories\TenantRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentTenantRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TenantRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(TenantRepository::class);
    }

    public function test_resolved_implementation_is_eloquent(): void
    {
        $this->assertInstanceOf(EloquentTenantRepository::class, $this->repository);
    }

    public function test_find_by_slug_returns_the_matching_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'academy-demo']);

        $found = $this->repository->findBySlug('academy-demo');

        $this->assertNotNull($found);
        $this->assertTrue($found->is($tenant));
    }

    public function test_find_by_slug_returns_null_when_missing(): void
    {
        $this->assertNull($this->repository->findBySlug('does-not-exist'));
    }
}
