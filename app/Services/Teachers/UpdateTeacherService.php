<?php

namespace App\Services\Teachers;

use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class UpdateTeacherService
{
    public function handle(Teacher $teacher, array $data): Teacher
    {
        return DB::transaction(function () use ($teacher, $data) {
            $teacher->user->update([
                'name'      => strtoupper($data['name']),
                'last_name' => strtoupper($data['last_name']),
                'email'     => strtolower($data['email']),
                'sex'       => $data['sex'],
            ]);

            $teacher->user->save();

            return $teacher;
        });
    }
}
