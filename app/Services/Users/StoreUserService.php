<?php

namespace App\Services\Users;

use App\Models\User;
use App\Services\Shared\CreatePersonService;
use Illuminate\Support\Facades\DB;

class StoreUserService
{
    public function __construct(
        private CreatePersonService $createPersonService
    ) {}

    public function handle(array $data): User
    {
        return DB::transaction(function () use ($data) {
            return $this->createPersonService->handle(
                data: $data,
                role: $data['role'],
                isActive: true
            );
        });
    }
}
