<?php

namespace Tests\Feature\Api\Academics;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SectionApiTest extends TestCase
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

    public function test_index_returns_paginated_sections(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        Section::factory()->count(3)->create();

        $response = $this->withToken($token)->getJson('/api/v1/sections');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'academicPeriodId', 'name', 'description', 'capacity', 'isActive', 'createdAt', 'updatedAt']],
                'meta' => ['total', 'page', 'perPage'],
            ])
            ->assertJsonPath('meta.total', 3);
    }

    public function test_show_returns_a_section(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $section = Section::factory()->create();

        $response = $this->withToken($token)->getJson("/api/v1/sections/{$section->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $section->id)
            ->assertJsonPath('data.academicPeriodId', $section->academic_period_id);
    }

    public function test_show_unknown_section_returns_not_found(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->getJson('/api/v1/sections/'.Str::uuid());

        $response->assertStatus(404)->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_store_creates_a_section(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $period = AcademicPeriod::factory()->create();

        $response = $this->withToken($token)->postJson('/api/v1/sections', [
            'academic_period_id' => $period->id,
            'name' => 'Seccion A',
            'description' => 'Turno matutino',
            'capacity' => 30,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'SECCION A')
            ->assertJsonPath('data.capacity', 30)
            ->assertJsonPath('data.academicPeriodId', $period->id);

        $this->assertDatabaseHas('sections', [
            'name' => 'SECCION A',
            'academic_period_id' => $period->id,
        ]);
    }

    public function test_store_validation_error_returns_envelope(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->postJson('/api/v1/sections', []);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure(['error' => ['code', 'message', 'fields' => ['academic_period_id', 'name', 'capacity']]]);
    }

    public function test_update_modifies_a_section(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $section = Section::factory()->create();

        $response = $this->withToken($token)->patchJson("/api/v1/sections/{$section->id}", [
            'academic_period_id' => $section->academic_period_id,
            'name' => 'Seccion B',
            'description' => 'Turno vespertino',
            'capacity' => 25,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'SECCION B')
            ->assertJsonPath('data.capacity', 25);

        $this->assertDatabaseHas('sections', [
            'id' => $section->id,
            'name' => 'SECCION B',
            'capacity' => 25,
        ]);
    }

    public function test_destroy_deletes_an_inactive_section(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $section = Section::factory()->inactive()->create();

        $response = $this->withToken($token)->deleteJson("/api/v1/sections/{$section->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('sections', ['id' => $section->id]);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/sections');

        $response->assertStatus(401)->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_forbidden_for_unauthorized_role(): void
    {
        $token = $this->tokenFor(User::factory()->create());

        $response = $this->withToken($token)->getJson('/api/v1/sections');

        $response->assertStatus(403)->assertJsonPath('error.code', 'FORBIDDEN');
    }
}
