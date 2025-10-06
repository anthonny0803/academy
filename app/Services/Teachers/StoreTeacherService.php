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
            $user = User::create([
                'email' => strtolower($data['email']),
                'name' => strtoupper($data['name']),
                'last_name' => strtoupper($data['last_name']),
                'sex' => $data['sex'],
                'password' => $data['password'],
                'is_active' => true,
            ]);

            $user->assignRole('Profesor');
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'is_active' => true,
            ]);

            return $teacher;
        });
    }
}
