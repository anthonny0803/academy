<?php

namespace Tests\Feature\Enrollments;

use App\Domains\Academics\Exceptions\SectionFullException;
use App\Domains\Academics\Models\Section;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Enrollments\Services\PromoteEnrollmentService;
use App\Domains\Enrollments\Services\StoreEnrollmentService;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Enums\RelationshipType;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Shared\Enums\Sex;
use App\Domains\Students\Models\Student;
use App\Domains\Students\Services\StoreStudentService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionCapacityGuardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_store_enrollment_service_throws_section_full_exception_when_section_is_full(): void
    {
        $fullSection = Section::factory()->withCapacity(1)->create();
        Student::factory()->inSection($fullSection)->create();

        $student = Student::factory()->inactive()->create();

        try {
            app(StoreEnrollmentService::class)->handle($student, ['section_id' => $fullSection->id]);

            $this->fail('Expected SectionFullException was not thrown.');
        } catch (SectionFullException $e) {
            $this->assertSame(422, $e->statusCode());
            $this->assertSame('SECTION_FULL', $e->errorCode());
            $this->assertSame(
                "La sección '{$fullSection->name}' ha alcanzado su capacidad máxima.",
                $e->getMessage()
            );
        }

        $this->assertFalse(
            Enrollment::where('student_id', $student->id)
                ->where('section_id', $fullSection->id)
                ->exists()
        );

        $student->refresh();
        $this->assertFalse($student->is_active);
    }

    public function test_promote_enrollment_service_throws_section_full_exception_when_target_section_is_full(): void
    {
        $sourceSection = Section::factory()->create();
        $targetSection = Section::factory()->withCapacity(1)->create();
        Student::factory()->inSection($targetSection)->create();

        $student = Student::factory()->inSection($sourceSection)->create();
        $enrollment = $student->enrollments()->where('section_id', $sourceSection->id)->first();

        try {
            app(PromoteEnrollmentService::class)->handle($enrollment, $targetSection->id);

            $this->fail('Expected SectionFullException was not thrown.');
        } catch (SectionFullException $e) {
            $this->assertSame('SECTION_FULL', $e->errorCode());
        }

        $enrollment->refresh();
        $this->assertEquals(EnrollmentStatus::Active->value, $enrollment->status);

        $this->assertFalse(
            Enrollment::where('student_id', $student->id)
                ->where('section_id', $targetSection->id)
                ->exists()
        );
    }

    public function test_store_student_service_throws_section_full_exception_when_section_is_full(): void
    {
        $fullSection = Section::factory()->withCapacity(1)->create();
        Student::factory()->inSection($fullSection)->create();

        $representative = Representative::factory()->create();

        $usersBefore = User::count();
        $studentsBefore = Student::count();

        try {
            app(StoreStudentService::class)->handle($representative, [
                'name' => 'Pedro',
                'last_name' => 'Pérez',
                'sex' => Sex::Male->value,
                'birth_date' => '2015-03-10',
                'relationship_type' => RelationshipType::Father->value,
                'section_id' => $fullSection->id,
            ]);

            $this->fail('Expected SectionFullException was not thrown.');
        } catch (SectionFullException $e) {
            $this->assertSame('SECTION_FULL', $e->errorCode());
        }

        $this->assertSame($usersBefore, User::count());
        $this->assertSame($studentsBefore, Student::count());
    }

    public function test_store_enrollment_service_allows_section_with_null_capacity(): void
    {
        $section = Section::factory()->create(['capacity' => null]);
        Student::factory()->inSection($section)->create();

        $student = Student::factory()->create();

        $enrollment = app(StoreEnrollmentService::class)->handle($student, ['section_id' => $section->id]);

        $this->assertSame($section->id, $enrollment->section_id);
        $this->assertEquals(EnrollmentStatus::Active->value, $enrollment->status);
    }
}
