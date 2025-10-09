<?php

namespace App\Services\Users;

use App\Enums\Role;
use App\Models\User;
use App\Services\Shared\UpdateEmployeeService;
use Illuminate\Support\Facades\DB;

class UpdateUserService
{
    public function __construct(
        private UpdateEmployeeService $updateEmployeeService
    ) {}

    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user = $this->updateEmployeeService->handle($user, $data);

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
