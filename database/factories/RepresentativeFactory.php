<?php

namespace Database\Factories;

use App\Models\Representative;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Representative>
 */
class RepresentativeFactory extends Factory
{
    protected $model = Representative::class;

    /**
     * Define the model's default state.
     * Los campos personales (document_id, phone, address, occupation, birth_date) 
     * ahora están en User.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // user_id se asigna desde UserFactory con el estado ->representative()
            'is_active' => fake()->boolean(95), // 95% de probabilidad de estar activo
        ];
    }

    /**
     * Indicate that the representative is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the representative is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}