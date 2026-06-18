<?php

namespace App\Domains\Shared\Services;

use App\Domains\Identity\Models\User;
use App\Domains\Identity\Repositories\UserRepository;

class CreateEmployeeService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function handle(array $data, string $role, bool $isActive): User
    {
        $user = $this->userRepository->create([
            'email' => $data['email'],
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'sex' => $data['sex'],
            'password' => $data['password'] ?? null,
            'is_active' => $isActive,
        ]);

        $user->assignRole($role);

        return $user;
    }
}
