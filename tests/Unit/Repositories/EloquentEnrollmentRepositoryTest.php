<?php

namespace Tests\Unit\Repositories;

use App\Domains\Academics\Models\Section;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Enrollments\Repositories\EloquentEnrollmentRepository;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentEnrollmentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EnrollmentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->repository = app(EnrollmentRepository::class);
    }

    public function test_binding_resolves_to_eloquent_implementation(): void
    {
        $this->assertInstanceOf(EloquentEnrollmentRepository::class, $this->repository);
    }

    public function test_create_persists_an_enrollment(): void
    {
        $student = Student::factory()->create();
        $section = Section::factory()->create();

        $enrollment = $this->repository->create([
            'student_id' => $student->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Active->value,
        ]);

        $this->assertInstanceOf(Enrollment::class, $enrollment);
        $this->assertDatabaseHas('enrollments', ['id' => $enrollment->id]);
    }

    public function test_update_changes_status(): void
    {
        $enrollment = Enrollment::factory()->create();

        $this->repository->update($enrollment, ['status' => EnrollmentStatus::Completed->value]);

        $this->assertSame(EnrollmentStatus::Completed->value, $enrollment->fresh()->status);
    }

    public function test_delete_removes_the_enrollment(): void
    {
        $enrollment = Enrollment::factory()->create();

        $this->repository->delete($enrollment);

        $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id]);
    }

    public function test_find_or_fail_returns_the_enrollment(): void
    {
        $enrollment = Enrollment::factory()->create();

        $this->assertTrue($this->repository->findOrFail($enrollment->id)->is($enrollment));
    }

    public function test_paginate_for_listing_matches_by_search(): void
    {
        $user = User::factory()->create(['name' => 'Carlos']);
        $student = Student::factory()->create(['user_id' => $user->id]);
        $enrollment = Enrollment::factory()->create(['student_id' => $student->id]);

        $result = $this->repository->paginateForListing('Carlos', null, null, null, 50);

        $this->assertTrue($result->contains($enrollment));
    }

    public function test_paginate_for_listing_filters_by_status(): void
    {
        $active = Enrollment::factory()->create();
        $withdrawn = Enrollment::factory()->withdrawn()->create();

        $result = $this->repository->paginateForListing('', EnrollmentStatus::Withdrawn->value, null, null, 50);

        $this->assertTrue($result->contains($withdrawn));
        $this->assertFalse($result->contains($active));
    }

    public function test_has_active_enrollment_in_period(): void
    {
        $section = Section::factory()->create();
        $student = Student::factory()->create();
        Enrollment::factory()->create(['student_id' => $student->id, 'section_id' => $section->id]);

        $otherSection = Section::factory()->create();

        $this->assertTrue($this->repository->hasActiveEnrollmentInPeriod($student->id, $section->academic_period_id));
        $this->assertFalse($this->repository->hasActiveEnrollmentInPeriod($student->id, $otherSection->academic_period_id));
    }

    public function test_student_ids_for_active_period(): void
    {
        $section = Section::factory()->create();
        $student = Student::factory()->create();
        Enrollment::factory()->create(['student_id' => $student->id, 'section_id' => $section->id]);

        $ids = $this->repository->studentIdsForActivePeriod($section->academic_period_id);

        $this->assertTrue($ids->contains($student->id));
    }
}
