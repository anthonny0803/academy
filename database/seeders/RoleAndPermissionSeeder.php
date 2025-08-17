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
        Role::firstOrCreate(['name' => 'superAdmin']);
        // Crear un rol 'admin'
        Role::firstOrCreate(['name' => 'admin']);
        // Crear un rol 'student'
        Role::firstOrCreate(['name' => 'student']);
        // Crear un rol 'teacher'
        Role::firstOrCreate(['name' => 'teacher']);
        // Crear un rol 'representative'
        Role::firstOrCreate(['name' => 'representative']);
    }
}
