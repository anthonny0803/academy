<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;

class UserService
{
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => strtoupper($data['name']),
                'last_name' => strtoupper($data['last_name']),
                'email' => strtolower($data['email']),
                'sex' => $data['sex'],
                'password' => $data['password'],
                'is_active' => true,
            ]);

            $user->assignRole($data['role']);

            event(new Registered($user));

            return $user;
        });
    }
}
