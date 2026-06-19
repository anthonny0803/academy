<?php

namespace Database\Factories;

use App\Domains\Academics\Models\Subject;
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
        static $sequence = 0;
        $base = self::$subjects[$sequence % count(self::$subjects)];
        $sequence++;

        return [
            'name' => "{$base} {$sequence}",
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
