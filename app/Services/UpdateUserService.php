<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateUserService
{
    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {

            $user->update([
                'name'      => strtoupper($data['name']),
                'last_name' => strtoupper($data['last_name']),
                'email'     => strtolower($data['email']),
                'sex'       => $data['sex'],
            ]);

            $submittedRoles = $data['roles'] ?? [];
            $clientRoles = ['Representante', 'Estudiante'];
            $rolesToPreserve = $user->getRoleNames()->intersect($clientRoles)->toArray();
            $rolesToSync = array_merge($submittedRoles, $rolesToPreserve);
            $user->syncRoles($rolesToSync);

            $employeeRoles = ['Supervisor', 'Administrador', 'Profesor'];
            $user->is_active = $user->hasAnyRole($employeeRoles);
            $user->save();

            return $user;
        });
    }
}
