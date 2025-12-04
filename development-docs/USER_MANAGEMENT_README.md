# User Management System Documentation

## Overview
A comprehensive user management system with role-based access control (RBAC), permissions, and activity tracking for Laravel ecommerce platform.

## Features

### ✅ Core Features
- **User CRUD Operations** - Create, read, update, delete users
- **Role Management** - Assign multiple roles to users
- **Permission System** - Granular permission control
- **Activity Tracking** - Log all user activities
- **Status Management** - Activate/deactivate users
- **Profile Management** - Avatar upload, address management
- **Social Login Support** - Google, Facebook, Apple integration ready
- **Advanced Search & Filters** - Search by name, email, mobile with filters
- **Livewire Components** - Real-time user search and status toggle

### 🔐 Security Features
- Password validation with complexity requirements
- Role-based middleware
- Permission-based middleware
- Active user verification
- Activity logging with IP tracking

## Installation

### 1. Run Migrations
```bash
php artisan migrate
```

This will create the following tables:
- `roles` - Store user roles
- `permissions` - Store system permissions
- `role_permissions` - Pivot table for role-permission relationships
- `user_roles` - Pivot table for user-role relationships
- `user_activities` - Track user activities

### 2. Seed Initial Data
```bash
php artisan db:seed --class=RolePermissionSeeder
```

This creates:
- **4 Default Roles**: Super Admin, Manager, Content Editor, Customer
- **24 Permissions**: Covering user, role, product, order, stock, finance, and blog modules
- **Role-Permission Assignments**: Pre-configured permission sets for each role

### 3. Create Admin User (Optional)
```php
use App\Models\User;
use App\Modules\User\Models\Role;

$admin = User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
    'is_active' => true,
]);

$superAdminRole = Role::where('slug', 'super-admin')->first();
$admin->roles()->attach($superAdminRole);
```

## File Structure

```
app/
├── Modules/User/
│   ├── Models/
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   └── UserActivity.php
│   ├── Controllers/
│   │   ├── UserController.php
│   │   └── RoleController.php
│   ├── Services/
│   │   ├── UserService.php
│   │   └── RoleService.php
│   ├── Repositories/
│   │   ├── UserRepository.php
│   │   ├── RoleRepository.php
│   │   └── PermissionRepository.php
│   └── Requests/
│       ├── StoreUserRequest.php
│       ├── UpdateUserRequest.php
│       ├── StoreRoleRequest.php
│       └── UpdateRoleRequest.php
├── Livewire/
│   ├── User/
│   │   ├── UserSearch.php
│   │   └── UserStatusToggle.php
│   └── Admin/
│       └── GlobalUserSearch.php
├── Http/Middleware/
│   ├── CheckRole.php
│   ├── CheckPermission.php
│   └── CheckUserActive.php
└── Models/
    └── User.php (Enhanced)

resources/views/
├── admin/
│   ├── users/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   └── roles/
│       └── index.blade.php
└── livewire/
    └── user/
        └── user-status-toggle.blade.php

database/
├── migrations/
│   ├── 2025_11_04_000001_create_roles_and_permissions_tables.php
│   └── 2025_11_04_000002_add_user_management_fields_to_users_table.php
└── seeders/
    └── RolePermissionSeeder.php

routes/
└── admin.php
```

## Usage

### Middleware Usage

#### Check Role
```php
Route::middleware(['role:admin'])->group(function () {
    // Admin only routes
});

Route::middleware(['role:admin,manager'])->group(function () {
    // Admin or Manager routes
});
```

#### Check Permission
```php
Route::middleware(['permission:users.create'])->group(function () {
    // Routes requiring users.create permission
});
```

#### Check User Active
```php
Route::middleware(['user.active'])->group(function () {
    // Routes requiring active user account
});
```

### User Model Methods

```php
// Check if user has role
$user->hasRole('admin'); // true/false

// Check if user has any of the roles
$user->hasAnyRole(['admin', 'manager']); // true/false

// Check if user has permission
$user->hasPermission('users.create'); // true/false

// Check if user is admin
$user->isAdmin(); // true/false

// Get user roles
$user->roles; // Collection

// Get user activities
$user->activities; // Collection
```

### Service Layer Usage

#### UserService
```php
use App\Modules\User\Services\UserService;

$userService = app(UserService::class);

// Get all users with filters
$users = $userService->getAllUsers(15, [
    'search' => 'john',
    'role' => 'customer',
    'is_active' => true,
]);

// Create user
$result = $userService->createUser([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'SecurePass123',
    'role' => 'customer',
    'roles' => [1, 2], // Additional role IDs
]);

// Update user
$result = $userService->updateUser($userId, $data);

// Delete user
$result = $userService->deleteUser($userId);

// Toggle user status
$result = $userService->toggleUserStatus($userId);

// Get user statistics
$stats = $userService->getUserStats();
```

#### RoleService
```php
use App\Modules\User\Services\RoleService;

$roleService = app(RoleService::class);

// Get all roles
$roles = $roleService->getAllRoles();

// Create role
$result = $roleService->createRole([
    'name' => 'Moderator',
    'description' => 'Content moderator',
    'permissions' => [1, 2, 3], // Permission IDs
]);

// Assign permissions to role
$result = $roleService->assignPermissions($roleId, [1, 2, 3]);
```

## Routes

All admin routes are prefixed with `/admin` and require authentication + admin role:

```
GET    /admin/users              - List all users
GET    /admin/users/create       - Show create user form
POST   /admin/users              - Store new user
GET    /admin/users/{id}         - Show user details
GET    /admin/users/{id}/edit    - Show edit user form
PUT    /admin/users/{id}         - Update user
DELETE /admin/users/{id}         - Delete user
POST   /admin/users/{id}/toggle-status - Toggle user status

GET    /admin/roles              - List all roles
GET    /admin/roles/create       - Show create role form
POST   /admin/roles              - Store new role
GET    /admin/roles/{id}         - Show role details
GET    /admin/roles/{id}/edit    - Show edit role form
PUT    /admin/roles/{id}         - Update role
DELETE /admin/roles/{id}         - Delete role
```

## Livewire Components

### UserSearch
Real-time user search with filters:
```blade
@livewire('user.user-search')
```

### UserStatusToggle
Toggle user active status:
```blade
@livewire('user.user-status-toggle', ['userId' => $user->id, 'isActive' => $user->is_active])
```

### GlobalUserSearch
Global search in admin panel:
```blade
@livewire('admin.global-user-search')
```

## Database Schema

### Users Table (Enhanced)
- `id` - Primary key
- `name` - User full name
- `email` - Email address (unique, nullable)
- `mobile` - Mobile number (unique, nullable)
- `password` - Hashed password
- `role` - Primary role (admin/customer)
- `is_active` - Account status
- `last_login_at` - Last login timestamp
- `avatar` - Profile picture path
- `address`, `city`, `state`, `country`, `postal_code` - Address fields
- `google_id`, `facebook_id`, `apple_id` - Social login IDs

### Roles Table
- `id` - Primary key
- `name` - Role name
- `slug` - URL-friendly identifier
- `description` - Role description
- `is_active` - Status

### Permissions Table
- `id` - Primary key
- `name` - Permission name
- `slug` - URL-friendly identifier
- `description` - Permission description
- `module` - Module name (user, product, order, etc.)
- `is_active` - Status

### User Activities Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `activity_type` - Type of activity
- `description` - Activity description
- `ip_address` - User IP address
- `user_agent` - Browser/device info
- `properties` - Additional JSON data

## Default Permissions

### User Module
- `users.view` - View users
- `users.create` - Create users
- `users.edit` - Edit users
- `users.delete` - Delete users
- `roles.view` - View roles
- `roles.create` - Create roles
- `roles.edit` - Edit roles
- `roles.delete` - Delete roles

### Product Module
- `products.view`, `products.create`, `products.edit`, `products.delete`

### Order Module
- `orders.view`, `orders.create`, `orders.edit`, `orders.delete`

### Stock Module
- `stock.view`, `stock.manage`

### Finance Module
- `finance.view`, `finance.manage`

### Blog Module
- `posts.view`, `posts.create`, `posts.edit`, `posts.delete`

## Best Practices

1. **Always use Service layer** for business logic
2. **Use Repository pattern** for database operations
3. **Validate requests** using Form Request classes
4. **Log activities** for audit trails
5. **Check permissions** before allowing actions
6. **Use middleware** for route protection
7. **Keep passwords secure** with strong validation rules

## Troubleshooting

### Issue: Middleware not working
**Solution**: Make sure middleware is registered in `bootstrap/app.php`

### Issue: Permissions not working
**Solution**: Run seeder and verify role-permission assignments

### Issue: Avatar upload fails
**Solution**: Run `php artisan storage:link` to create symbolic link

## Next Steps

1. Create admin layout (`resources/views/layouts/admin.blade.php`)
2. Add pagination styles
3. Implement role create/edit views
4. Add permission management interface
5. Create user export functionality
6. Add bulk user operations
7. Implement email notifications

## Support

For issues or questions, refer to:
- Laravel Documentation: https://laravel.com/docs
- Livewire Documentation: https://livewire.laravel.com
- Project Guidelines: `.windsurfrules`
