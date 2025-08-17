<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subjectNames = [
            'Matemática básica',
            'Matemáticas 1',
            'Matemáticas 2',
            'Lengua y Literatura',
            'Historia básica',
            'Historia 1',
            'Historia 2',
            'Geografía',
            'Ciencias Naturales',
            'Educación Física',
            'Inglés básico',
            'Inglés 1',
            'Inglés 2',
            'Arte y Cultura',
            'Computación básica',
            'Computación 1',
            'Computación 2',
            'Física 1',
            'Física 2',
            'Química 1',
            'Química 2',
            'Biología 1',
            'Biología 2',
            'Economía',
            'Filosofía',
            'Música',
            'Danza',
        ];
        return [
            'name' => $this->faker->unique()->randomElement($subjectNames),
            'description' =>$this->faker->paragraph(2),
        ];
    }
}
