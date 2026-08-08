<?php

namespace Tests\Feature\Enrollments;

use App\Domains\Academics\Exceptions\AcademicPeriodNotPromotableException;
use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Exceptions\EnrollmentNotActiveException;
use App\Domains\Enrollments\Exceptions\SectionOutsideAcademicPeriodException;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Enrollments\Services\PromoteEnrollmentService;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The service is called directly, without HTTP: that is the path the Form
 * Request never covered, and the one a job or a console command would take.
 */
class EnrollmentPromotionRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_promote_rejects_a_target_section_outside_the_current_period(): void
    {
        $academicPeriod = AcademicPeriod::factory()->promotable()->create();
        $sourceSection = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);
        $foreignSection = Section::factory()->create();

        $student = Student::factory()->inSection($sourceSection)->create();
        $enrollment = $student->enrollments()->where('section_id', $sourceSection->id)->first();

        try {
            app(PromoteEnrollmentService::class)->handle($enrollment, $foreignSection->id);

            $this->fail('Expected SectionOutsideAcademicPeriodException was not thrown.');
        } catch (SectionOutsideAcademicPeriodException $e) {
            $this->assertSame(422, $e->statusCode());
            $this->assertSame('SECTION_OUTSIDE_ACADEMIC_PERIOD', $e->errorCode());
            $this->assertStringContainsString($foreignSection->name, $e->getMessage());
        }

        $enrollment->refresh();
        $this->assertEquals(EnrollmentStatus::Active->value, $enrollment->status);

        $this->assertFalse(
            Enrollment::where('student_id', $student->id)
                ->where('section_id', $foreignSection->id)
                ->exists()
        );
    }

    /**
     * Promoting an already promoted enrollment would leave the student holding
     * two active ones in the same period — the very invariant StoreEnrollment
     * Service now refuses to break. The same rule has to answer at both doors.
     */
    public function test_promote_rejects_an_enrollment_that_is_no_longer_active(): void
    {
        $academicPeriod = AcademicPeriod::factory()->promotable()->create();
        $sourceSection = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);
        $firstTarget = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);
        $secondTarget = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $student = Student::factory()->inSection($sourceSection)->create();
        $enrollment = $student->enrollments()->where('section_id', $sourceSection->id)->first();

        app(PromoteEnrollmentService::class)->handle($enrollment, $firstTarget->id);

        try {
            app(PromoteEnrollmentService::class)->handle($enrollment->refresh(), $secondTarget->id);

            $this->fail('Expected EnrollmentNotActiveException was not thrown.');
        } catch (EnrollmentNotActiveException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('ENROLLMENT_NOT_ACTIVE', $e->errorCode());
        }

        $this->assertSame(
            1,
            Enrollment::where('student_id', $student->id)
                ->where('status', EnrollmentStatus::Active->value)
                ->count()
        );
    }

    public function test_promote_rejects_a_period_that_does_not_allow_promotions(): void
    {
        $academicPeriod = AcademicPeriod::factory()->create(['is_promotable' => false]);
        $sourceSection = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);
        $targetSection = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $student = Student::factory()->inSection($sourceSection)->create();
        $enrollment = $student->enrollments()->where('section_id', $sourceSection->id)->first();

        try {
            app(PromoteEnrollmentService::class)->handle($enrollment, $targetSection->id);

            $this->fail('Expected AcademicPeriodNotPromotableException was not thrown.');
        } catch (AcademicPeriodNotPromotableException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('ACADEMIC_PERIOD_NOT_PROMOTABLE', $e->errorCode());
            $this->assertStringContainsString($academicPeriod->name, $e->getMessage());
        }

        $enrollment->refresh();
        $this->assertEquals(EnrollmentStatus::Active->value, $enrollment->status);
    }
}
