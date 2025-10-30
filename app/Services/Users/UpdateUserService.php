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

            return $user->fresh();
        });
    }
}
