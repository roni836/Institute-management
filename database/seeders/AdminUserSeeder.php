<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@antra.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);
    }
}
