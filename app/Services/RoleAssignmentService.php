<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RoleAssignmentService
{
    public function getAssignableRoles($user)
    {
        if ($user->isDeveloper()) {
            return Role::whereNotIn('name', ['Profesor', 'Representante', 'Estudiante'])->get();
        }

        if ($user->isSupervisor()) {
            return Role::whereNotIn('name', ['Supervisor', 'Profesor', 'Representante', 'Estudiante'])->get();
        }

        return collect();
    }
}
