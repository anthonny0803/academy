<?php

namespace Tests\Feature\Tenancy;

use App\Domains\Academics\Models\Section;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Models\Grade;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Enums\RelationshipType;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Shared\Enums\Sex;
use App\Domains\Students\Models\Student;
use App\Domains\Tenancy\Models\Tenant;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function tokenFor(User $user): string
    {
        return $user->createToken('api')->plainTextToken;
    }

    public function test_index_only_returns_current_tenant_records(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        Student::factory()->count(2)->create();

        $otherTenant = Tenant::factory()->create();
        $this->withinTenant($otherTenant, fn () => Student::factory()->count(3)->create());

        $this->withToken($token)
            ->getJson('/api/v1/students')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2);
    }

    public function test_show_of_another_tenant_record_returns_404(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $otherTenant = Tenant::factory()->create();
        $foreignStudent = $this->withinTenant($otherTenant, fn () => Student::factory()->create());

        $this->withToken($token)
            ->getJson("/api/v1/students/{$foreignStudent->id}")
            ->assertNotFound()
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_update_of_another_tenant_record_returns_404(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $otherTenant = Tenant::factory()->create();
        $foreignStudent = $this->withinTenant($otherTenant, fn () => Student::factory()->create());

        $this->withToken($token)
            ->putJson("/api/v1/students/{$foreignStudent->id}", ['is_active' => false])
            ->assertNotFound();
    }

    public function test_isolation_holds_for_a_second_aggregate(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $ownEnrollment = Enrollment::factory()->create();

        $otherTenant = Tenant::factory()->create();
        $foreignEnrollment = $this->withinTenant($otherTenant, fn () => Enrollment::factory()->create());

        $returnedIds = array_column(
            $this->withToken($token)->getJson('/api/v1/enrollments')->assertOk()->json('data'),
            'id'
        );

        $this->assertContains($ownEnrollment->id, $returnedIds);
        $this->assertNotContains($foreignEnrollment->id, $returnedIds);
    }

    public function test_store_assigns_the_current_tenant_to_new_records(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $representative = Representative::factory()->create();
        $section = Section::factory()->create();

        $this->withToken($token)->postJson(
            "/api/v1/representatives/{$representative->id}/students",
            $this->validPayload(['section_id' => $section->id])
        )->assertCreated();

        $this->assertDatabaseHas('users', [
            'email' => 'pedro.gomez@example.com',
            'tenant_id' => $this->tenant->id,
        ]);
        $this->assertDatabaseHas('students', [
            'representative_id' => $representative->id,
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_model_queries_are_isolated_in_both_directions(): void
    {
        Grade::factory()->count(2)->create();

        $otherTenant = Tenant::factory()->create();
        $this->withinTenant($otherTenant, fn () => Grade::factory()->count(3)->create());

        $this->assertCount(2, Grade::all());
        $this->withinTenant($otherTenant, fn () => $this->assertCount(3, Grade::all()));
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Pedro',
            'last_name' => 'Gomez',
            'email' => 'pedro.gomez@example.com',
            'sex' => Sex::Male->value,
            'document_id' => '12345678A',
            'birth_date' => '2010-03-15',
            'relationship_type' => RelationshipType::Father->value,
            'section_id' => Str::uuid()->toString(),
        ], $overrides);
    }
}
