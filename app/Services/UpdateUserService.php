<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateUserService
{
    /**
     * Update an existing user with new data and roles.
     *
     * @param User  $user
     * @param array $data
     * @return User
     */
    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {

            $user->update([
                'name'      => strtoupper($data['name']),
                'last_name' => strtoupper($data['last_name']),
                'email'     => strtolower($data['email']),
                'sex'       => $data['sex'],
            ]);

            // Sent roles from the form.
            $submittedRoles = $data['roles'] ?? [];

            // Client roles.
            $clientRoles = ['Representante', 'Estudiante'];

            // Get client roles to preserve.
            $rolesToPreserve = $user->getRoleNames()->intersect($clientRoles)->toArray();

            // Combine roles
            $rolesToSync = array_merge($submittedRoles, $rolesToPreserve);

            $user->syncRoles($rolesToSync);

            // If user has an employee role is active (as employee), else deactive.
            $employeeRoles = ['Supervisor', 'Administrador', 'Profesor'];
            $user->is_active = $user->hasAnyRole($employeeRoles);

            $user->save();

            return $user;
        });
    }
}
