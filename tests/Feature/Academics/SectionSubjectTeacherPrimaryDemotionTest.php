<?php

namespace Tests\Feature\Academics;

use App\Domains\Academics\Enums\SectionSubjectTeacherStatus;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Models\SubjectTeacher;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Academics\Services\SectionSubjectTeacher\StoreSectionSubjectTeacherService;
use App\Domains\Academics\Services\SectionSubjectTeacher\UpdateSectionSubjectTeacherService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionSubjectTeacherPrimaryDemotionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_store_demotes_the_previous_primary(): void
    {
        $existing = SectionSubjectTeacher::factory()->create();
        $newTeacher = Teacher::factory()->create();
        SubjectTeacher::create([
            'teacher_id' => $newTeacher->id,
            'subject_id' => $existing->subject_id,
        ]);

        $created = app(StoreSectionSubjectTeacherService::class)->handle([
            'section_id' => $existing->section_id,
            'subject_id' => $existing->subject_id,
            'teacher_id' => $newTeacher->id,
            'is_primary' => true,
            'status' => SectionSubjectTeacherStatus::Active->value,
        ]);

        $this->assertFalse($existing->fresh()->is_primary);
        $this->assertTrue($created->fresh()->is_primary);
    }

    public function test_update_demotes_the_previous_primary_for_the_same_section_and_subject(): void
    {
        $primary = SectionSubjectTeacher::factory()->create();
        $target = SectionSubjectTeacher::factory()->substitute()->create([
            'section_id' => $primary->section_id,
            'subject_id' => $primary->subject_id,
        ]);

        app(UpdateSectionSubjectTeacherService::class)->handle($target, [
            'is_primary' => true,
            'status' => SectionSubjectTeacherStatus::Active->value,
        ]);

        $this->assertFalse($primary->fresh()->is_primary);
        $this->assertTrue($target->fresh()->is_primary);
    }

    public function test_primary_for_scope_excludes_the_given_id(): void
    {
        $primary = SectionSubjectTeacher::factory()->create();
        $peer = SectionSubjectTeacher::factory()->create([
            'section_id' => $primary->section_id,
            'subject_id' => $primary->subject_id,
        ]);

        $withoutException = SectionSubjectTeacher::primaryFor($primary->section_id, $primary->subject_id)
            ->pluck('id')
            ->all();
        $excludingPrimary = SectionSubjectTeacher::primaryFor($primary->section_id, $primary->subject_id, $primary->id)
            ->pluck('id')
            ->all();

        $this->assertEqualsCanonicalizing([$primary->id, $peer->id], $withoutException);
        $this->assertSame([$peer->id], $excludingPrimary);
    }

    public function test_store_without_the_primary_flag_demotes_nobody(): void
    {
        $existing = SectionSubjectTeacher::factory()->create();
        $newTeacher = Teacher::factory()->create();
        SubjectTeacher::create([
            'teacher_id' => $newTeacher->id,
            'subject_id' => $existing->subject_id,
        ]);

        app(StoreSectionSubjectTeacherService::class)->handle([
            'section_id' => $existing->section_id,
            'subject_id' => $existing->subject_id,
            'teacher_id' => $newTeacher->id,
            'is_primary' => false,
            'status' => SectionSubjectTeacherStatus::Active->value,
        ]);

        $this->assertTrue($existing->fresh()->is_primary);
    }
}
