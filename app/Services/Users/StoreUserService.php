<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class StoreUserService
{
    public function handle(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'email' => $data['email'],
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'sex' => $data['sex'],
                'password' => $data['password'],
                'is_active' => true,
            ]);

            $user->assignRole($data['role']);

            return $user;
        });
    }
}
