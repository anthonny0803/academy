<?php

namespace App\Services\Users;

use App\Enums\Role as EnumRole;
use Spatie\Permission\Models\Role as SpatieRole;

class RoleAssignmentService
{
    // Retrieves roles that the current user can assign to others in user creation or modification

    public function getAssignableRoles($user)
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

    public function getAssignableRolesForAdditionalAssignment($user)
    {
        if ($user->isDeveloper()) {
            return SpatieRole::whereIn('name', EnumRole::assignableByDeveloperForAdditionalRoles())->get();
        }

        if ($user->isSupervisor()) {
            return SpatieRole::whereIn('name', EnumRole::assignableBySupervisorForAdditionalRoles())->get();
        }

        if ($user->isAdmin()) {
            return SpatieRole::whereIn('name', EnumRole::assignableByAdminForAdditionalRoles())->get();
        }

        return collect();
    }
}
