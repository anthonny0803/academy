<?php

namespace Tests\Feature\Identity;

use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementAssignAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private const MANAGE_HIGHER_ROLE_ERROR = 'No tienes autorización para gestionar roles de este usuario.';

    private const UNASSIGNABLE_ROLE_ERROR = 'No tienes autorización para asignar este rol.';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::Admin->value);

        return $admin;
    }

    private function createSupervisor(): User
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole(Role::Supervisor->value);

        return $supervisor;
    }

    public function test_admin_cannot_self_promote_to_supervisor(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('role-management.assign', [
            'user' => $admin,
            'role' => Role::Supervisor->value,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('error', self::MANAGE_HIGHER_ROLE_ERROR);

        $admin->refresh();
        $this->assertTrue($admin->hasRole(Role::Admin->value));
        $this->assertFalse($admin->hasRole(Role::Supervisor->value));
    }

    public function test_admin_cannot_promote_another_user_to_supervisor(): void
    {
        $admin = $this->createAdmin();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('role-management.assign', [
            'user' => $targetUser,
            'role' => Role::Supervisor->value,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('error', self::UNASSIGNABLE_ROLE_ERROR);

        $this->assertFalse($targetUser->fresh()->hasRole(Role::Supervisor->value));
    }

    public function test_admin_cannot_demote_a_supervisor_by_assigning_admin(): void
    {
        $admin = $this->createAdmin();
        $supervisor = $this->createSupervisor();

        $response = $this->actingAs($admin)->post(route('role-management.assign', [
            'user' => $supervisor,
            'role' => Role::Admin->value,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('error', self::MANAGE_HIGHER_ROLE_ERROR);

        $supervisor->refresh();
        $this->assertTrue($supervisor->hasRole(Role::Supervisor->value));
        $this->assertFalse($supervisor->hasRole(Role::Admin->value));
    }

    public function test_supervisor_cannot_promote_another_user_to_supervisor(): void
    {
        $supervisor = $this->createSupervisor();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($supervisor)->post(route('role-management.assign', [
            'user' => $targetUser,
            'role' => Role::Supervisor->value,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('error', self::UNASSIGNABLE_ROLE_ERROR);

        $this->assertFalse($targetUser->fresh()->hasRole(Role::Supervisor->value));
    }

    public function test_admin_can_assign_teacher_to_a_plain_user(): void
    {
        $admin = $this->createAdmin();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('role-management.assign', [
            'user' => $targetUser,
            'role' => Role::Teacher->value,
        ]));

        $response->assertRedirect(route('role-management.show-assign-options', $targetUser));
        $response->assertSessionHas('success');

        $this->assertTrue($targetUser->fresh()->hasRole(Role::Teacher->value));
    }

    public function test_student_role_is_denied_at_authorization(): void
    {
        $supervisor = $this->createSupervisor();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($supervisor)->post(route('role-management.assign', [
            'user' => $targetUser,
            'role' => Role::Student->value,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('error', self::UNASSIGNABLE_ROLE_ERROR);

        $this->assertFalse($targetUser->fresh()->hasRole(Role::Student->value));
    }
}
