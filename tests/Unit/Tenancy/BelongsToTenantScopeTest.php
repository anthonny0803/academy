<?php

namespace Tests\Unit\Tenancy;

use App\Domains\Students\Models\Student;
use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Support\CurrentTenant;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BelongsToTenantScopeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        app(CurrentTenant::class)->forget();
    }

    public function test_query_is_not_scoped_without_a_resolved_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $this->withinTenant($tenantA, fn () => Student::factory()->count(2)->create());

        $tenantB = Tenant::factory()->create();
        $this->withinTenant($tenantB, fn () => Student::factory()->create());

        $this->assertCount(3, Student::all());
    }

    public function test_query_is_scoped_to_the_resolved_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $this->withinTenant($tenantA, fn () => Student::factory()->count(2)->create());

        $tenantB = Tenant::factory()->create();
        app(CurrentTenant::class)->set($tenantB);
        Student::factory()->create();

        $this->assertCount(1, Student::all());
    }

    public function test_tenant_id_is_auto_assigned_on_create_when_resolved(): void
    {
        $tenant = Tenant::factory()->create();
        app(CurrentTenant::class)->set($tenant);

        $student = Student::factory()->create();

        $this->assertSame($tenant->id, $student->tenant_id);
    }

    public function test_create_requires_a_resolved_tenant(): void
    {
        $this->expectException(QueryException::class);

        Student::factory()->create();
    }
}
