<?php

namespace Tests\Feature\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherAssignmentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_teacher_sees_active_assignments_grouped_by_period(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $teacherUser = $sst->teacher->user;

        $response = $this->actingAs($teacherUser)->get(route('teacher.assignments'));

        $response->assertOk();
        $response->assertViewIs('grades.teacher-assignments');
        $response->assertViewHas('teacher');
        $response->assertViewHas('assignments', function ($assignments) use ($sst) {
            return $assignments->collapse()->contains('id', $sst->id);
        });
    }

    public function test_non_teacher_is_redirected_to_dashboard(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $response = $this->actingAs($supervisor)->get(route('teacher.assignments'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }
}
