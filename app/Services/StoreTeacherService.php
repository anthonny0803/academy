<?php

namespace App\Services;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class StoreTeacherService
{
    public function handle(array $data): Teacher
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
                    'password' => bcrypt($data['password']),
                    'is_active' => true,
                ]);
            }

            if (!$user->hasRole('Profesor')) {
                $user->assignRole('Profesor');
            }

            $teacher = Teacher::firstOrCreate(
                ['user_id' => $user->id],
                ['is_active' => true]
            );

            return $teacher;
        });
    }
}
