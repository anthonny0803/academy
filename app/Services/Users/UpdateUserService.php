<?php

namespace App\Services\Users;

use App\Models\User;
use App\Enums\Role;
use Illuminate\Support\Facades\DB;

class UpdateUserService
{
    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name'      => $data['name'],
                'last_name' => $data['last_name'],
                'email'     => $data['email'],
                'sex'       => $data['sex'],
            ]);

            if (isset($data['role'])) {
                $this->updateAdministrativeRole($user, $data['role']);
            }

            return $user->fresh();
        });
    }

    private function updateAdministrativeRole(User $user, string $newAdministrativeRole): void
    {
        $profileRoles = $user->getRoleNames()
            ->filter(fn($role) => in_array($role, Role::profileRoles()))
            ->toArray();

        $rolesToSync = array_merge([$newAdministrativeRole], $profileRoles);

        $user->syncRoles($rolesToSync);
    }
}
