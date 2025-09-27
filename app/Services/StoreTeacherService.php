<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StoreTeacherService
{
    public function handle(array $data): Teacher
    {
        return DB::transaction(function () use ($data) {
            $user = User::firstOrCreate(
                [
                    'email' => strtolower($data['email']),
                    'name' => strtoupper($data['name']),
                    'last_name' => strtoupper($data['last_name']),
                    'sex' => $data['sex'],
                    'password' => $data['password'],
                ]
            );

            if (!$user->wasRecentlyCreated) {
                $user->update([
                    'name' => strtoupper($data['name']),
                    'last_name' => strtoupper($data['last_name']),
                    'sex' => $data['sex'],
                ]);
            }

            if ($user->hasRole('Profesor')) {
                throw new \Exception('Este usuario ya es un profesor.');
            }

            $user->assignRole('Profesor');
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'is_active' => true,
            ]);

            return $teacher;
        });
    }
}
