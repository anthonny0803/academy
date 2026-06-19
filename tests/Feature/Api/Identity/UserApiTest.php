<?php

namespace Tests\Feature\Api\Identity;

use App\Domains\Identity\Models\User;
use App\Domains\Shared\Enums\Sex;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserApiTest extends TestCase
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

    public function test_index_returns_paginated_employees(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        User::factory()->admin()->count(2)->create();

        $response = $this->withToken($token)->getJson('/api/v1/users');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'name', 'lastName', 'fullName', 'email', 'isActive', 'isDeveloper', 'createdAt', 'updatedAt']],
                'meta' => ['total', 'page', 'perPage'],
            ])
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.page', 1);
    }

    public function test_show_returns_a_user(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $user = User::factory()->admin()->create();

        $response = $this->withToken($token)->getJson("/api/v1/users/{$user->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_show_unknown_user_returns_not_found(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->getJson('/api/v1/users/'.Str::uuid());

        $response->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_store_creates_a_user(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->postJson('/api/v1/users', [
            'name' => 'Carlos',
            'last_name' => 'Mendoza',
            'email' => 'carlos.mendoza@example.com',
            'sex' => Sex::Male->value,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Administrador',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'CARLOS')
            ->assertJsonPath('data.lastName', 'MENDOZA')
            ->assertJsonPath('data.isActive', true);

        $this->assertDatabaseHas('users', ['email' => 'carlos.mendoza@example.com']);
    }

    public function test_store_validation_error_returns_envelope(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->postJson('/api/v1/users', []);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'error' => ['code', 'message', 'fields' => ['name', 'last_name', 'email', 'sex', 'password', 'role']],
            ]);
    }

    public function test_update_modifies_a_user(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $user = User::factory()->admin()->create();

        $response = $this->withToken($token)->patchJson("/api/v1/users/{$user->id}", [
            'email' => 'updated.email@example.com',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.email', 'updated.email@example.com');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'updated.email@example.com',
        ]);
    }

    public function test_destroy_deletes_an_inactive_user_without_profiles(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $user = User::factory()->admin()->inactive()->create();

        $response = $this->withToken($token)->deleteJson("/api/v1/users/{$user->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_forbidden_for_unauthorized_role(): void
    {
        $token = $this->tokenFor(User::factory()->create());

        $response = $this->withToken($token)->getJson('/api/v1/users');

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
    }
}
