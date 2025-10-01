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

            $submittedRole = $data['role'] ?? null;
            $rolesToPreserve = $user->getRoleNames()
                ->filter(fn($role) => in_array($role, ['Representante', 'Estudiante', 'Profesor']))
                ->toArray();

            $rolesToSync = $submittedRole
                ? array_merge([$submittedRole], $rolesToPreserve)
                : $rolesToPreserve;

            $user->syncRoles($rolesToSync);

            return $user;
        });
    }
}
