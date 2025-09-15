<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RoleAssignmentService
{
    /**
     * Get assignable roles based on the current user's role.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Support\Collection
     */
    public function getAssignableRoles($user)
    {
        if ($user->id === 1) {
            return Role::whereNotIn('name', ['Representante', 'Estudiante'])->get();
        }

        if ($user->hasRole('Supervisor')) {
            return Role::whereNotIn('name', ['Supervisor', 'Representante', 'Estudiante'])->get();
        }

        if ($user->hasRole('Administrador')) {
            return Role::whereNotIn('name', ['Supervisor', 'Administrador', 'Representante', 'Estudiante'])->get();
        }

        return collect();
    }
}
