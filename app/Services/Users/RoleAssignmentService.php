<?php

namespace App\Services\Users;

use App\Enums\Role as EnumRole;
use Spatie\Permission\Models\Role as SpatieRole;

class RoleAssignmentService
{
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
}
