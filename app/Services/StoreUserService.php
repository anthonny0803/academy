<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class StoreUserService
{
    public function handle(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::firstOrCreate(
                ['email' => strtolower($data['email'])],
                [
                    'name' => strtoupper($data['name']),
                    'last_name' => strtoupper($data['last_name']),
                    'sex' => $data['sex'],
                    'password' => bcrypt($data['password']),
                    'is_active' => true,
                ]
            );

            if (!$user->wasRecentlyCreated) {
                $user->update([
                    'name' => strtoupper($data['name']),
                    'last_name' => strtoupper($data['last_name']),
                    'sex' => $data['sex'],
                    'password' => $data['password'],
                    'is_active' => true,
                ]);
            }

            if (!$user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }

            return $user;
        });
    }
}
