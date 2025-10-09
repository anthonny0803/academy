<?php

namespace App\Services\Users;

use App\Models\User;
use App\Services\Shared\CreateEmployeeService;
use Illuminate\Support\Facades\DB;

class StoreUserService
{
    public function __construct(
        private CreateEmployeeService $createEmployeeService
    ) {}

    public function handle(array $data): User
    {
        return DB::transaction(function () use ($data) {
            return $this->createEmployeeService->handle(
                data: $data,
                role: $data['role'],
                isActive: true
            );
        });
    }
}
