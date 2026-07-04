<?php

namespace Tests\Feature\Academics;

use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Models\SubjectTeacher;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Tenancy\Models\Tenant;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTeacherPivotTenancyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_attach_assigns_current_tenant_to_pivot(): void
    {
        $teacher = Teacher::factory()->create();
        $subject = Subject::factory()->create();

        $subject->teachers()->attach($teacher->id);

        $this->assertDatabaseHas('subject_teacher', [
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_sync_assigns_current_tenant_to_pivot(): void
    {
        $teacher = Teacher::factory()->create();
        $subject = Subject::factory()->create();

        $teacher->subjects()->sync([$subject->id]);

        $this->assertDatabaseHas('subject_teacher', [
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_pivot_rows_are_isolated_across_tenants(): void
    {
        $teacher = Teacher::factory()->create();
        $subject = Subject::factory()->create();
        $subject->teachers()->attach($teacher->id);

        $otherTenant = Tenant::factory()->create();
        $this->withinTenant($otherTenant, function () {
            $foreignTeacher = Teacher::factory()->create();
            $foreignSubject = Subject::factory()->create();
            $foreignSubject->teachers()->attach($foreignTeacher->id);
        });

        $this->assertDatabaseCount('subject_teacher', 2);
        $this->assertSame(1, SubjectTeacher::query()->count());
        $this->assertSame(
            1,
            $this->withinTenant($otherTenant, fn () => SubjectTeacher::query()->count())
        );
    }
}
