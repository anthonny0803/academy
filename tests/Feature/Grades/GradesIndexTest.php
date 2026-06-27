<?php

namespace Tests\Feature\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradesIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_index_exposes_grades_grouped_by_enrollment_and_column(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $sst = SectionSubjectTeacher::factory()->create();
        $column = GradeColumn::factory()->withWeight(100)->create([
            'section_subject_teacher_id' => $sst->id,
        ]);

        $student = Student::factory()->inSection($sst->section)->create();
        $enrollment = $student->enrollments()->where('section_id', $sst->section_id)->first();

        $grade = Grade::factory()->withValue(75)->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);

        $response = $this->actingAs($supervisor)->get(route('grades.index', $sst));

        $response->assertOk();
        $response->assertViewIs('grades.index');
        $response->assertViewHas('isConfigurationComplete', true);
        $response->assertViewHas('gradesByEnrollment', function ($grouped) use ($enrollment, $column, $grade) {
            return isset($grouped[$enrollment->id][$column->id])
                && $grouped[$enrollment->id][$column->id]->is($grade);
        });
    }
}
