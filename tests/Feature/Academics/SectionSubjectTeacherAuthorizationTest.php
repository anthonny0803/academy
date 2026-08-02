<?php

namespace Tests\Feature\Academics;

use App\Domains\Academics\Enums\SectionSubjectTeacherStatus;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class SectionSubjectTeacherAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_a_developer_may_update_and_delete_an_assignment(): void
    {
        $developer = User::factory()->developer()->create();
        $assignment = SectionSubjectTeacher::factory()->create();

        $this->assertTrue(Gate::forUser($developer)->allows('update', $assignment));
        $this->assertTrue(Gate::forUser($developer)->allows('delete', $assignment));
    }

    public function test_a_supervisor_may_update_an_assignment_but_not_delete_it(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $assignment = SectionSubjectTeacher::factory()->create();

        $this->assertTrue(Gate::forUser($supervisor)->allows('update', $assignment));

        $deletion = Gate::forUser($supervisor)->inspect('delete', $assignment);

        $this->assertTrue($deletion->denied());
        $this->assertSame('Solo los desarrolladores pueden eliminar asignaciones.', $deletion->message());
    }

    public function test_a_teacher_may_neither_update_nor_delete_an_assignment(): void
    {
        $assignment = SectionSubjectTeacher::factory()->create();
        $teacherUser = $assignment->teacher->user;

        $update = Gate::forUser($teacherUser)->inspect('update', $assignment);

        $this->assertTrue($update->denied());
        $this->assertSame(
            'No tienes autorización para gestionar asignaciones de profesores.',
            $update->message()
        );
        $this->assertTrue(Gate::forUser($teacherUser)->denies('delete', $assignment));
    }

    public function test_the_web_update_authorizes_the_bound_assignment(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $assignment = SectionSubjectTeacher::factory()->create();

        $response = $this->actingAs($supervisor)
            ->from(route('sections.assignments', $assignment->section_id))
            ->put(route('section-subject-teacher.update', $assignment), [
                'is_primary' => '1',
                'status' => SectionSubjectTeacherStatus::Substitute->value,
            ]);

        $response->assertRedirect(route('sections.show', $assignment->section_id));
        $this->assertSame(
            SectionSubjectTeacherStatus::Substitute->value,
            $assignment->fresh()->status
        );
    }
}
