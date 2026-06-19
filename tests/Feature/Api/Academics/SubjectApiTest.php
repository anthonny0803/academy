<?php

namespace Tests\Feature\Api\Academics;

use App\Domains\Academics\Models\Subject;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SubjectApiTest extends TestCase
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

    public function test_index_returns_paginated_subjects(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        Subject::factory()->count(3)->create();

        $response = $this->withToken($token)->getJson('/api/v1/subjects');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'name', 'description', 'isActive', 'createdAt', 'updatedAt']],
                'meta' => ['total', 'page', 'perPage'],
            ])
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.page', 1);
    }

    public function test_show_returns_a_subject(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $subject = Subject::factory()->create();

        $response = $this->withToken($token)->getJson("/api/v1/subjects/{$subject->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $subject->id)
            ->assertJsonPath('data.name', $subject->name);
    }

    public function test_show_unknown_subject_returns_not_found(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->getJson('/api/v1/subjects/'.Str::uuid());

        $response->assertStatus(404)->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_store_creates_a_subject(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->postJson('/api/v1/subjects', [
            'name' => 'Quimica Organica',
            'description' => 'Estudio de los compuestos del carbono',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'QUIMICA ORGANICA')
            ->assertJsonPath('data.isActive', true);

        $this->assertDatabaseHas('subjects', ['name' => 'QUIMICA ORGANICA']);
    }

    public function test_store_validation_error_returns_envelope(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->postJson('/api/v1/subjects', []);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure(['error' => ['code', 'message', 'fields' => ['name', 'description']]]);
    }

    public function test_update_modifies_a_subject(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $subject = Subject::factory()->create();

        $response = $this->withToken($token)->patchJson("/api/v1/subjects/{$subject->id}", [
            'name' => 'Algebra Lineal',
            'description' => 'Vectores, matrices y transformaciones',
        ]);

        $response->assertOk()->assertJsonPath('data.name', 'ALGEBRA LINEAL');

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'ALGEBRA LINEAL',
        ]);
    }

    public function test_destroy_deletes_an_inactive_subject(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $subject = Subject::factory()->inactive()->create();

        $response = $this->withToken($token)->deleteJson("/api/v1/subjects/{$subject->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/subjects');

        $response->assertStatus(401)->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_forbidden_for_unauthorized_role(): void
    {
        $token = $this->tokenFor(User::factory()->create());

        $response = $this->withToken($token)->getJson('/api/v1/subjects');

        $response->assertStatus(403)->assertJsonPath('error.code', 'FORBIDDEN');
    }
}
