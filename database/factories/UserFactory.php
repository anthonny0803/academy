<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(), // Genera un nombre aleatorio
            'last_name' => $this->faker->lastName(), // Genera un apellido aleatorio
            'document_id' => $this->faker->unique()->bothify('?#########?'), // Genera un ID de documento único (10 dígitos)
            'username' => $this->faker->unique()->userName(), // Genera un nombre de usuario único
            'email' => $this->faker->unique()->safeEmail(), // Genera un email único y seguro
            'password' => static::$password ??= Hash::make('password'), // Contraseña por defecto 'password', hasheada
            'sex' => $this->faker->randomElement(['M', 'F', 'O']), // 'M'asculino, 'F'emenino, 'O'tro
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-6 years')->format('Y-m-d'), // Edad entre 18 y 60 años
            'is_active' => $this->faker->boolean(), // true o false aleatoriamente
        ];
    }

    /**
     * Indicate that the user is an admin.
     * Indica que el Usuario es un admin.
     *
     * @return static
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ])->afterCreating(function (\App\Models\User $user) {
            $user->assignRole('admin'); // Asigna el rol 'admin' después de crear el Usuario
        });
    }

    /**
     * Indicate that the user is a teacher.
     * Indica que el Usuario es un Profesor.
     *
     * @return static
     */
    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ])->afterCreating(function (\App\Models\User $user) {
            $user->assignRole('teacher'); // Asigna el rol 'teacher'
            \App\Models\Teacher::factory()->create(['user_id' => $user->id]); // Crea un perfil de Teacher
        });
    }

    /**
     * Indicate that the user is a representative.
     * Indica que el Usuario es un Representante.
     *
     * @return static
     */
    public function representative(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ])->afterCreating(function (\App\Models\User $user) {
            $user->assignRole('representative'); // Asigna el rol 'representative'
            \App\Models\Representative::factory()->create(['user_id' => $user->id]); // Crea un perfil de Representative
        });
    }

    /**
     * Indicate that the user is a student.
     * Indica que el Usuario es un Estudiante.
     *
     * @return static
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ])->afterCreating(function (\App\Models\User $user) {
            $user->assignRole('student'); // Asigna el rol 'student'
            // NOTA: Para crear un estudiante, también necesita un representative_id
            // Esto lo podemos manejar en un Seeder principal o un Factory de Representative
            // por ahora, solo crea el shell y el Representative se podría asignar después.
            // O podríamos encadenar la creación del Representative aquí si siempre debe existir con el estudiante.
            \App\Models\Student::factory()->create([
                'user_id' => $user->id,
                'representative_id' => \App\Models\Representative::factory()
                ->create(['user_id' => \App\Models\User::factory()->create()])->id
            ]);
        });
    }
}
