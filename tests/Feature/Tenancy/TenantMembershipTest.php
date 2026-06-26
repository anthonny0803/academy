<?php

namespace Tests\Feature\Tenancy;

use App\Domains\Identity\Models\User;
use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Support\CurrentTenant;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\TenantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantMembershipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_factory_persists_an_active_tenant(): void
    {
        $tenant = Tenant::factory()->create();

        $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);
        $this->assertTrue($tenant->isActive());
    }

    public function test_user_belongs_to_a_tenant(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'tenant_id' => $this->tenant->id]);
        $this->assertTrue($user->fresh()->tenant->is($this->tenant));
        $this->assertTrue($this->tenant->users->contains($user));
    }

    public function test_seeders_assign_admin_to_demo_tenant(): void
    {
        app(CurrentTenant::class)->forget();

        $this->seed(TenantSeeder::class);
        $this->seed(UserSeeder::class);

        $tenant = Tenant::where('slug', TenantSeeder::DEMO_SLUG)->first();
        $admin = User::where('email', 'anthonny0803@gmail.com')->first();

        $this->assertNotNull($tenant);
        $this->assertTrue($admin->tenant->is($tenant));
    }
}
