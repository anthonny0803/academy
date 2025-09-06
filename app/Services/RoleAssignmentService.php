<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RoleAssignmentService
{
    public function getAssignableRoles($user)
    {
        if ($user->id === 1) {
            return Role::whereNotIn('name', ['Representante', 'Estudiante'])->get();
        }

        if ($user->hasRole('SuperAdmin')) {
            return Role::whereNotIn('name', ['SuperAdmin', 'Representante', 'Estudiante'])->get();
        }

        if ($user->hasRole('Administrador')) {
            return Role::whereNotIn('name', ['SuperAdmin', 'Administrador', 'Representante', 'Estudiante'])->get();
        }

        return collect();
    }
}
