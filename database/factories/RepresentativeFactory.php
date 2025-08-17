<?php

namespace Database\Factories;

use App\Models\Representative;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Representative>
 */
class RepresentativeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Representative::class;

    /**
     * Define the model's default state.
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'user_id' se asignará cuando el UserFactory lo cree (en el estado 'representative()')
            'document_id' => $this->faker->unique()->regexify('[A-Z]{0,1}[0-9]{7,9}[A-Z]{1}'), // Genera un ID de documento único (10 dígitos)
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'occupation' => $this->faker->jobTitle(),
            'is_active' => $this->faker->boolean(95), // 95% de probabilidad de ser activo
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-20 years')->format('Y-m-d'), // Edad entre 20 y 60 años
        ];
    }
}