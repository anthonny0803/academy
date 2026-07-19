<?php

namespace Tests\Feature\Enrollments;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentCapacityTest extends TestCase
{
    use RefreshDatabase;

    private const SECTION_FULL_MESSAGE = 'La sección seleccionada ha alcanzado su capacidad máxima.';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_store_rejects_enrollment_when_section_is_full(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $fullSection = Section::factory()->withCapacity(1)->create();
        Student::factory()->inSection($fullSection)->create();

        $student = Student::factory()->create();

        $response = $this->actingAs($supervisor)->post(
            route('students.enrollments.store', $student),
            ['section_id' => $fullSection->id]
        );

        $response->assertSessionHasErrors(['section_id' => self::SECTION_FULL_MESSAGE]);

        $this->assertFalse(
            Enrollment::where('student_id', $student->id)
                ->where('section_id', $fullSection->id)
                ->exists()
        );
    }

    public function test_promote_rejects_promotion_when_target_section_is_full(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $academicPeriod = AcademicPeriod::factory()->promotable()->create();
        $sourceSection = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);
        $targetSection = Section::factory()->withCapacity(1)->create(['academic_period_id' => $academicPeriod->id]);

        Student::factory()->inSection($targetSection)->create();

        $student = Student::factory()->inSection($sourceSection)->create();
        $enrollment = $student->enrollments()->where('section_id', $sourceSection->id)->first();

        $response = $this->actingAs($supervisor)->patch(
            route('enrollments.promote', $enrollment),
            ['section_id' => $targetSection->id]
        );

        $response->assertSessionHasErrors(['section_id' => self::SECTION_FULL_MESSAGE]);

        $enrollment->refresh();
        $this->assertEquals(EnrollmentStatus::Active->value, $enrollment->status);

        $this->assertFalse(
            Enrollment::where('student_id', $student->id)
                ->where('section_id', $targetSection->id)
                ->exists()
        );
    }

    public function test_store_allows_enrollment_when_section_has_remaining_capacity(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $section = Section::factory()->withCapacity(2)->create();
        Student::factory()->inSection($section)->create();

        $student = Student::factory()->create();

        $response = $this->actingAs($supervisor)->post(
            route('students.enrollments.store', $student),
            ['section_id' => $section->id]
        );

        $response->assertRedirect(route('students.show', $student));
        $response->assertSessionHas('success');

        $this->assertTrue(
            Enrollment::where('student_id', $student->id)
                ->where('section_id', $section->id)
                ->where('status', EnrollmentStatus::Active->value)
                ->exists()
        );
    }

    public function test_store_allows_enrollment_when_capacity_is_null(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $section = Section::factory()->create(['capacity' => null]);
        Student::factory()->inSection($section)->create();

        $student = Student::factory()->create();

        $response = $this->actingAs($supervisor)->post(
            route('students.enrollments.store', $student),
            ['section_id' => $section->id]
        );

        $response->assertRedirect(route('students.show', $student));
        $response->assertSessionHas('success');

        $this->assertTrue(
            Enrollment::where('student_id', $student->id)
                ->where('section_id', $section->id)
                ->exists()
        );
    }

    public function test_store_ignores_non_active_enrollments_when_counting_occupancy(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $section = Section::factory()->withCapacity(1)->create();

        $withdrawnStudent = Student::factory()->inSection($section)->create();
        $withdrawnStudent->enrollments()
            ->where('section_id', $section->id)
            ->update(['status' => EnrollmentStatus::Withdrawn->value]);

        $student = Student::factory()->create();

        $response = $this->actingAs($supervisor)->post(
            route('students.enrollments.store', $student),
            ['section_id' => $section->id]
        );

        $response->assertRedirect(route('students.show', $student));
        $response->assertSessionHas('success');

        $this->assertTrue(
            Enrollment::where('student_id', $student->id)
                ->where('section_id', $section->id)
                ->exists()
        );
    }
}
