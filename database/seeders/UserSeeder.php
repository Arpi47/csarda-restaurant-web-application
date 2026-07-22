<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'user1@example.com'],
            [
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'password'   => Hash::make('password123'),
                'email_verified_at' => now(),
                'profile_image' => null,
                'is_suspended' => false,
                'deletion_requested' => false,
                'deletion_requested_at' => null,
                'deletion_will_be_final_at' => null,
                'deletion_attempts_last_24h' => 0,
            ]
        );
        User::updateOrCreate(
            ['email' => 'user2@example.com'],
            [
                'first_name' => 'Jane',
                'last_name'  => 'Smith',
                'password'   => Hash::make('password123'),
                'email_verified_at' => now(),
                'profile_image' => null,
                'is_suspended' => false,
                'deletion_requested' => false,
                'deletion_requested_at' => null,
                'deletion_will_be_final_at' => null,
                'deletion_attempts_last_24h' => 0,
            ]
        );
    }
}