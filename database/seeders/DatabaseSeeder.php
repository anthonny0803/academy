<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamar al seeder de RoleAndPermission para crear los roles necesarios
        $this->call(RoleAndPermissionSeeder::class);

        // Llama a UserSeeder para poblar la tabla de usuarios
        $this->call(UserSeeder::class);

    }
}
