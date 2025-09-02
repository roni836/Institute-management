<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Test Teacher',
            'email' => 'teacher@test.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'status' => 'active',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'expertise' => 'Mathematics',
        ]);
    }
}
