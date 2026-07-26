<?php

namespace Tests\Feature\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeColumnIndexAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private const FOREIGN_ASSIGNMENT_ERROR = 'Esta asignación no te corresponde.';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_a_teacher_cannot_view_the_grade_columns_of_another_teacher_assignment(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $foreignTeacher = Teacher::factory()->create();

        $response = $this->actingAs($foreignTeacher->user)
            ->get(route('grade-columns.index', $sst));

        $response->assertRedirect();
        $response->assertSessionHas('error', self::FOREIGN_ASSIGNMENT_ERROR);
    }

    public function test_the_assigned_teacher_can_view_the_grade_columns(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();

        $response = $this->actingAs($sst->teacher->user)
            ->get(route('grade-columns.index', $sst));

        $response->assertOk();
        $response->assertViewIs('grade-columns.index');
    }

    public function test_the_assigned_teacher_can_view_the_grade_columns_of_an_inactive_assignment(): void
    {
        $sst = SectionSubjectTeacher::factory()->inactive()->create();

        $response = $this->actingAs($sst->teacher->user)
            ->get(route('grade-columns.index', $sst));

        $response->assertOk();
        $response->assertViewIs('grade-columns.index');
    }

    public function test_a_supervisor_can_view_the_grade_columns_of_any_assignment(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $sst = SectionSubjectTeacher::factory()->create();

        $response = $this->actingAs($supervisor)
            ->get(route('grade-columns.index', $sst));

        $response->assertOk();
        $response->assertViewIs('grade-columns.index');
    }
}
