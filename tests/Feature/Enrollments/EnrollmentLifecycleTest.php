<?php

namespace Tests\Feature\Enrollments;

use App\Enums\EnrollmentStatus;
use App\Enums\StudentSituation;
use App\Models\AcademicPeriod;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }
    public function test_promote_creates_new_enrollment_in_target_section(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $academicPeriod = AcademicPeriod::factory()->promotable()->create();
        $section1 = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);
        $section2 = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $student = Student::factory()->inSection($section1)->create();
        $enrollment = $student->enrollments()->where('section_id', $section1->id)->first();

        $response = $this->actingAs($supervisor)->patch(
            route('enrollments.promote', $enrollment),
            ['section_id' => $section2->id]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Old enrollment marked as promoted
        $enrollment->refresh();
        $this->assertEquals(EnrollmentStatus::Promoted->value, $enrollment->status);

        // New enrollment created in target section
        $newEnrollment = Enrollment::where('student_id', $student->id)
            ->where('section_id', $section2->id)
            ->first();

        $this->assertNotNull($newEnrollment);
        $this->assertEquals(EnrollmentStatus::Active->value, $newEnrollment->status);
    }

    public function test_transfer_deactivates_student_and_syncs_representative(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $academicPeriod = AcademicPeriod::factory()->transferable()->create();
        $section = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $student = Student::factory()->inSection($section)->create();
        $enrollment = $student->enrollments()->where('section_id', $section->id)->first();
        $representative = $student->representative;

        // Representative is active after student creation
        $representative->refresh();
        $this->assertTrue($representative->is_active);

        $response = $this->actingAs($supervisor)->patch(
            route('enrollments.transfer', $enrollment),
            ['reason' => 'Cambio de ciudad']
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Enrollment marked as transferred
        $enrollment->refresh();
        $this->assertEquals(EnrollmentStatus::Transferred->value, $enrollment->status);

        // Student deactivated (no other active enrollments)
        $student->refresh();
        $this->assertFalse($student->is_active);
        $this->assertEquals(StudentSituation::Inactive, $student->situation);

        // Representative deactivated (no active students)
        $representative->refresh();
        $this->assertFalse($representative->is_active);
    }

    public function test_withdraw_deactivates_all_enrollments_and_student(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $section = Section::factory()->create();
        $student = Student::factory()->inSection($section)->create();
        $enrollment = $student->enrollments()->where('section_id', $section->id)->first();
        $representative = $student->representative;

        $representative->refresh();
        $this->assertTrue($representative->is_active);

        $response = $this->actingAs($supervisor)->patch(
            route('students.withdraw', $student),
            ['reason' => 'Motivo personal']
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Enrollment withdrawn
        $enrollment->refresh();
        $this->assertEquals(EnrollmentStatus::Withdrawn->value, $enrollment->status);

        // Student deactivated
        $student->refresh();
        $this->assertFalse($student->is_active);
        $this->assertEquals(StudentSituation::Inactive, $student->situation);

        // Representative deactivated
        $representative->refresh();
        $this->assertFalse($representative->is_active);
    }
}
