<?php

namespace Tests\Feature\Api\Students;

use App\Domains\Academics\Models\Section;
use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Enums\RelationshipType;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Shared\Enums\Sex;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StudentApiTest extends TestCase
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
            'name' => 'Pedro',
            'last_name' => 'Gomez',
            'email' => 'pedro.gomez@example.com',
            'sex' => Sex::Male->value,
            'document_id' => '12345678A',
            'birth_date' => '2010-03-15',
            'relationship_type' => RelationshipType::Father->value,
            'section_id' => Section::factory()->create()->id,
        ], $overrides);
    }

    public function test_index_returns_paginated_students(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        Student::factory()->count(2)->create();

        $response = $this->withToken($token)->getJson('/api/v1/students');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'studentCode',
                    'situation',
                    'relationshipType',
                    'isActive',
                    'representativeId',
                    'user' => ['id', 'name', 'lastName', 'email'],
                    'createdAt',
                    'updatedAt',
                ]],
                'meta' => ['total', 'page', 'perPage'],
            ])
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.page', 1);
    }

    public function test_index_filters_by_active_status(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        Student::factory()->create();
        Student::factory()->inactive()->create();

        $response = $this->withToken($token)->getJson('/api/v1/students?isActive=true');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.isActive', true);
    }

    public function test_index_filters_by_section(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $section = Section::factory()->create();
        Student::factory()->inSection($section)->create();
        Student::factory()->create();

        $response = $this->withToken($token)->getJson("/api/v1/students?sectionId={$section->id}");

        $response->assertOk()
            ->assertJsonPath('meta.total', 1);
    }

    public function test_show_returns_a_student(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $student = Student::factory()->create();

        $response = $this->withToken($token)->getJson("/api/v1/students/{$student->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $student->id)
            ->assertJsonPath('data.representativeId', $student->representative_id)
            ->assertJsonPath('data.user.email', $student->email);
    }

    public function test_show_unknown_student_returns_not_found(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->getJson('/api/v1/students/'.Str::uuid());

        $response->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_store_creates_a_student_nested_under_representative(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $representative = Representative::factory()->create();
        $section = Section::factory()->create();

        $response = $this->withToken($token)->postJson(
            "/api/v1/representatives/{$representative->id}/students",
            $this->validPayload(['section_id' => $section->id])
        );

        $response->assertCreated()
            ->assertJsonPath('data.isActive', true)
            ->assertJsonPath('data.representativeId', $representative->id)
            ->assertJsonPath('data.user.name', 'PEDRO')
            ->assertJsonPath('data.user.lastName', 'GOMEZ');

        $user = User::where('email', 'pedro.gomez@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->is_active);
        $this->assertTrue($user->hasRole(Role::Student->value));

        $this->assertDatabaseHas('students', [
            'user_id' => $user->id,
            'representative_id' => $representative->id,
            'is_active' => true,
        ]);

        $student = Student::where('user_id', $user->id)->first();
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'section_id' => $section->id,
            'status' => 'activo',
        ]);

        $this->assertDatabaseHas('representatives', [
            'id' => $representative->id,
            'is_active' => true,
        ]);
    }

    public function test_store_validation_error_returns_envelope(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $representative = Representative::factory()->create();

        $response = $this->withToken($token)->postJson(
            "/api/v1/representatives/{$representative->id}/students",
            []
        );

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'error' => ['code', 'message', 'fields' => [
                    'name',
                    'last_name',
                    'sex',
                    'document_id',
                    'birth_date',
                    'relationship_type',
                    'section_id',
                ]],
            ]);
    }

    public function test_update_modifies_a_student(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $student = Student::factory()->create();

        $payload = [
            'email' => 'updated.student@example.com',
            'document_id' => $student->document_id,
            'birth_date' => '2011-06-10',
        ];

        $response = $this->withToken($token)->patchJson("/api/v1/students/{$student->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.user.email', 'updated.student@example.com')
            ->assertJsonPath('data.representative.user.id', $student->representative->user_id);

        $this->assertDatabaseHas('users', [
            'id' => $student->user_id,
            'email' => 'updated.student@example.com',
        ]);
    }

    public function test_destroy_is_not_allowed(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $student = Student::factory()->create();

        $response = $this->withToken($token)->deleteJson("/api/v1/students/{$student->id}");

        $response->assertStatus(405);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/students');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_forbidden_for_unauthorized_role(): void
    {
        $token = $this->tokenFor(User::factory()->create());

        $response = $this->withToken($token)->getJson('/api/v1/students');

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
    }
}
