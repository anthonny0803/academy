<?php

namespace App\Services\Users;

use App\Models\User;
use App\Shared\Services\UpdateEmployeeService;
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
