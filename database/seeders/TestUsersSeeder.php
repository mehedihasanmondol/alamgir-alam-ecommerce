<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 test customer users
        for ($i = 1; $i <= 50; $i++) {
            User::create([
                'name' => "Test User {$i}",
                'email' => "testuser{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'customer',
                'mobile' => '01' . str_pad($i, 9, '0', STR_PAD_LEFT),
                'is_active' => $i % 5 !== 0, // Every 5th user is inactive
                'email_verified_at' => now(),
                'created_at' => now()->subDays(rand(1, 365)),
                'last_login_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
            ]);
        }

        $this->command->info('50 test users created successfully!');
    }
}
