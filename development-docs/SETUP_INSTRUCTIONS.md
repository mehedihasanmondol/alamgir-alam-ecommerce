# Login System Setup Instructions

## Database Configuration

You have two options for database setup:

### Option 1: Using MySQL (Recommended for Production)

1. Update your `.env` file with these settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

2. Create the database in MySQL:
```sql
CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. Run migrations and seed:
```bash
php artisan migrate:fresh --seed
```

### Option 2: Using SQLite (Quick Setup for Development)

1. Update your `.env` file:
```env
DB_CONNECTION=sqlite
DB_DATABASE=C:\Users\Love Station\Documents\alom vai\website\ecommerce\database\database.sqlite
```

2. Create the database file:
```bash
# PowerShell
New-Item -Path "database\database.sqlite" -ItemType File -Force

# Or manually create an empty file named database.sqlite in the database folder
```

3. Run migrations and seed:
```bash
php artisan migrate:fresh --seed
```

## After Database Setup

Once the database is configured and migrations are run, you can:

### Access the Login Page
Navigate to: `http://localhost:8000/login`

### Default Admin Credentials
- **Email:** admin@iherb.com
- **Password:** admin123
- **Mobile:** 01700000000 (can also be used to login)

### Admin Dashboard
After logging in as admin, you'll be redirected to: `http://localhost:8000/admin/dashboard`

## Features Implemented

✅ Login page with iHerb-inspired design
✅ Email or mobile number login support
✅ Password authentication
✅ "Keep me signed in" functionality
✅ Social login buttons (Google, Facebook, Apple) - UI only, backend integration pending
✅ Role-based access (admin/customer)
✅ Admin dashboard
✅ Responsive design with Tailwind CSS

## Building Frontend Assets

Before running the application, you need to build the frontend assets:

### Option 1: Using the batch file (Recommended for Windows)
```bash
build-assets.bat
```

### Option 2: Manual commands (Use Command Prompt, not PowerShell)
```bash
# Install dependencies
npm install

# Build assets for production
npm run build

# OR run in development mode with hot reload
npm run dev
```

**Note:** If you get a PowerShell execution policy error, use Command Prompt (cmd) instead.

## Running the Application

1. Build frontend assets (see above)

2. Start the development server:
```bash
php artisan serve
```

3. Visit: `http://localhost:8000/login`

## Next Steps

1. Configure social login providers (Google, Facebook, Apple)
2. Add SMS verification for mobile login
3. Implement password reset functionality
4. Add email verification
5. Build out admin panel features
6. Create customer-facing pages

## File Structure

```
app/
├── Http/Controllers/Auth/
│   └── LoginController.php          # Authentication logic
├── Models/
│   └── User.php                      # User model with new fields

database/
├── migrations/
│   └── 0001_01_01_000000_create_users_table.php  # Updated users table
├── seeders/
│   ├── AdminUserSeeder.php          # Creates default admin
│   └── DatabaseSeeder.php           # Main seeder

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php            # Main layout
│   ├── auth/
│   │   └── login.blade.php          # Login page
│   └── admin/
│       └── dashboard.blade.php      # Admin dashboard

routes/
└── web.php                          # Application routes
```

## Troubleshooting

### Database Connection Error
- Verify your .env database settings
- Ensure MySQL service is running (if using MySQL)
- Check database credentials

### Migration Errors
- Run `php artisan config:clear`
- Run `php artisan cache:clear`
- Try `php artisan migrate:fresh --seed` again

### Permission Issues
- Ensure storage and bootstrap/cache directories are writable
- Run: `php artisan storage:link`

## Security Notes

⚠️ **Important:** Change the default admin password after first login!

The default credentials are for initial setup only. In production:
- Use strong passwords
- Enable two-factor authentication
- Implement rate limiting
- Use HTTPS
- Keep Laravel and dependencies updated
