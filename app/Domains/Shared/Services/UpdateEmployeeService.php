<?php

namespace App\Domains\Shared\Services;

use App\Domains\Identity\Models\User;
use App\Domains\Identity\Repositories\UserRepository;

class UpdateEmployeeService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function handle(User $user, array $data): User
    {
        $this->userRepository->update($user, [
            'email' => $data['email'],
        ]);

        return $user->fresh();
    }
}
