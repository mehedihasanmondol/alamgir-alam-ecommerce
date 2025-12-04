<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\User\Models\Role;
use Illuminate\Console\Command;

class SetupAdminUser extends Command
{
    protected $signature = 'user:setup-admin {user_id=1}';
    protected $description = 'Setup admin role for a user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $this->info("Found user: {$user->name} ({$user->email})");

        // Update user role to admin
        $user->update([
            'role' => 'admin',
            'is_active' => true,
        ]);
        $this->info("✓ Updated user role to 'admin'");

        // Get Super Admin role
        $superAdminRole = Role::where('slug', 'super-admin')->first();

        if ($superAdminRole) {
            // Assign Super Admin role
            $user->roles()->syncWithoutDetaching([$superAdminRole->id]);
            $this->info("✓ Assigned 'Super Admin' role");
        } else {
            $this->warn("⚠ Super Admin role not found");
        }

        $this->newLine();
        $this->info("✅ User setup complete!");
        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Role', $user->role],
                ['Active', $user->is_active ? 'Yes' : 'No'],
                ['Additional Roles', $user->roles->pluck('name')->join(', ')],
            ]
        );

        return 0;
    }
}
