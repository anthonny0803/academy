<?php

namespace Tests\Feature\Academics;

use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Academics\Services\SubjectTeacher\StoreSubjectTeacherService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PivotUuidGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_sync_on_subject_teacher_generates_pivot_uuid(): void
    {
        $teacher = Teacher::factory()->create();
        $subject = Subject::factory()->create();

        $teacher->subjects()->sync([$subject->id]);

        $pivotId = DB::table('subject_teacher')
            ->where('teacher_id', $teacher->id)
            ->where('subject_id', $subject->id)
            ->value('id');

        $this->assertNotNull($pivotId);
    }

    public function test_attach_on_subject_teacher_generates_pivot_uuid(): void
    {
        $subject = Subject::factory()->create();
        $teacher = Teacher::factory()->create();

        $subject->teachers()->attach([$teacher->id]);

        $pivotId = DB::table('subject_teacher')
            ->where('teacher_id', $teacher->id)
            ->where('subject_id', $subject->id)
            ->value('id');

        $this->assertNotNull($pivotId);
    }

    public function test_store_subject_teacher_service_links_subjects_without_failure(): void
    {
        $teacher = Teacher::factory()->create();
        $subject = Subject::factory()->create();

        app(StoreSubjectTeacherService::class)->handle($teacher, [
            'subjects' => [$subject->id],
        ]);

        $this->assertTrue($teacher->subjects()->where('subjects.id', $subject->id)->exists());
    }
}
