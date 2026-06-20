<?php

namespace Tests\Feature\Api\Academics;

use App\Domains\Academics\Models\Teacher;
use App\Domains\Identity\Models\User;
use App\Domains\Shared\Enums\Sex;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TeacherApiTest extends TestCase
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

    public function test_index_returns_paginated_teachers(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        Teacher::factory()->count(2)->create();

        $response = $this->withToken($token)->getJson('/api/v1/teachers');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'isActive', 'user' => ['id', 'name', 'lastName', 'email'], 'createdAt', 'updatedAt']],
                'meta' => ['total', 'page', 'perPage'],
            ])
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.page', 1);
    }

    public function test_show_returns_a_teacher(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $teacher = Teacher::factory()->create();

        $response = $this->withToken($token)->getJson("/api/v1/teachers/{$teacher->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $teacher->id)
            ->assertJsonPath('data.user.email', $teacher->email);
    }

    public function test_show_unknown_teacher_returns_not_found(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->getJson('/api/v1/teachers/'.Str::uuid());

        $response->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_store_creates_a_teacher(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->postJson('/api/v1/teachers', [
            'name' => 'Carlos',
            'last_name' => 'Mendoza',
            'email' => 'carlos.mendoza@example.com',
            'sex' => Sex::Male->value,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.isActive', true)
            ->assertJsonPath('data.user.name', 'CARLOS')
            ->assertJsonPath('data.user.lastName', 'MENDOZA');

        $this->assertDatabaseHas('users', ['email' => 'carlos.mendoza@example.com']);
        $this->assertDatabaseHas('teachers', ['is_active' => true]);
    }

    public function test_store_validation_error_returns_envelope(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->postJson('/api/v1/teachers', []);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'error' => ['code', 'message', 'fields' => ['name', 'last_name', 'email', 'sex', 'password']],
            ]);
    }

    public function test_update_modifies_a_teacher(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $teacher = Teacher::factory()->create();

        $response = $this->withToken($token)->patchJson("/api/v1/teachers/{$teacher->id}", [
            'email' => 'updated.email@example.com',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.user.email', 'updated.email@example.com');

        $this->assertDatabaseHas('users', [
            'id' => $teacher->user_id,
            'email' => 'updated.email@example.com',
        ]);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/teachers');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_forbidden_for_unauthorized_role(): void
    {
        $token = $this->tokenFor(User::factory()->create());

        $response = $this->withToken($token)->getJson('/api/v1/teachers');

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
    }
}
