<?php

namespace Tests\Feature\Api\Academics;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class AcademicPeriodApiTest extends TestCase
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

    public function test_index_returns_paginated_academic_periods(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        AcademicPeriod::factory()->count(3)->create();

        $response = $this->withToken($token)->getJson('/api/v1/academic-periods');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'name', 'notes', 'startDate', 'endDate', 'minGrade', 'maxGrade', 'passingGrade', 'isPromotable', 'isTransferable', 'isActive', 'createdAt', 'updatedAt']],
                'meta' => ['total', 'page', 'perPage'],
            ])
            ->assertJsonPath('meta.total', 3);
    }

    public function test_show_returns_an_academic_period(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $period = AcademicPeriod::factory()->create();

        $response = $this->withToken($token)->getJson("/api/v1/academic-periods/{$period->id}");

        $response->assertOk()->assertJsonPath('data.id', $period->id);
    }

    public function test_show_unknown_period_returns_not_found(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->getJson('/api/v1/academic-periods/'.Str::uuid());

        $response->assertStatus(404)->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_store_creates_an_academic_period(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->postJson('/api/v1/academic-periods', [
            'name' => 'Periodo 2026',
            'notes' => 'Año escolar regular',
            'start_date' => Carbon::now()->addDays(10)->toDateString(),
            'end_date' => Carbon::now()->addDays(40)->toDateString(),
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'PERIODO 2026')
            ->assertJsonPath('data.isActive', true);

        $this->assertDatabaseHas('academic_periods', ['name' => 'PERIODO 2026']);
    }

    public function test_store_validation_error_returns_envelope(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->postJson('/api/v1/academic-periods', []);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure(['error' => ['code', 'message', 'fields']]);
    }

    public function test_update_modifies_name_and_notes(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $period = AcademicPeriod::factory()->create();
        Section::factory()->create(['academic_period_id' => $period->id]);

        $response = $this->withToken($token)->patchJson("/api/v1/academic-periods/{$period->id}", [
            'name' => 'Periodo Actualizado',
            'notes' => 'Nota revisada',
        ]);

        $response->assertOk()->assertJsonPath('data.name', 'PERIODO ACTUALIZADO');

        $this->assertDatabaseHas('academic_periods', [
            'id' => $period->id,
            'name' => 'PERIODO ACTUALIZADO',
        ]);
    }

    public function test_destroy_deletes_a_period_without_sections(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $period = AcademicPeriod::factory()->create();

        $response = $this->withToken($token)->deleteJson("/api/v1/academic-periods/{$period->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('academic_periods', ['id' => $period->id]);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/academic-periods');

        $response->assertStatus(401)->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_forbidden_for_unauthorized_role(): void
    {
        $token = $this->tokenFor(User::factory()->create());

        $response = $this->withToken($token)->getJson('/api/v1/academic-periods');

        $response->assertStatus(403)->assertJsonPath('error.code', 'FORBIDDEN');
    }
}
