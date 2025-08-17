<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'sex' => $this->faker->randomElement(['Masculino', 'Femenino', 'Otro']),
            'is_active' => $this->faker->boolean(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ])->afterCreating(function (User $user) {
            $user->assignRole('admin');
        });
    }

    public function teacher(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ])->afterCreating(function (User $user) {
            $user->assignRole('teacher');
            \App\Models\Teacher::factory()->create(['user_id' => $user->id]);
        });
    }

    public function representative(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ])->afterCreating(function (User $user) {
            $user->assignRole('representative');
            \App\Models\Representative::factory()->create(['user_id' => $user->id]);
        });
    }

    public function student(bool $selfRepresented = false): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ])->afterCreating(function (User $user) use ($selfRepresented) {
            $user->assignRole('student');

            if ($selfRepresented) {
                // Crea el representante del mismo usuario usando el factory
                $representativeProfile = \App\Models\Representative::factory()->create([
                    'user_id' => $user->id,
                ]);

                // Crea el estudiante vinculando al representante auto-representado
                \App\Models\Student::factory()->create([
                    'user_id' => $user->id,
                    'representative_id' => $representativeProfile->id,
                    'relationship_type' => 'Auto-representado',
                ]);
            } else {
                // Estudiante normal: crea con un representante externo
                \App\Models\Student::factory()->create(['user_id' => $user->id]);
            }
        });
    }
}
