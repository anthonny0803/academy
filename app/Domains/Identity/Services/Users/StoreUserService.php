<?php

namespace App\Domains\Identity\Services\Users;

use App\Domains\Identity\Models\User;
use App\Shared\Services\CreateEmployeeService;
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
