<?php

namespace Tests\Unit\Models;

use App\Domains\Academics\Models\Section;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_is_full_returns_true_when_active_enrollments_reach_capacity(): void
    {
        $section = Section::factory()->withCapacity(1)->create();
        Student::factory()->inSection($section)->create();

        $this->assertTrue($section->isFull());
    }

    public function test_is_full_returns_false_below_capacity(): void
    {
        $section = Section::factory()->withCapacity(2)->create();
        Student::factory()->inSection($section)->create();

        $this->assertFalse($section->isFull());
    }

    public function test_is_full_returns_false_when_capacity_is_null(): void
    {
        $section = Section::factory()->create(['capacity' => null]);
        Student::factory()->inSection($section)->create();

        $this->assertFalse($section->isFull());
    }

    public function test_is_full_ignores_non_active_enrollments(): void
    {
        $section = Section::factory()->withCapacity(1)->create();

        $student = Student::factory()->inSection($section)->create();
        $student->enrollments()
            ->where('section_id', $section->id)
            ->update(['status' => EnrollmentStatus::Withdrawn->value]);

        $this->assertFalse($section->isFull());
    }
}
