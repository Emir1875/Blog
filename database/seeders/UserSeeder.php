<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat 3 user default: Admin, Staff, dan Pelanggan
     */
    public function run(): void
    {
        // Admin
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Staff
        \App\Models\User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);

        // Pelanggan
        \App\Models\User::create([
            'name' => 'Pelanggan User',
            'email' => 'pelanggan@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'pelanggan',
            'email_verified_at' => now(),
        ]);
    }
}
