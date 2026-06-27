<?php

namespace App\Domains\Identity\Services\RoleManagement;

use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Models\User;
use App\Domains\Identity\Services\Users\RoleAssignmentService;
use Spatie\Permission\Models\Role as SpatieRole;

class AvailableRolesService
{
    public function __construct(
        private RoleAssignmentService $roleAssignmentService,
        private RoleRequirementsService $roleRequirements
    ) {}

    public function forUser(User $user, User $currentUser): array
    {
        $currentRoles = $user->roles->pluck('name')->toArray();

        return $this->roleAssignmentService
            ->getAssignableRolesForAdditionalAssignment($currentUser)
            ->reject(fn (SpatieRole $spatieRole) => in_array($spatieRole->name, $currentRoles))
            ->map(fn (SpatieRole $spatieRole) => $this->describe($user, Role::from($spatieRole->name)))
            ->values()
            ->toArray();
    }

    private function describe(User $user, Role $role): array
    {
        return [
            'role' => $role,
            'label' => $role->value,
            'description' => $this->descriptionFor($role),
            'needs_form' => $this->roleRequirements->roleNeedsForm($user, $role),
        ];
    }

    private function descriptionFor(Role $role): string
    {
        return match ($role) {
            Role::Supervisor => 'Rol administrativo superior',
            Role::Admin => 'Rol administrativo',
            Role::Teacher => 'Se creará perfil de profesor',
            Role::Representative => 'Se creará perfil de representante',
        };
    }
}
