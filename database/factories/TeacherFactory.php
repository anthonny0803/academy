<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['is_active' => false]),
            'is_active' => true,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(fn (Teacher $teacher) =>
            $teacher->user->assignRole(Role::Teacher->value)
        );
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
