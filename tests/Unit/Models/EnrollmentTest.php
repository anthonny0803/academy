<?php

namespace Tests\Unit\Models;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use PHPUnit\Framework\TestCase;

class EnrollmentTest extends TestCase
{
    private function makeEnrollment(string $status, ?bool $passed = null): Enrollment
    {
        return new Enrollment([
            'student_id' => 1,
            'section_id' => 1,
            'status' => $status,
            'passed' => $passed,
        ]);
    }

    public function test_is_active(): void
    {
        $enrollment = $this->makeEnrollment(EnrollmentStatus::Active->value);

        $this->assertTrue($enrollment->isActive());
        $this->assertFalse($enrollment->isCompleted());
        $this->assertFalse($enrollment->isWithdrawn());
        $this->assertFalse($enrollment->isTransferred());
        $this->assertFalse($enrollment->isPromoted());
    }

    public function test_is_completed(): void
    {
        $enrollment = $this->makeEnrollment(EnrollmentStatus::Completed->value);

        $this->assertTrue($enrollment->isCompleted());
        $this->assertFalse($enrollment->isActive());
    }

    public function test_is_withdrawn(): void
    {
        $enrollment = $this->makeEnrollment(EnrollmentStatus::Withdrawn->value);

        $this->assertTrue($enrollment->isWithdrawn());
        $this->assertFalse($enrollment->isActive());
    }

    public function test_is_transferred(): void
    {
        $enrollment = $this->makeEnrollment(EnrollmentStatus::Transferred->value);

        $this->assertTrue($enrollment->isTransferred());
        $this->assertFalse($enrollment->isActive());
    }

    public function test_is_promoted(): void
    {
        $enrollment = $this->makeEnrollment(EnrollmentStatus::Promoted->value);

        $this->assertTrue($enrollment->isPromoted());
        $this->assertFalse($enrollment->isActive());
    }

    public function test_has_passed_returns_true(): void
    {
        $enrollment = $this->makeEnrollment(EnrollmentStatus::Completed->value, true);

        $this->assertTrue($enrollment->hasPassed());
    }

    public function test_has_passed_returns_false(): void
    {
        $enrollment = $this->makeEnrollment(EnrollmentStatus::Completed->value, false);

        $this->assertFalse($enrollment->hasPassed());
    }

    public function test_has_passed_returns_null_when_not_set(): void
    {
        $enrollment = $this->makeEnrollment(EnrollmentStatus::Active->value);

        $this->assertNull($enrollment->hasPassed());
    }

    public function test_entity_name(): void
    {
        $enrollment = new Enrollment();

        $this->assertSame('Inscripción', $enrollment->getEntityName());
    }
}
