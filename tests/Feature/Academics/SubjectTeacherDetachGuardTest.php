<?php

namespace Tests\Feature\Academics;

use App\Domains\Academics\Exceptions\SubjectTeacherInUseException;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Academics\Services\SubjectTeacher\StoreSubjectTeacherService;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTeacherDetachGuardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_sync_rejects_removing_a_subject_the_teacher_still_teaches(): void
    {
        $assignment = SectionSubjectTeacher::factory()->create();
        $teacher = $assignment->teacher;
        $assignedSubject = $assignment->subject;
        $freeSubject = Subject::factory()->create();
        $teacher->subjects()->attach($freeSubject->id);

        try {
            app(StoreSubjectTeacherService::class)->handle($teacher, ['subjects' => [$freeSubject->id]]);

            $this->fail('Expected SubjectTeacherInUseException was not thrown.');
        } catch (SubjectTeacherInUseException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('SUBJECT_TEACHER_IN_USE', $e->errorCode());
            $this->assertStringContainsString($assignedSubject->name, $e->getMessage());
        }

        $this->assertDatabaseHas('subject_teacher', [
            'teacher_id' => $teacher->id,
            'subject_id' => $assignedSubject->id,
        ]);
    }

    public function test_sync_detaches_a_subject_the_teacher_does_not_teach(): void
    {
        $teacher = Teacher::factory()->create();
        $keptSubject = Subject::factory()->create();
        $removedSubject = Subject::factory()->create();
        $teacher->subjects()->attach([$keptSubject->id, $removedSubject->id]);

        app(StoreSubjectTeacherService::class)->handle($teacher, ['subjects' => [$keptSubject->id]]);

        $this->assertDatabaseHas('subject_teacher', [
            'teacher_id' => $teacher->id,
            'subject_id' => $keptSubject->id,
        ]);
        $this->assertDatabaseMissing('subject_teacher', [
            'teacher_id' => $teacher->id,
            'subject_id' => $removedSubject->id,
        ]);
    }

    public function test_web_store_redirects_back_with_an_error_and_keeps_the_subject_in_use(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $assignment = SectionSubjectTeacher::factory()->create();
        $teacher = $assignment->teacher;
        $freeSubject = Subject::factory()->create();
        $teacher->subjects()->attach($freeSubject->id);

        $response = $this->actingAs($supervisor)
            ->from(route('teachers.subjects.assign', $teacher))
            ->post(route('teachers.subjects.store', $teacher), [
                'subjects' => [$freeSubject->id],
            ]);

        $response->assertRedirect(route('teachers.subjects.assign', $teacher));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('subject_teacher', [
            'teacher_id' => $teacher->id,
            'subject_id' => $assignment->subject_id,
        ]);
    }
}
