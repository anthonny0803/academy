<?php

namespace Tests\Feature\Api\Representatives;

use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Shared\Enums\Sex;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RepresentativeApiTest extends TestCase
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

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Lucia',
            'last_name' => 'Ramirez',
            'email' => 'lucia.ramirez@example.com',
            'sex' => Sex::Female->value,
            'document_id' => '12345678A',
            'birth_date' => '1990-05-20',
            'phone' => '123456789',
            'address' => 'Calle Falsa 123',
            'occupation' => 'Ingeniera',
        ], $overrides);
    }

    public function test_index_returns_paginated_representatives(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        Representative::factory()->count(2)->create();

        $response = $this->withToken($token)->getJson('/api/v1/representatives');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'isActive', 'user' => ['id', 'name', 'lastName', 'email'], 'createdAt', 'updatedAt']],
                'meta' => ['total', 'page', 'perPage'],
            ])
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.page', 1);
    }

    public function test_index_filters_by_active_status(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        Representative::factory()->active()->create();
        Representative::factory()->create();

        $response = $this->withToken($token)->getJson('/api/v1/representatives?isActive=true');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.isActive', true);
    }

    public function test_index_filters_by_has_students(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        Representative::factory()->count(2)->create();

        $response = $this->withToken($token)->getJson('/api/v1/representatives?hasStudents=true');

        $response->assertOk()
            ->assertJsonPath('meta.total', 0);
    }

    public function test_show_returns_a_representative(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $representative = Representative::factory()->create();

        $response = $this->withToken($token)->getJson("/api/v1/representatives/{$representative->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $representative->id)
            ->assertJsonPath('data.user.email', $representative->email);
    }

    public function test_show_unknown_representative_returns_not_found(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->getJson('/api/v1/representatives/'.Str::uuid());

        $response->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_store_creates_an_inactive_representative(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->postJson('/api/v1/representatives', $this->validPayload());

        $response->assertCreated()
            ->assertJsonPath('data.isActive', false)
            ->assertJsonPath('data.user.name', 'LUCIA')
            ->assertJsonPath('data.user.lastName', 'RAMIREZ');

        $this->assertDatabaseHas('users', [
            'email' => 'lucia.ramirez@example.com',
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('representatives', ['is_active' => false]);

        $user = User::where('email', 'lucia.ramirez@example.com')->first();
        $this->assertTrue($user->hasRole(Role::Representative->value));
    }

    public function test_store_validation_error_returns_envelope(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->postJson('/api/v1/representatives', []);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'error' => ['code', 'message', 'fields' => ['name', 'last_name', 'email', 'sex', 'document_id', 'birth_date', 'phone', 'address']],
            ]);
    }

    public function test_update_modifies_a_representative(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $representative = Representative::factory()->create();

        $payload = $this->validPayload([
            'email' => 'updated.email@example.com',
            'document_id' => $representative->document_id,
        ]);

        $response = $this->withToken($token)->patchJson("/api/v1/representatives/{$representative->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.user.email', 'updated.email@example.com');

        $this->assertDatabaseHas('users', [
            'id' => $representative->user_id,
            'email' => 'updated.email@example.com',
        ]);
    }

    public function test_destroy_is_not_allowed(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $representative = Representative::factory()->create();

        $response = $this->withToken($token)->deleteJson("/api/v1/representatives/{$representative->id}");

        $response->assertStatus(405);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/representatives');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_forbidden_for_unauthorized_role(): void
    {
        $token = $this->tokenFor(User::factory()->create());

        $response = $this->withToken($token)->getJson('/api/v1/representatives');

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
    }
}
