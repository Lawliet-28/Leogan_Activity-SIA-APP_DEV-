<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'admin',
                'full_name' => 'System Administrator',
                'email_verified_at' => now(),
                'password' => Hash::make('123456'),
                'role' => 'admin',
            ]
        );
    }
}
