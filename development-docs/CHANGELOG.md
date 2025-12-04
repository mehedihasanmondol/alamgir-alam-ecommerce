# Changelog

All notable changes to this project will be documented in this file.

## [2025-01-03] - Module: Authentication - Login System

### Added
- **User Authentication System**
  - Created LoginController with email/mobile login support
  - Implemented "Keep me signed in" functionality
  - Added role-based authentication (admin/customer)
  - Social login UI for Google, Facebook, and Apple (backend integration pending)

- **Database Schema**
  - Updated users migration with new fields:
    - `mobile` - for mobile number login
    - `role` - enum field for user roles (admin/customer)
    - `google_id`, `facebook_id`, `apple_id` - for social login integration
    - `keep_signed_in` - boolean for remember me functionality
  - Made email nullable to support mobile-only accounts

- **Views & UI**
  - Created iHerb-inspired login page with modern design
  - Implemented responsive layout with Tailwind CSS
  - Added main app layout (app.blade.php)
  - Created admin dashboard view with stats cards
  - Dynamic password field that appears after email/mobile input

- **Seeder**
  - Created AdminUserSeeder for default admin account
  - Default credentials: admin@iherb.com / admin123

- **Routes**
  - GET /login - Display login form
  - POST /login - Process login
  - POST /logout - Handle logout
  - GET /admin/dashboard - Admin dashboard (protected)

- **Documentation**
  - Created SETUP_INSTRUCTIONS.md with detailed setup guide
  - Created setup-database.ps1 PowerShell script for easy database setup

### Changed
- Updated User model fillable fields to include new authentication fields
- Added keep_signed_in to model casts

### Technical Details
- **Controller**: `app/Http/Controllers/Auth/LoginController.php`
- **Views**: 
  - `resources/views/auth/login.blade.php`
  - `resources/views/layouts/app.blade.php`
  - `resources/views/admin/dashboard.blade.php`
- **Migration**: `database/migrations/0001_01_01_000000_create_users_table.php`
- **Seeder**: `database/seeders/AdminUserSeeder.php`

### Security
- Passwords are hashed using Laravel's default bcrypt
- CSRF protection enabled on all forms
- Session regeneration on login
- Role-based access control for admin routes

### Build & Deployment
- Created `build-assets.bat` for easy frontend asset compilation on Windows
- Configured Vite with Tailwind CSS 4.0
- All assets compiled and ready for production

### Next Steps
- Implement social login backend (Google, Facebook, Apple OAuth)
- Add SMS verification for mobile login
- Create password reset functionality
- Add email verification
- Implement rate limiting for login attempts
- Add two-factor authentication
