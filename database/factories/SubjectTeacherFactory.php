<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectTeacherFactory extends Factory
{
    protected $model = SubjectTeacher::class;

    public function definition(): array
    {
        return [
            'teacher_id' => Teacher::factory(),
            'subject_id' => Subject::factory(),
        ];
    }
}
