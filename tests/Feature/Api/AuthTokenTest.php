<?php

namespace Tests\Feature\Api;

use App\Domains\Academics\Models\Teacher;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTokenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_valid_credentials_issue_a_token(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['token', 'tokenType']])
            ->assertJsonPath('data.tokenType', 'Bearer');

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_missing_fields_return_validation_envelope(): void
    {
        $response = $this->postJson('/api/v1/auth/token', []);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure(['error' => ['code', 'message', 'fields' => ['email', 'password']]]);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonPath('error.fields.email', 'Las credenciales proporcionadas son incorrectas.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_inactive_user_cannot_obtain_a_token(): void
    {
        $user = User::factory()->inactive()->create();

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.fields.email', 'Tu cuenta está inactiva, contacta con el administrador.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_inactive_user_with_active_teacher_can_obtain_a_token(): void
    {
        $teacher = Teacher::factory()->create();

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => $teacher->user->email,
            'password' => 'password',
        ]);

        $response->assertOk()->assertJsonPath('data.tokenType', 'Bearer');
    }

    public function test_logout_revokes_the_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertNoContent();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401)->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_unknown_api_route_returns_not_found_envelope(): void
    {
        $response = $this->getJson('/api/v1/does-not-exist');

        $response->assertStatus(404)->assertJsonPath('error.code', 'NOT_FOUND');
    }
}
