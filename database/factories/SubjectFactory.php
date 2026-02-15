<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    private static array $subjects = [
        'Matematicas', 'Lengua', 'Historia', 'Fisica',
        'Quimica', 'Biologia', 'Ingles', 'Educacion Fisica',
        'Filosofia', 'Tecnologia', 'Musica', 'Arte',
    ];

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(self::$subjects)
                ?? 'Asignatura ' . fake()->unique()->numerify('##'),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
