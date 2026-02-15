<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Enums\Sex;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $letter = fake()->randomElement(range('A', 'Z'));
        $digits = fake()->numerify('#######' . fake()->randomElement(['', '#', '##']));

        return [
            'name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'sex' => fake()->randomElement(Sex::toArray()),
            'document_id' => $digits . $letter,
            'birth_date' => fake()->dateTimeBetween('-60 years', '-18 years'),
            'phone' => fake()->numerify('#########'),
            'address' => fake()->address(),
            'occupation' => fake()->jobTitle(),
            'is_active' => true,
        ];
    }

    public function supervisor(): static
    {
        return $this->afterCreating(fn (User $user) =>
            $user->assignRole(Role::Supervisor->value)
        );
    }

    public function admin(): static
    {
        return $this->afterCreating(fn (User $user) =>
            $user->assignRole(Role::Admin->value)
        );
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function male(): static
    {
        return $this->state(['sex' => Sex::Male->value]);
    }

    public function female(): static
    {
        return $this->state(['sex' => Sex::Female->value]);
    }

    public function minor(): static
    {
        return $this->state([
            'birth_date' => fake()->dateTimeBetween('-17 years', '-6 years'),
        ]);
    }

    public function developer(): static
    {
        return $this->state(['is_developer' => true]);
    }
}
