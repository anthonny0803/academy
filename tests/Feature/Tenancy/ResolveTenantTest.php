<?php

namespace Tests\Feature\Tenancy;

use App\Domains\Identity\Models\User;
use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Support\CurrentTenant;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ResolveTenantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        config(['tenancy.central_domains' => ['localhost']]);

        Route::middleware('tenant.resolve')->get('/_test/current-tenant', fn () => response()->json([
            'tenantId' => app(CurrentTenant::class)->id(),
        ]));

        app(CurrentTenant::class)->forget();
    }

    public function test_resolves_tenant_from_authenticated_user(): void
    {
        $tenant = Tenant::factory()->create();
        $user = $this->withinTenant($tenant, fn () => User::factory()->create());

        $this->actingAs($user)
            ->getJson('/_test/current-tenant')
            ->assertOk()
            ->assertJson(['tenantId' => $tenant->id]);
    }

    public function test_resolves_tenant_from_subdomain(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'academy-demo']);

        $this->getJson('http://academy-demo.localhost/_test/current-tenant')
            ->assertOk()
            ->assertJson(['tenantId' => $tenant->id]);
    }

    public function test_resolves_no_tenant_on_central_domain(): void
    {
        $this->getJson('http://localhost/_test/current-tenant')
            ->assertOk()
            ->assertJson(['tenantId' => null]);
    }

    public function test_authenticated_user_tenant_is_authoritative_over_subdomain(): void
    {
        $userTenant = Tenant::factory()->create(['slug' => 'real-school']);
        Tenant::factory()->create(['slug' => 'academy-demo']);
        $user = $this->withinTenant($userTenant, fn () => User::factory()->create());

        $this->actingAs($user)
            ->getJson('http://academy-demo.localhost/_test/current-tenant')
            ->assertOk()
            ->assertJson(['tenantId' => $userTenant->id]);
    }
}
