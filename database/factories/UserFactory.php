<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Enums\Sex;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password = null;

    /**
     * Define the model's default state.
     * IMPORTANTE: Este estado base NO se usa directamente.
     * Siempre se debe usar un estado específico: ->admin(), ->teacher(), etc.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'sex' => fake()->randomElement([Sex::Male->value, Sex::Female->value]),
            'document_id' => null, // Depende del módulo
            'birth_date' => null, // Depende del módulo
            'phone' => null, // Depende del módulo
            'address' => null, // Depende del módulo
            'occupation' => null, // Depende del módulo
            'is_active' => true, // Por defecto, pero se sobrescribe en los estados
            'is_developer' => false,
        ];
    }

    /**
     * Developer.
     * Lógica: is_active = true siempre
     */
    public function developer(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_developer' => true,
            'is_active' => true,
        ]);
    }

    /**
     * Supervisor (rol administrativo).
     * Lógica: is_active = true, NO pide birth_date
     */
    public function supervisor(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'birth_date' => null,
        ])->afterCreating(function (User $user) {
            $user->assignRole(Role::Supervisor->value);
        });
    }

    /**
     * Admin (rol administrativo).
     * Lógica: is_active = true, NO pide birth_date
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'birth_date' => null,
        ])->afterCreating(function (User $user) {
            $user->assignRole(Role::Admin->value);
        });
    }

    /**
     * Teacher.
     * Lógica según StoreTeacherService y CheckActiveUser middleware:
     * - user.is_active = false (pero SÍ puede acceder porque teacher.is_active = true)
     * - teacher.is_active = true (permite acceso al sistema)
     * - Tiene password (puede loguearse)
     * - NO pide birth_date
     * - Middleware CheckActiveUser línea 20: isActive() || teacher->isActive()
     */
    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false, // user.is_active = false
            'birth_date' => null, // NO se pide en el form
        ])->afterCreating(function (User $user) {
            $user->assignRole(Role::Teacher->value);
            \App\Models\Teacher::factory()->create([
                'user_id' => $user->id,
                'is_active' => true, // Permite acceso al sistema
            ]);
        });
    }

    /**
     * Representative.
     * Lógica según tu corrección:
     * - user.is_active = false (NO accede al sistema)
     * - representative.is_active = true (activo como representante)
     * - Todos los campos required: document_id, birth_date, phone, address, password
     * - occupation es nullable
     */
    public function representative(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false, // NO accede al sistema
            'document_id' => $this->generateSpanishDocument(),
            'birth_date' => fake()->dateTimeBetween('-60 years', '-20 years')->format('Y-m-d'),
            'phone' => fake()->numerify('6########'),
            'address' => fake()->address(),
            'occupation' => fake()->boolean(70) ? fake()->jobTitle() : null, // 70% con ocupación
        ])->afterCreating(function (User $user) {
            $user->assignRole(Role::Representative->value);
            \App\Models\Representative::factory()->create([
                'user_id' => $user->id,
                'is_active' => true,
            ]);
        });
    }

    /**
     * Student.
     * Lógica según tu corrección:
     * - user.is_active = false (NO accede al sistema)
     * - student.is_active = true (activo como estudiante)
     * - birth_date es required
     * - document_id y email son nullable
     * - password = null (no pueden loguearse)
     */
    public function student(bool $selfRepresented = false): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false, // NO accede al sistema
            'birth_date' => $selfRepresented 
                ? fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d') // Mayor de edad
                : fake()->dateTimeBetween('-17 years', '-6 years')->format('Y-m-d'), // Menor de edad
            'document_id' => fake()->boolean(70) ? $this->generateSpanishDocument() : null, // 70% con documento
            'email' => fake()->boolean(50) ? fake()->unique()->safeEmail() : null, // 50% con email
            'password' => null, // Students no pueden loguearse
        ])->afterCreating(function (User $user) use ($selfRepresented) {
            $user->assignRole(Role::Student->value);

            if ($selfRepresented) {
                // Crear representante con el mismo usuario
                $representativeProfile = \App\Models\Representative::factory()->create([
                    'user_id' => $user->id,
                    'is_active' => true,
                ]);

                // Crear estudiante auto-representado
                \App\Models\Student::factory()->create([
                    'user_id' => $user->id,
                    'representative_id' => $representativeProfile->id,
                    'relationship_type' => \App\Enums\RelationshipType::SelfRepresented->value,
                    'is_active' => true,
                ]);
            } else {
                // Estudiante con representante externo (se crea en StudentFactory)
                \App\Models\Student::factory()->create([
                    'user_id' => $user->id,
                    'is_active' => true,
                ]);
            }
        });
    }

    /**
     * Indicate that the user is inactive.
     * Útil para tests específicos de activación/desactivación.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the user is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the user is male.
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'sex' => Sex::Male->value,
        ]);
    }

    /**
     * Indicate that the user is female.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'sex' => Sex::Female->value,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user has no birth date.
     * Útil para User y Teacher que NO piden birth_date.
     */
    public function withoutBirthDate(): static
    {
        return $this->state(fn (array $attributes) => [
            'birth_date' => null,
        ]);
    }

    /**
     * Indicate that the user has a specific age.
     * Útil para Representative y Student que SÍ piden birth_date.
     */
    public function withAge(int $years): static
    {
        return $this->state(fn (array $attributes) => [
            'birth_date' => now()->subYears($years)->format('Y-m-d'),
        ]);
    }

    /**
     * Generate a valid Spanish DNI or NIE document.
     * Formato: DNI (8 dígitos + letra) o NIE (X/Y/Z + 7 dígitos + letra)
     */
    private function generateSpanishDocument(): string
    {
        // 70% DNI, 30% NIE
        if (fake()->boolean(70)) {
            return $this->generateDNI();
        }
        return $this->generateNIE();
    }

    /**
     * Generate a valid Spanish DNI.
     * Format: 8 números + letra de control
     */
    private function generateDNI(): string
    {
        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $number = fake()->unique()->numberBetween(10000000, 99999999);
        $letter = $letters[$number % 23];
        
        return $number . $letter;
    }

    /**
     * Generate a valid Spanish NIE.
     * Format: X/Y/Z + 7 números + letra de control
     */
    private function generateNIE(): string
    {
        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $prefix = fake()->randomElement(['X', 'Y', 'Z']);
        $number = fake()->unique()->numberBetween(1000000, 9999999);
        
        // Para calcular la letra: X=0, Y=1, Z=2
        $prefixValue = ['X' => 0, 'Y' => 1, 'Z' => 2][$prefix];
        $fullNumber = $prefixValue . str_pad($number, 7, '0', STR_PAD_LEFT);
        $letter = $letters[$fullNumber % 23];
        
        return $prefix . $number . $letter;
    }
}