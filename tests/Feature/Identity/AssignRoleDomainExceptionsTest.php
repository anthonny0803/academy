<?php

namespace Tests\Feature\Identity;

use App\Domains\Academics\Models\Teacher;
use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Exceptions\RoleAlreadyAssignedException;
use App\Domains\Identity\Exceptions\UnsupportedRoleAssignmentException;
use App\Domains\Identity\Models\User;
use App\Domains\Identity\Services\RoleManagement\AssignRoleService;
use App\Domains\Representatives\Models\Representative;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignRoleDomainExceptionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_service_throws_when_the_user_already_has_the_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Teacher->value);

        try {
            app(AssignRoleService::class)->handle($user, Role::Teacher, []);

            $this->fail('Expected RoleAlreadyAssignedException was not thrown.');
        } catch (RoleAlreadyAssignedException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('ROLE_ALREADY_ASSIGNED', $e->errorCode());
            $this->assertSame('El usuario ya tiene el rol Profesor', $e->getMessage());
        }
    }

    public function test_service_throws_for_an_unsupported_role(): void
    {
        $user = User::factory()->create();

        try {
            app(AssignRoleService::class)->handle($user, Role::Student, []);

            $this->fail('Expected UnsupportedRoleAssignmentException was not thrown.');
        } catch (UnsupportedRoleAssignmentException $e) {
            $this->assertSame(422, $e->statusCode());
            $this->assertSame('UNSUPPORTED_ROLE_ASSIGNMENT', $e->errorCode());
            $this->assertSame('Rol Estudiante no soportado para asignación', $e->getMessage());
        }
    }

    public function test_service_throws_when_the_user_already_has_a_teacher_profile(): void
    {
        $user = Teacher::factory()->create()->user;
        $user->removeRole(Role::Teacher->value);

        try {
            app(AssignRoleService::class)->handle($user, Role::Teacher, []);

            $this->fail('Expected RoleAlreadyAssignedException was not thrown.');
        } catch (RoleAlreadyAssignedException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('El usuario ya tiene un perfil de profesor', $e->getMessage());
        }
    }

    public function test_service_throws_when_the_user_already_has_a_representative_profile(): void
    {
        $user = Representative::factory()->create()->user;
        $user->removeRole(Role::Representative->value);

        try {
            app(AssignRoleService::class)->handle($user, Role::Representative, []);

            $this->fail('Expected RoleAlreadyAssignedException was not thrown.');
        } catch (RoleAlreadyAssignedException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('El usuario ya tiene un perfil de representante', $e->getMessage());
        }
    }

    public function test_web_assign_flashes_the_domain_error_and_redirects_back(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $target = User::factory()->create();
        $target->assignRole(Role::Teacher->value);

        $response = $this->actingAs($supervisor)
            ->from(route('role-management.show-assign-options', $target))
            ->post(route('role-management.assign', ['user' => $target, 'role' => Role::Teacher->value]));

        $response->assertRedirect(route('role-management.show-assign-options', $target));
        $response->assertSessionHas('error', 'El usuario ya tiene el rol Profesor');
    }
}
