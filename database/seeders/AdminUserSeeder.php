<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Modules\User\Models\Role;

/**
 * ModuleName: Database Seeder
 * Purpose: Create default admin user for the system with super admin role
 * 
 * Key Methods:
 * - run(): Create admin user and assign super-admin role
 * 
 * Dependencies:
 * - User Model
 * - Role Model
 * 
 * @category Database
 * @package  Database\Seeders
 * @author   Admin
 * @created  2025-01-03
 * @updated  2025-01-03
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Check if admin user already exists
        $admin = User::where('email', 'admin@demo.com')->first();

        if (!$admin) {
            // Create admin user
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@demo.com',
                'mobile' => '01700000000',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $this->command->info('✓ Admin user created successfully!');
            $this->command->info('  Email: admin@demo.com');
            $this->command->info('  Password: admin123');
        } else {
            $this->command->warn('⚠ Admin user already exists!');
        }

        // Assign super-admin role
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        
        if ($superAdminRole) {
            // Check if role is already assigned
            if (!$admin->hasRole('super-admin')) {
                $admin->roles()->attach($superAdminRole->id);
                $this->command->info('✓ Super Admin role assigned to admin user');
            } else {
                $this->command->info('✓ Admin user already has Super Admin role');
            }
        } else {
            $this->command->error('✗ Super Admin role not found! Please run RolePermissionSeeder first.');
        }
    }
}
