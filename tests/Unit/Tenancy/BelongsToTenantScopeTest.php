<?php

namespace Tests\Unit\Tenancy;

use App\Domains\Students\Models\Student;
use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Support\CurrentTenant;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BelongsToTenantScopeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_query_is_not_scoped_without_a_resolved_tenant(): void
    {
        Student::factory()->count(2)->create();

        $this->assertCount(2, Student::all());
    }

    public function test_query_is_scoped_to_the_resolved_tenant(): void
    {
        Student::factory()->count(2)->create();

        $tenant = Tenant::factory()->create();
        app(CurrentTenant::class)->set($tenant);
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

    public function test_tenant_id_stays_null_on_create_without_a_resolved_tenant(): void
    {
        $student = Student::factory()->create();

        $this->assertNull($student->tenant_id);
    }
}
