<?php

namespace App\Services\Shared;

use App\Models\User;

class CreateEmployeeService
{
    public function handle(array $data, string $role, bool $isActive): User
    {
        $user = User::create([
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
