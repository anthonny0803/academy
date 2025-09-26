<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crea un usuario administrador por defecto con datos estáticos.
        $adminUser = User::firstOrCreate(
            ['email' => 'anthonny0803@gmail.com'],
            [
                'name' => 'Admin',
                'last_name' => 'User',
                'sex' => 'Masculino',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $adminUser->is_developer = true;
        $adminUser->assignRole('Supervisor'); // Asigna el rol de admin manualmente

        // 2. Crea 10 usuarios de prueba genéricos sin un rol específico.
        User::factory()->count(10)->create();

        // 3. Crea 5 usuarios que serán Profesores, usando el estado 'teacher' del factory.
        User::factory()->teacher()->count(5)->create();

        // 4. Crea 5 usuarios que serán Representantes, usando el estado 'representative'.
        User::factory()->representative()->count(5)->create();

        // 5. Crea 10 usuarios que serán Estudiantes, usando el estado 'student'.
        User::factory()->student()->count(10)->create();

        // 6. Crea 2 estudiantes que son sus propios representantes.
        User::factory()->student(selfRepresented: true)->count(2)->create();
    }
}
