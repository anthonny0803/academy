<?php

namespace Tests\Feature\Api\Tenancy;

use App\Domains\Identity\Models\User;
use App\Domains\Tenancy\Models\Tenant;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TenantApiTest extends TestCase
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

    public function test_index_returns_paginated_tenants(): void
    {
        $token = $this->tokenFor(User::factory()->developer()->create());
        Tenant::factory()->count(2)->create();

        $response = $this->withToken($token)->getJson('/api/v1/tenants');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'name', 'slug', 'plan', 'status', 'isActive', 'createdAt', 'updatedAt']],
                'meta' => ['total', 'page', 'perPage'],
            ])
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.page', 1);
    }

    public function test_show_returns_a_tenant(): void
    {
        $token = $this->tokenFor(User::factory()->developer()->create());
        $tenant = Tenant::factory()->create();

        $response = $this->withToken($token)->getJson("/api/v1/tenants/{$tenant->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $tenant->id)
            ->assertJsonPath('data.name', $tenant->name);
    }

    public function test_show_unknown_tenant_returns_not_found(): void
    {
        $token = $this->tokenFor(User::factory()->developer()->create());

        $response = $this->withToken($token)->getJson('/api/v1/tenants/'.Str::uuid());

        $response->assertStatus(404)->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_store_creates_a_tenant_with_defaults_and_slugifies_the_slug(): void
    {
        $token = $this->tokenFor(User::factory()->developer()->create());

        $response = $this->withToken($token)->postJson('/api/v1/tenants', [
            'name' => 'Colegio Nuevo',
            'slug' => 'Colegio Nuevo',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.slug', 'colegio-nuevo')
            ->assertJsonPath('data.plan', 'free')
            ->assertJsonPath('data.status', 'activo')
            ->assertJsonPath('data.isActive', true);

        $this->assertDatabaseHas('tenants', [
            'slug' => 'colegio-nuevo',
            'plan' => 'free',
            'status' => 'activo',
        ]);
    }

    public function test_store_persists_explicit_plan_and_status(): void
    {
        $token = $this->tokenFor(User::factory()->developer()->create());

        $response = $this->withToken($token)->postJson('/api/v1/tenants', [
            'name' => 'Colegio Pro',
            'slug' => 'colegio-pro',
            'plan' => 'pro',
            'status' => 'suspendido',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.plan', 'pro')
            ->assertJsonPath('data.status', 'suspendido')
            ->assertJsonPath('data.isActive', false);

        $this->assertDatabaseHas('tenants', [
            'slug' => 'colegio-pro',
            'plan' => 'pro',
            'status' => 'suspendido',
        ]);
    }

    public function test_store_validation_error_returns_envelope(): void
    {
        $token = $this->tokenFor(User::factory()->developer()->create());

        $response = $this->withToken($token)->postJson('/api/v1/tenants', []);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure(['error' => ['code', 'message', 'fields' => ['name', 'slug']]]);
    }

    public function test_store_rejects_an_invalid_plan(): void
    {
        $token = $this->tokenFor(User::factory()->developer()->create());

        $response = $this->withToken($token)->postJson('/api/v1/tenants', [
            'name' => 'Colegio Plan',
            'slug' => 'colegio-plan',
            'plan' => 'ultra',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure(['error' => ['fields' => ['plan']]]);
    }

    public function test_store_rejects_a_duplicate_slug(): void
    {
        $token = $this->tokenFor(User::factory()->developer()->create());
        Tenant::factory()->create(['slug' => 'colegio-existente']);

        $response = $this->withToken($token)->postJson('/api/v1/tenants', [
            'name' => 'Otro Colegio',
            'slug' => 'colegio-existente',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure(['error' => ['fields' => ['slug']]]);
    }

    public function test_update_modifies_a_tenant(): void
    {
        $token = $this->tokenFor(User::factory()->developer()->create());
        $tenant = Tenant::factory()->create();

        $response = $this->withToken($token)->patchJson("/api/v1/tenants/{$tenant->id}", [
            'name' => 'Colegio Actualizado',
            'slug' => 'colegio-actualizado',
            'plan' => 'enterprise',
            'status' => 'activo',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Colegio Actualizado')
            ->assertJsonPath('data.plan', 'enterprise');

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'slug' => 'colegio-actualizado',
            'plan' => 'enterprise',
        ]);
    }

    public function test_destroy_deletes_a_tenant_without_users(): void
    {
        $token = $this->tokenFor(User::factory()->developer()->create());
        $tenant = Tenant::factory()->create();

        $response = $this->withToken($token)->deleteJson("/api/v1/tenants/{$tenant->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }

    public function test_destroy_is_forbidden_for_a_tenant_with_users(): void
    {
        $token = $this->tokenFor(User::factory()->developer()->create());
        $tenant = Tenant::factory()->create();
        User::factory()->create(['tenant_id' => $tenant->id]);

        $response = $this->withToken($token)->deleteJson("/api/v1/tenants/{$tenant->id}");

        $response->assertStatus(403)->assertJsonPath('error.code', 'FORBIDDEN');
        $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/tenants');

        $response->assertStatus(401)->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_forbidden_for_non_developer(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->getJson('/api/v1/tenants');

        $response->assertStatus(403)->assertJsonPath('error.code', 'FORBIDDEN');
    }
}
