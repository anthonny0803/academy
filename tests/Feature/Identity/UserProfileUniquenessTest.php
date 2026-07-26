<?php

namespace Tests\Feature\Identity;

use App\Domains\Academics\Models\Teacher;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Students\Models\Student;
use App\Domains\Tenancy\Models\Tenant;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The database is what guarantees the HasOne profile relations on User resolve
 * to a single record; the exists() checks in the services are check-then-act
 * and cannot serialize concurrent requests.
 *
 * Postgres aborts the surrounding transaction on a constraint violation, so
 * every expectException is the last statement of its test.
 */
class UserProfileUniquenessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_a_user_cannot_hold_two_teacher_profiles(): void
    {
        $teacher = Teacher::factory()->create();

        $this->expectException(UniqueConstraintViolationException::class);

        Teacher::factory()->create(['user_id' => $teacher->user_id]);
    }

    public function test_a_user_cannot_hold_two_representative_profiles(): void
    {
        $representative = Representative::factory()->create();

        $this->expectException(UniqueConstraintViolationException::class);

        Representative::factory()->create(['user_id' => $representative->user_id]);
    }

    public function test_a_user_cannot_hold_two_student_profiles(): void
    {
        $student = Student::factory()->create();

        $this->expectException(UniqueConstraintViolationException::class);

        Student::factory()->create(['user_id' => $student->user_id]);
    }

    public function test_the_profile_is_unique_across_tenants(): void
    {
        // The index is global on purpose: a user belongs to a single tenant, so
        // no second tenant may grant them another profile of the same type.
        $teacher = Teacher::factory()->create();
        $userId = $teacher->user_id;
        $otherTenant = Tenant::factory()->create();

        $this->expectException(UniqueConstraintViolationException::class);

        $this->withinTenant(
            $otherTenant,
            fn () => Teacher::factory()->create(['user_id' => $userId])
        );
    }

    public function test_a_user_can_hold_one_profile_of_each_type(): void
    {
        $user = User::factory()->create();

        Teacher::factory()->create(['user_id' => $user->id]);
        Representative::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->teacher()->exists());
        $this->assertTrue($user->representative()->exists());
    }
}
