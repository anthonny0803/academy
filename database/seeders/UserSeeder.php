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
        $adminUser = User::firstOrCreate(
            ['email' => 'anthonny0803@gmail.com'],
            [
                'name' => 'Admin',
                'last_name' => 'User',
                'sex' => 'Masculino',
                'password' => Hash::make('admin000'),
                'is_active' => true,
            ]
        );
        $adminUser->is_developer = true;
        $adminUser->save();
        $adminUser->assignRole('Supervisor');
    }
}
