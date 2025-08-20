<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un rol 'SuperAdmin'
        Role::firstOrCreate(['name' => 'SuperAdmin']);
        // Crear un rol 'admin'
        Role::firstOrCreate(['name' => 'Administrador']);
        // Crear un rol 'student'
        Role::firstOrCreate(['name' => 'Profesor']);
        // Crear un rol 'teacher'
        Role::firstOrCreate(['name' => 'Representante']);
        // Crear un rol 'representative'
        Role::firstOrCreate(['name' => 'Estudiante']);
    }
}
