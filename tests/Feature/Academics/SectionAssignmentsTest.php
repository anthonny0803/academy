<?php

namespace Tests\Feature\Academics;

use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionAssignmentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_assignments_view_receives_active_subjects_with_their_active_teachers(): void
    {
        $developer = User::factory()->developer()->create();
        $section = Section::factory()->create();
        SectionSubjectTeacher::factory()->create(['section_id' => $section->id]);

        $subject = Subject::factory()->create();
        $teacher = Teacher::factory()->create();
        $subject->teachers()->attach($teacher->id);

        $response = $this->actingAs($developer)->get(route('sections.assignments', $section));

        $response->assertOk();
        $response->assertViewIs('sections.assignments');
        $response->assertViewHas('subjects');
        $response->assertViewHas('teachersBySubject', function (array $teachersBySubject) use ($subject, $teacher) {
            return $teachersBySubject[$subject->id] === [[
                'id' => $teacher->id,
                'name' => $teacher->user->full_name,
            ]];
        });
    }

    public function test_assignments_excludes_inactive_teachers_from_each_subject(): void
    {
        $developer = User::factory()->developer()->create();
        $section = Section::factory()->create();

        $subject = Subject::factory()->create();
        $activeTeacher = Teacher::factory()->create();
        $inactiveTeacher = Teacher::factory()->inactive()->create();
        $subject->teachers()->attach([$activeTeacher->id, $inactiveTeacher->id]);

        $response = $this->actingAs($developer)->get(route('sections.assignments', $section));

        $response->assertViewHas('teachersBySubject', function (array $teachersBySubject) use ($subject, $activeTeacher) {
            $teacherIds = array_column($teachersBySubject[$subject->id], 'id');

            return $teacherIds === [$activeTeacher->id];
        });
    }
}
