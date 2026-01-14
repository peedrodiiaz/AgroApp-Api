<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\UserRole;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario administrador
        User::create([
            'name' => 'Administrador',
            'usuario' => 'admin',
            'email' => 'admin@agroapp.com',
            'password' => Hash::make('12345678'),
            'role' => UserRole::Admin,
        ]);

        // Usuario de prueba
        User::create([
            'name' => 'Usuario Demo',
            'usuario' => 'demo',
            'email' => 'demo@agroapp.com',
            'password' => Hash::make('12345678'),
            'role' => UserRole::User,
        ]);
    }
}
