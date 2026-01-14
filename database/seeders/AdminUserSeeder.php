<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Cek dulu apakah sudah ada, biar tidak duplikat
        if (User::where('email', 'admin@pgascom.com')->exists()) {
            return;
        }

        User::create([
            'name' => 'Admin PGAS',
            'email' => 'admin@pgascom.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Security 1',
            'email' => 'security@pgascom.com',
            'password' => Hash::make('security123'),
            'role' => 'security',
        ]);
    }
}