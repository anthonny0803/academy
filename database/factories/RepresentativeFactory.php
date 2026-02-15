<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Representative;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepresentativeFactory extends Factory
{
    protected $model = Representative::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['is_active' => false]),
            'is_active' => false,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(fn (Representative $representative) =>
            $representative->user->assignRole(Role::Representative->value)
        );
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'user_id' => User::factory()->state(['is_active' => true]),
            'is_active' => true,
        ]);
    }
}
