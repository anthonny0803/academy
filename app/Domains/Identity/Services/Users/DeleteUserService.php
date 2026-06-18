<?php

namespace App\Domains\Identity\Services\Users;

use App\Domains\Identity\Models\User;
use App\Domains\Identity\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class DeleteUserService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function handle(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->syncRoles([]);
            $this->userRepository->delete($user);
        });
    }
}
