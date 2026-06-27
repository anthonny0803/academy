<?php

namespace Tests\Feature\Enrollments;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_show_renders_subjects_data_with_weighted_average(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $sst = SectionSubjectTeacher::factory()->create();
        $column = GradeColumn::factory()->withWeight(100)->create([
            'section_subject_teacher_id' => $sst->id,
        ]);

        $student = Student::factory()->inSection($sst->section)->create();
        $enrollment = $student->enrollments()->where('section_id', $sst->section_id)->first();

        Grade::factory()->withValue(80)->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);

        $response = $this->actingAs($supervisor)->get(route('enrollments.show', $enrollment));

        $response->assertOk();
        $response->assertViewIs('enrollments.show');
        $response->assertViewHas('passingGrade');
        $response->assertViewHas('subjectsData', function ($subjectsData) use ($sst) {
            $subject = $subjectsData->firstWhere('sst_id', $sst->id);

            return $subject !== null && (float) $subject['average'] === 80.0;
        });
    }
}
