<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
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

            // Update basic user info
            $user->update([
                'name' => strtoupper($data['name']),
                'last_name' => strtoupper($data['last_name']),
                'email' => strtolower($data['email']),
                'sex' => $data['sex'],
            ]);

            // Update roles if provided
            if (!empty($data['roles'])) {
                $editableRoles = Role::whereIn('name', $data['roles'])->pluck('name')->toArray();

                // Keep non-editable roles intact
                $nonEditableRoles = $user->roles
                    ->whereNotIn('name', ['SuperAdmin', 'Administrador', 'Profesor'])
                    ->pluck('name')
                    ->toArray();

                $user->syncRoles(array_merge($editableRoles, $nonEditableRoles));
            }

            // Update activation status based on roles
            $employeeRoles = ['SuperAdmin', 'Administrador', 'Profesor'];
            $user->is_active = $user->roles->pluck('name')->intersect($employeeRoles)->isNotEmpty();

            $user->save();

            return $user;
        });
    }
}
