<?php

namespace Tests\Feature\Enrollments;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Exceptions\StudentAlreadyEnrolledInPeriodException;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Enrollments\Services\StoreEnrollmentService;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The service is called directly, without HTTP: that is the path the Form
 * Request never covered, and the one a job or a console command would take.
 */
class EnrollmentPeriodInvariantsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_store_rejects_a_second_active_enrollment_in_the_same_period(): void
    {
        $academicPeriod = AcademicPeriod::factory()->create();
        $enrolledSection = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);
        $otherSection = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $student = Student::factory()->inSection($enrolledSection)->create();

        try {
            app(StoreEnrollmentService::class)->handle($student, ['section_id' => $otherSection->id]);

            $this->fail('Expected StudentAlreadyEnrolledInPeriodException was not thrown.');
        } catch (StudentAlreadyEnrolledInPeriodException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('STUDENT_ALREADY_ENROLLED_IN_PERIOD', $e->errorCode());
            $this->assertStringContainsString($academicPeriod->name, $e->getMessage());
        }

        $this->assertFalse(
            Enrollment::where('student_id', $student->id)
                ->where('section_id', $otherSection->id)
                ->exists()
        );
    }

    public function test_store_allows_an_enrollment_in_a_different_period(): void
    {
        $enrolledSection = Section::factory()->create();
        $otherPeriodSection = Section::factory()->create();

        $student = Student::factory()->inSection($enrolledSection)->create();

        $enrollment = app(StoreEnrollmentService::class)
            ->handle($student, ['section_id' => $otherPeriodSection->id]);

        $this->assertSame($otherPeriodSection->id, $enrollment->section_id);
        $this->assertEquals(EnrollmentStatus::Active->value, $enrollment->status);
    }

    public function test_store_ignores_a_non_active_enrollment_in_the_same_period(): void
    {
        $academicPeriod = AcademicPeriod::factory()->create();
        $withdrawnSection = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);
        $newSection = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $student = Student::factory()->inSection($withdrawnSection)->create();
        $student->enrollments()->update(['status' => EnrollmentStatus::Withdrawn->value]);

        $enrollment = app(StoreEnrollmentService::class)
            ->handle($student, ['section_id' => $newSection->id]);

        $this->assertSame($newSection->id, $enrollment->section_id);
    }

    /**
     * The service guard is defence in depth, not a replacement: the ordinary
     * path must still answer the Form Request's per-field 422, not the 409.
     */
    public function test_web_store_still_answers_the_form_request_error_for_the_same_period(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $academicPeriod = AcademicPeriod::factory()->create();
        $enrolledSection = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);
        $otherSection = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $student = Student::factory()->inSection($enrolledSection)->create();

        $response = $this->actingAs($supervisor)->post(
            route('students.enrollments.store', $student),
            ['section_id' => $otherSection->id]
        );

        $response->assertSessionHasErrors([
            'section_id' => 'El estudiante ya tiene una inscripción activa en este período académico.',
        ]);

        $this->assertFalse(
            Enrollment::where('student_id', $student->id)
                ->where('section_id', $otherSection->id)
                ->exists()
        );
    }
}
