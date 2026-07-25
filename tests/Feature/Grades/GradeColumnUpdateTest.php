<?php

namespace Tests\Feature\Grades;

use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeColumnUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_update_clears_observation_when_the_field_is_submitted_empty(): void
    {
        $developer = User::factory()->developer()->create();
        $column = GradeColumn::factory()->create(['observation' => 'REVISAR PONDERACION']);

        $response = $this->actingAs($developer)->put(
            route('grade-columns.update', $column),
            [
                'name' => $column->name,
                'weight' => $column->weight,
                'observation' => '',
            ]
        );

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('grade_columns', [
            'id' => $column->id,
            'observation' => null,
        ]);
    }

    public function test_update_keeps_observation_when_the_field_is_omitted(): void
    {
        $developer = User::factory()->developer()->create();
        $column = GradeColumn::factory()->create(['observation' => 'REVISAR PONDERACION']);

        $response = $this->actingAs($developer)->put(
            route('grade-columns.update', $column),
            [
                'name' => $column->name,
                'weight' => $column->weight,
            ]
        );

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('grade_columns', [
            'id' => $column->id,
            'observation' => 'REVISAR PONDERACION',
        ]);
    }
}
