<?php

namespace Tests\Feature\Identity;

use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementSelfDemoteTest extends TestCase
{
    use RefreshDatabase;

    private const SELF_DEMOTE_ERROR = 'No puedes degradar tu propio rol administrativo.';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function createSupervisor(): User
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole(Role::Supervisor->value);

        return $supervisor;
    }

    public function test_supervisor_cannot_self_demote_to_admin_via_assign_route(): void
    {
        $supervisor = $this->createSupervisor();

        $response = $this->actingAs($supervisor)->post(route('role-management.assign', [
            'user' => $supervisor,
            'role' => Role::Admin->value,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('error', self::SELF_DEMOTE_ERROR);

        $supervisor->refresh();
        $this->assertTrue($supervisor->hasRole(Role::Supervisor->value));
        $this->assertFalse($supervisor->hasRole(Role::Admin->value));
    }

    public function test_supervisor_cannot_open_the_assign_form_to_self_demote(): void
    {
        $supervisor = $this->createSupervisor();

        $response = $this->actingAs($supervisor)->get(route('role-management.show-form', [
            'user' => $supervisor,
            'role' => Role::Admin->value,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('error', self::SELF_DEMOTE_ERROR);

        $supervisor->refresh();
        $this->assertTrue($supervisor->hasRole(Role::Supervisor->value));
        $this->assertFalse($supervisor->hasRole(Role::Admin->value));
    }

    public function test_supervisor_can_assign_admin_to_another_user(): void
    {
        $supervisor = $this->createSupervisor();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($supervisor)->post(route('role-management.assign', [
            'user' => $targetUser,
            'role' => Role::Admin->value,
        ]));

        $response->assertRedirect(route('role-management.show-assign-options', $targetUser));
        $response->assertSessionHas('success');

        $this->assertTrue($targetUser->fresh()->hasRole(Role::Admin->value));
    }
}
