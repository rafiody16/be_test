<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin1@admin.com',
            'password' => Hash::make('rahasia123'),
            'role' => 'admin',
            'provider_id' => null,
        ]);

        User::create([
            'name' => 'Admin Fulan',
            'email' => 'admin2@admin.com',
            'password' => Hash::make('rahasia123'),
            'role' => 'admin',
            'provider_id' => null,
        ]);

        User::create([
            'name' => 'Budi Pegawai',
            'email' => 'budi@poltek.com',
            'password' => Hash::make('rahasia123'),
            'role' => 'employee',
            'provider_id' => null,
        ]);

        User::create([
            'name' => 'Siti',
            'email' => 'siti@poltek.com',
            'password' => Hash::make('rahasia123'),
            'role' => 'employee',
            'provider_id' => null,
        ]);

        User::create([
            'name' => 'Beni',
            'email' => 'beni@poltek.com',
            'password' => Hash::make('rahasia123'),
            'role' => 'employee',
            'provider_id' => null,
        ]);

        // Untuk testing OAuth.
        User::create([
            'name' => 'Rafi Ody Prasetyo',
            'email' => 'rafiodi17@gmail.com',
            'password' => Hash::make('rahasia123'),
            'role' => 'employee',
            'provider_id' => null,
        ]);
    }
}