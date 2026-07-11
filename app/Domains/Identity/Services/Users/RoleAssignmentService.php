<?php

namespace App\Domains\Identity\Services\Users;

use App\Domains\Identity\Enums\Role as EnumRole;
use App\Domains\Identity\Models\User;
use Spatie\Permission\Models\Role as SpatieRole;

class RoleAssignmentService
{
    // Retrieves roles that the current user can assign to others in user creation or modification

    public function getAssignableRoles(User $user)
    {
        if ($user->isDeveloper()) {
            return SpatieRole::whereIn('name', EnumRole::assignableByDeveloper())->get();
        }

        if ($user->isSupervisor()) {
            return SpatieRole::whereIn('name', EnumRole::assignableBySupervisor())->get();
        }

        return collect();
    }

    // Retrieves roles that the current user can assign to others in additional role assignments

    public function getAssignableRolesForAdditionalAssignment(User $user)
    {
        $assignableRoles = $user->assignableRolesForAdditionalAssignment();

        if (empty($assignableRoles)) {
            return collect();
        }

        return SpatieRole::whereIn('name', $assignableRoles)->get();
    }
}
