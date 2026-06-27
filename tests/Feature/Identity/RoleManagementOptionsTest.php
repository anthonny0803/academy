<?php

namespace Tests\Feature\Identity;

use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementOptionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_assign_options_lists_available_roles_excluding_already_assigned(): void
    {
        $developer = User::factory()->developer()->create();
        $user = User::factory()->create();
        $user->assignRole(Role::Admin->value);

        $response = $this->actingAs($developer)
            ->get(route('role-management.show-assign-options', $user));

        $response->assertOk();
        $response->assertViewIs('role-management.assign-options');
        $response->assertViewHas('availableRoles', function (array $roles) {
            $labels = array_column($roles, 'label');

            return ! in_array(Role::Admin->value, $labels, true)
                && in_array(Role::Supervisor->value, $labels, true)
                && in_array(Role::Teacher->value, $labels, true)
                && in_array(Role::Representative->value, $labels, true);
        });
    }

    public function test_show_form_displays_missing_fields_for_incomplete_representative(): void
    {
        $developer = User::factory()->developer()->create();
        $user = User::factory()->create([
            'document_id' => null,
            'birth_date' => null,
            'phone' => null,
            'address' => null,
        ]);

        $response = $this->actingAs($developer)->get(route('role-management.show-form', [
            'user' => $user,
            'role' => Role::Representative->value,
        ]));

        $response->assertOk();
        $response->assertViewIs('role-management.assign-form');
        $response->assertViewHas('missingFields', function (array $missingFields) {
            sort($missingFields);

            return $missingFields === ['address', 'birth_date', 'document_id', 'phone'];
        });
    }

    public function test_show_form_assigns_directly_when_no_fields_are_missing(): void
    {
        $developer = User::factory()->developer()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($developer)->get(route('role-management.show-form', [
            'user' => $user,
            'role' => Role::Admin->value,
        ]));

        $response->assertRedirect(route('role-management.show-assign-options', $user));
        $this->assertTrue($user->fresh()->hasRole(Role::Admin->value));
    }

    public function test_show_form_aborts_404_for_invalid_role(): void
    {
        $developer = User::factory()->developer()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($developer)->get(route('role-management.show-form', [
            'user' => $user,
            'role' => 'InvalidRole',
        ]));

        $response->assertNotFound();
    }

    public function test_assign_aborts_404_for_invalid_role(): void
    {
        $developer = User::factory()->developer()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($developer)->post(route('role-management.assign', [
            'user' => $user,
            'role' => 'InvalidRole',
        ]));

        $response->assertNotFound();
    }
}
