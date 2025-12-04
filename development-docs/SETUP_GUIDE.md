# User Management System - Quick Setup Guide

## 🚀 Quick Start (5 Minutes)

Follow these steps to get the user management system up and running:

### Step 1: Run Migrations
```bash
php artisan migrate
```

This creates all necessary database tables:
- ✅ roles
- ✅ permissions
- ✅ role_permissions
- ✅ user_roles
- ✅ user_activities
- ✅ Enhanced users table

### Step 2: Seed Initial Data
```bash
php artisan db:seed --class=RolePermissionSeeder
```

This creates:
- ✅ 4 Default Roles (Super Admin, Manager, Editor, Customer)
- ✅ 24 Permissions across 6 modules
- ✅ Pre-configured role-permission assignments

### Step 3: Create Storage Link
```bash
php artisan storage:link
```

This enables avatar uploads to work properly.

### Step 4: Create Your First Admin User

**Option A: Using Tinker**
```bash
php artisan tinker
```

Then run:
```php
$admin = App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('Admin@123'),
    'role' => 'admin',
    'is_active' => true,
]);

$superAdminRole = App\Modules\User\Models\Role::where('slug', 'super-admin')->first();
$admin->roles()->attach($superAdminRole);

echo "Admin user created successfully!";
exit;
```

**Option B: Create a Seeder**

Create `database/seeders/AdminUserSeeder.php`:
```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\User\Models\Role;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('Admin@123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $admin->roles()->attach($superAdminRole);
        }

        $this->command->info('Admin user created successfully!');
    }
}
```

Then run:
```bash
php artisan db:seed --class=AdminUserSeeder
```

### Step 5: Access Admin Panel

1. Login with your admin credentials
2. Navigate to: `http://your-domain.com/admin/users`
3. Start managing users!

## 📋 Available Routes

### User Management
- `GET /admin/users` - List all users
- `GET /admin/users/create` - Create new user
- `POST /admin/users` - Store new user
- `GET /admin/users/{id}` - View user details
- `GET /admin/users/{id}/edit` - Edit user
- `PUT /admin/users/{id}` - Update user
- `DELETE /admin/users/{id}` - Delete user
- `POST /admin/users/{id}/toggle-status` - Toggle user status

### Role Management
- `GET /admin/roles` - List all roles
- `GET /admin/roles/create` - Create new role
- `POST /admin/roles` - Store new role
- `GET /admin/roles/{id}` - View role details
- `GET /admin/roles/{id}/edit` - Edit role
- `PUT /admin/roles/{id}` - Update role
- `DELETE /admin/roles/{id}` - Delete role

## 🔐 Default Credentials

After running the seeders, you can create an admin with:
- **Email**: admin@example.com
- **Password**: Admin@123 (or whatever you set)

**⚠️ IMPORTANT**: Change the default password immediately after first login!

## ✅ Verification Checklist

After setup, verify everything works:

- [ ] Can access `/admin/users`
- [ ] Can create a new user
- [ ] Can edit existing user
- [ ] Can view user details
- [ ] Can toggle user status
- [ ] Can delete user
- [ ] Can create a new role
- [ ] Can assign permissions to role
- [ ] Can assign roles to users
- [ ] Avatar upload works
- [ ] Search functionality works
- [ ] Filters work correctly
- [ ] Livewire components are interactive

## 🎨 Customization

### Change Admin Route Prefix
Edit `routes/admin.php`:
```php
Route::middleware(['auth', 'role:admin'])->prefix('dashboard')->name('admin.')->group(function () {
    // Routes...
});
```

### Add More Permissions
Edit `database/seeders/RolePermissionSeeder.php` and add to the `$permissions` array:
```php
['name' => 'Your Permission', 'slug' => 'your.permission', 'module' => 'your_module'],
```

### Customize User Fields
Add fields to `database/migrations/2025_11_04_000002_add_user_management_fields_to_users_table.php`

## 🐛 Troubleshooting

### Issue: "Class 'Role' not found"
**Solution**: Run `composer dump-autoload`

### Issue: "Route [admin.users.index] not defined"
**Solution**: Clear route cache with `php artisan route:clear`

### Issue: Avatar upload fails
**Solution**: 
1. Run `php artisan storage:link`
2. Check `storage/app/public` permissions
3. Verify `config/filesystems.php` has 'public' disk configured

### Issue: Middleware not working
**Solution**: 
1. Verify middleware is registered in `bootstrap/app.php`
2. Clear config cache: `php artisan config:clear`

### Issue: Livewire components not working
**Solution**:
1. Run `npm install` and `npm run build`
2. Clear view cache: `php artisan view:clear`
3. Verify Livewire is installed: `composer require livewire/livewire`

## 📚 Next Steps

1. **Customize the admin layout** - Edit `resources/views/layouts/admin.blade.php`
2. **Add more modules** - Products, Orders, etc.
3. **Implement email notifications** - For user registration, password reset
4. **Add two-factor authentication** - Enhanced security
5. **Create activity dashboard** - Visualize user activities
6. **Add export functionality** - Export users to CSV/Excel
7. **Implement bulk operations** - Bulk delete, bulk status change

## 📖 Documentation

- Full documentation: `USER_MANAGEMENT_README.md`
- Task tracking: `editor-task-management.md`
- Project rules: `.windsurfrules`

## 🆘 Support

If you encounter any issues:
1. Check the troubleshooting section above
2. Review `USER_MANAGEMENT_README.md`
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify all migrations ran successfully

## 🎉 You're All Set!

Your user management system is now ready to use. Happy coding!
