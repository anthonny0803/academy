<?php

namespace Tests\Feature\AcademicPeriods;

use App\Enums\EnrollmentStatus;
use App\Enums\StudentSituation;
use App\Models\AcademicPeriod;
use App\Models\Grade;
use App\Models\GradeColumn;
use App\Models\Representative;
use App\Models\Section;
use App\Models\SectionSubjectTeacher;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CloseAcademicPeriodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }
    public function test_close_period_completes_enrollments_with_correct_pass_fail(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $academicPeriod = AcademicPeriod::factory()->create([
            'passing_grade' => 5,
        ]);

        $section = Section::factory()->create([
            'academic_period_id' => $academicPeriod->id,
        ]);

        // Subject assignment with grade column (100% weight)
        $sst = SectionSubjectTeacher::factory()->create([
            'section_id' => $section->id,
        ]);
        $gradeColumn = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);

        // Shared representative
        $representative = Representative::factory()->create();

        // Student who PASSES (grade 8 >= passing 5)
        $passingStudent = Student::factory()->inSection($section)->create([
            'representative_id' => $representative->id,
        ]);
        $passingEnrollment = $passingStudent->enrollments()
            ->where('section_id', $section->id)->first();
        Grade::factory()->create([
            'enrollment_id' => $passingEnrollment->id,
            'grade_column_id' => $gradeColumn->id,
            'value' => 8,
        ]);

        // Student who FAILS (grade 3 < passing 5)
        $failingStudent = Student::factory()->inSection($section)->create([
            'representative_id' => $representative->id,
        ]);
        $failingEnrollment = $failingStudent->enrollments()
            ->where('section_id', $section->id)->first();
        Grade::factory()->create([
            'enrollment_id' => $failingEnrollment->id,
            'grade_column_id' => $gradeColumn->id,
            'value' => 3,
        ]);

        // Representative should be active after student creation
        $representative->refresh();
        $this->assertTrue($representative->is_active);

        // Act: Close the period
        $response = $this->actingAs($supervisor)->patch(
            route('academic-periods.close', $academicPeriod)
        );

        $response->assertRedirect(route('academic-periods.index'));
        $response->assertSessionHas('success');

        // Passing student: completed + passed=true
        $passingEnrollment->refresh();
        $this->assertEquals(EnrollmentStatus::Completed->value, $passingEnrollment->status);
        $this->assertTrue($passingEnrollment->passed);

        // Failing student: completed + passed=false
        $failingEnrollment->refresh();
        $this->assertEquals(EnrollmentStatus::Completed->value, $failingEnrollment->status);
        $this->assertFalse($failingEnrollment->passed);

        // Section and period deactivated
        $section->refresh();
        $this->assertFalse($section->is_active);

        $academicPeriod->refresh();
        $this->assertFalse($academicPeriod->is_active);

        // Students deactivated (no other active enrollments)
        $passingStudent->refresh();
        $this->assertFalse($passingStudent->is_active);
        $this->assertEquals(StudentSituation::Inactive, $passingStudent->situation);

        $failingStudent->refresh();
        $this->assertFalse($failingStudent->is_active);

        // Representative deactivated (no active students)
        $representative->refresh();
        $this->assertFalse($representative->is_active);
    }

    public function test_cannot_close_period_with_incomplete_grades(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $academicPeriod = AcademicPeriod::factory()->create();
        $section = Section::factory()->create([
            'academic_period_id' => $academicPeriod->id,
        ]);

        // SST with grade column but NO grades for the student
        $sst = SectionSubjectTeacher::factory()->create([
            'section_id' => $section->id,
        ]);
        GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);

        Student::factory()->inSection($section)->create();

        $response = $this->actingAs($supervisor)
            ->from(route('academic-periods.show', $academicPeriod))
            ->patch(route('academic-periods.close', $academicPeriod));

        // Service throws exception, controller catches and redirects with error
        $response->assertRedirect(route('academic-periods.show', $academicPeriod));
        $response->assertSessionHas('error');
    }
}
