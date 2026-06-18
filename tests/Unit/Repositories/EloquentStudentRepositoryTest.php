<?php

namespace Tests\Unit\Repositories;

use App\Domains\Academics\Models\Section;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Enums\RelationshipType;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Students\Enums\StudentSituation;
use App\Domains\Students\Models\Student;
use App\Domains\Students\Repositories\EloquentStudentRepository;
use App\Domains\Students\Repositories\StudentRepository;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentStudentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private StudentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->repository = app(StudentRepository::class);
    }

    public function test_binding_resolves_to_eloquent_implementation(): void
    {
        $this->assertInstanceOf(EloquentStudentRepository::class, $this->repository);
    }

    public function test_create_persists_a_student(): void
    {
        $user = User::factory()->create();
        $representative = Representative::factory()->create();

        $student = $this->repository->create([
            'user_id' => $user->id,
            'representative_id' => $representative->id,
            'student_code' => 'ADULT000999',
            'relationship_type' => RelationshipType::Father->value,
            'situation' => StudentSituation::Active,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Student::class, $student);
        $this->assertDatabaseHas('students', ['student_code' => 'ADULT000999']);
    }

    public function test_update_changes_attributes(): void
    {
        $student = Student::factory()->create();

        $this->repository->update($student, ['situation' => StudentSituation::Inactive]);

        $this->assertSame(StudentSituation::Inactive, $student->fresh()->situation);
    }

    public function test_last_code_for_prefix_returns_highest(): void
    {
        Student::factory()->create(['student_code' => 'ADULT000001']);
        Student::factory()->create(['student_code' => 'ADULT000005']);
        Student::factory()->child()->create(['student_code' => 'CHILD000003']);

        $this->assertSame('ADULT000005', $this->repository->lastCodeForPrefix('ADULT'));
        $this->assertSame('CHILD000003', $this->repository->lastCodeForPrefix('CHILD'));
    }

    public function test_last_code_for_prefix_returns_null_when_none(): void
    {
        $this->assertNull($this->repository->lastCodeForPrefix('ADULT'));
    }

    public function test_paginate_for_listing_matches_by_search(): void
    {
        $user = User::factory()->create(['name' => 'Carlos']);
        $student = Student::factory()->create(['user_id' => $user->id]);

        $result = $this->repository->paginateForListing('Carlos', null, null, null, 10);

        $this->assertTrue($result->contains($student));
    }

    public function test_paginate_for_listing_filters_by_section(): void
    {
        $section = Section::factory()->create();

        $user = User::factory()->create(['name' => 'Carlos']);
        $student = Student::factory()->inSection($section)->create(['user_id' => $user->id]);

        $otherUser = User::factory()->create(['name' => 'Carlos']);
        $otherStudent = Student::factory()->create(['user_id' => $otherUser->id]);

        $result = $this->repository->paginateForListing('Carlos', null, null, $section->id, 10);

        $this->assertTrue($result->contains($student));
        $this->assertFalse($result->contains($otherStudent));
    }

    public function test_representative_ids_for_returns_representative_ids(): void
    {
        $representative = Representative::factory()->create();
        $student = Student::factory()->create(['representative_id' => $representative->id]);

        $ids = $this->repository->representativeIdsFor([$student->id]);

        $this->assertTrue($ids->contains($representative->id));
    }

    public function test_deactivate_without_active_enrollments(): void
    {
        $student = Student::factory()->create();
        $student->enrollments()->update(['status' => EnrollmentStatus::Withdrawn->value]);

        $count = $this->repository->deactivateWithoutActiveEnrollments([$student->id]);

        $fresh = $student->fresh();
        $this->assertSame(1, $count);
        $this->assertFalse($fresh->is_active);
        $this->assertSame(StudentSituation::Inactive, $fresh->situation);
    }

    public function test_deactivate_skips_students_with_active_enrollments(): void
    {
        $student = Student::factory()->create();

        $count = $this->repository->deactivateWithoutActiveEnrollments([$student->id]);

        $this->assertSame(0, $count);
        $this->assertTrue($student->fresh()->is_active);
    }
}
