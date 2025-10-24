<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    /**
     * Define the model's default state.
     * Los campos personales (name, email, document_id, etc.) están en User ahora.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // user_id se asigna desde UserFactory con el estado ->teacher()
            'is_active' => fake()->boolean(90), // 90% de probabilidad de estar activo académicamente
        ];
    }

    /**
     * Indicate that the teacher is inactive academically.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the teacher is active academically.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}