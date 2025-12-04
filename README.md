# 🛍️ Laravel Ecommerce + Blog Platform

> A comprehensive, production-ready ecommerce and blog platform built with Laravel 12, Livewire 3, and modern web technologies.

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-4E56A6?style=flat&logo=livewire)](https://livewire.laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/Tailwind-4.x-38B2AC?style=flat&logo=tailwind-css)](https://tailwindcss.com)

---

## 📋 Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Development Setup](#development-setup)
- [Module Architecture](#module-architecture)
- [Theme System](#theme-system)
- [Permission System](#permission-system)
- [Database Schema](#database-schema)
- [CLI Commands](#cli-commands)
- [API Documentation](#api-documentation)
- [Admin Panel](#admin-panel)
- [Testing](#testing)
- [Deployment](#deployment)
- [Versioning](#versioning)
- [Security](#security)
- [Contributing](#contributing)
- [Support](#support)
- [License](#license)

---

## 🎯 Overview

A feature-rich, modular ecommerce and blog platform designed for scalability, performance, and ease of use. Built following Laravel best practices with a service-repository pattern, comprehensive permission system, and modern UI/UX.

### **Perfect For:**
- 🏪 **E-commerce Stores** - Health supplements, products, multi-vendor
- 📝 **Content Publishing** - Blog, articles, SEO-optimized content
- 📦 **Inventory Management** - Stock tracking, warehouses, suppliers
- 💰 **Finance Tracking** - Orders, payments, reports
- 👥 **Multi-role Teams** - Super Admin, Admin, Manager, Editor, Author

---

## ✨ Key Features

### 🛒 **E-commerce Module**
- ✅ **Product Management** - Simple, Variable, Grouped, Affiliate products
- ✅ **Variant System** - Attributes (color, size) with auto-generation
- ✅ **Smart Shopping Cart** - iHerb-inspired design with bundles
- ✅ **Order Management** - Complete order lifecycle tracking
- ✅ **Coupon System** - Percentage/fixed discounts with validation
- ✅ **Delivery System** - Zones, methods, dynamic rates
- ✅ **Product Q&A** - Customer questions with admin moderation
- ✅ **Review System** - 5-star ratings with image galleries
- ✅ **Stock Management** - Real-time tracking, alerts, movements

### 📝 **Blog & Content**
- ✅ **WordPress-Style CMS** - Intuitive post creation with TinyMCE/CKEditor
- ✅ **Multiple Categories** - Posts can belong to multiple categories
- ✅ **Tag System** - Flexible content organization
- ✅ **Author Profiles** - Bio, social links, post statistics
- ✅ **Comment System** - Moderated user engagement
- ✅ **Tick Marks** - Key points highlighter for posts
- ✅ **Shop This Article** - Link products within blog posts
- ✅ **SEO Optimization** - Meta tags, structured data, sitemaps
- ✅ **WordPress Migration** - One-click migration from WordPress/WooCommerce

### 🎨 **Frontend Features**
- ✅ **Dynamic Theme System** - 6 pre-built themes, admin-customizable
- ✅ **Responsive Design** - Mobile-first, tablet, desktop optimized
- ✅ **Mega Menu** - Dynamic categories with trending brands
- ✅ **Hero Sliders** - Drag-drop admin management
- ✅ **Homepage Builder** - Customizable sections
- ✅ **Advanced Search** - Real-time product/blog search
- ✅ **Smart Filters** - Category, brand, price, attributes
- ✅ **Breadcrumbs** - SEO-friendly navigation

### 🔐 **User & Security**
- ✅ **Role-Based Access** - 6 roles with 144 granular permissions
- ✅ **Social Authentication** - Google, Facebook login
- ✅ **Customer Dashboard** - Orders, reviews, wishlist, profile
- ✅ **Admin Panel** - Comprehensive management interface
- ✅ **Activity Logging** - Track user actions
- ✅ **Email Preferences** - Newsletter, promotions, recommendations

### 📊 **Business Intelligence**
- ✅ **Analytics Dashboard** - Sales, orders, revenue metrics
- ✅ **Stock Reports** - Inventory levels, movements, alerts
- ✅ **Financial Reports** - Revenue, expenses, profit tracking
- ✅ **Delivery Reports** - Zone performance, method statistics
- ✅ **PDF Export** - All reports exportable

---

## 🔧 Technology Stack

### **Backend**
| Technology | Version | Purpose |
|------------|---------|---------|
| **PHP** | 8.2+ | Server-side language |
| **Laravel** | 12.x | PHP framework |
| **MySQL** | 8.x | Relational database |
| **Livewire** | 3.6+ | Dynamic components |

### **Frontend**
| Technology | Version | Purpose |
|------------|---------|---------|
| **Tailwind CSS** | 4.x | Utility-first CSS |
| **Alpine.js** | 3.14+ | JavaScript framework |
| **Vite** | 7.x | Asset bundling |
| **TinyMCE** | 8.2+ | Rich text editor |
| **CKEditor 5** | 47.2+ | Alternative editor |

### **Additional Packages**
| Package | Purpose |
|---------|---------|
| **Intervention Image** | Image processing (WebP compression) |
| **Spatie Image Optimizer** | Image optimization |
| **DomPDF** | PDF report generation |
| **Laravel Socialite** | Social authentication |
| **Cropper.js** | Image cropping interface |
| **SortableJS** | Drag-drop ordering |

---

## 📁 Project Structure

```
ecommerce/
├── app/
│   ├── Console/Commands/          # Custom Artisan commands (6)
│   │   ├── ClearMegaMenuCache.php
│   │   ├── SendNewsletterEmails.php
│   │   ├── SendPromotionalEmails.php
│   │   ├── SendRecommendationEmails.php
│   │   └── SetupAdminUser.php
│   │
│   ├── Helpers/                   # Helper functions
│   │   ├── helpers.php           # Global helpers
│   │   └── ThemeHelper.php       # Theme system utilities
│   │
│   ├── Http/
│   │   ├── Controllers/           # Base controllers
│   │   ├── Middleware/            # Custom middleware
│   │   └── Requests/              # Form validation requests
│   │
│   ├── Livewire/                  # Livewire components
│   │   ├── Admin/                 # Admin panel components
│   │   ├── Cart/                  # Shopping cart
│   │   ├── Product/               # Product features
│   │   └── Search/                # Search functionality
│   │
│   ├── Models/                    # Eloquent models
│   │   ├── User.php
│   │   ├── SiteSetting.php
│   │   ├── HomepageSetting.php
│   │   └── ThemeSetting.php
│   │
│   ├── Modules/                   # ⭐ Modular architecture
│   │   ├── Admin/                 # Admin management
│   │   ├── Blog/                  # Blog system
│   │   │   ├── Controllers/
│   │   │   ├── Models/
│   │   │   │   ├── BlogPost.php
│   │   │   │   ├── BlogCategory.php
│   │   │   │   ├── BlogTag.php
│   │   │   │   └── BlogComment.php
│   │   │   ├── Services/
│   │   │   └── Repositories/
│   │   │
│   │   ├── Contact/               # Contact form system
│   │   │
│   │   ├── Ecommerce/             # E-commerce modules
│   │   │   ├── Brand/
│   │   │   │   ├── Controllers/
│   │   │   │   ├── Models/Brand.php
│   │   │   │   └── Services/
│   │   │   │
│   │   │   ├── Category/
│   │   │   │   ├── Controllers/
│   │   │   │   ├── Models/Category.php
│   │   │   │   └── Services/
│   │   │   │
│   │   │   ├── Delivery/
│   │   │   │   ├── Controllers/
│   │   │   │   ├── Models/
│   │   │   │   │   ├── DeliveryZone.php
│   │   │   │   │   ├── DeliveryMethod.php
│   │   │   │   │   └── DeliveryRate.php
│   │   │   │   └── Services/
│   │   │   │
│   │   │   ├── Order/
│   │   │   │   ├── Controllers/
│   │   │   │   ├── Models/
│   │   │   │   │   ├── Order.php
│   │   │   │   │   └── Coupon.php
│   │   │   │   └── Services/
│   │   │   │
│   │   │   └── Product/
│   │   │       ├── Controllers/
│   │   │       ├── Models/
│   │   │       │   ├── Product.php
│   │   │       │   ├── ProductVariant.php
│   │   │       │   ├── ProductImage.php
│   │   │       │   ├── ProductQuestion.php
│   │   │       │   └── ProductReview.php
│   │   │       ├── Services/
│   │   │       └── Repositories/
│   │   │
│   │   ├── Stock/                 # Stock management
│   │   │   ├── Controllers/
│   │   │   ├── Models/
│   │   │   │   ├── StockMovement.php
│   │   │   │   ├── Warehouse.php
│   │   │   │   └── Supplier.php
│   │   │   ├── Services/
│   │   │   └── Repositories/
│   │   │
│   │   └── User/                  # User management
│   │       ├── Controllers/
│   │       ├── Models/
│   │       │   ├── Role.php
│   │       │   └── Permission.php
│   │       └── Services/
│   │
│   ├── Services/                  # Global services
│   │   ├── SmsService.php
│   │   ├── PaymentService.php
│   │   └── SeoService.php
│   │
│   └── Traits/                    # Reusable traits
│       ├── HasSeo.php
│       ├── HasUniqueSlug.php
│       └── Trackable.php
│
├── database/
│   ├── migrations/                # 100+ migrations
│   ├── seeders/                   # 24+ smart seeders
│   │   ├── DatabaseSeeder.php    # Master seeder
│   │   ├── RolePermissionSeeder.php
│   │   ├── AdminUserSeeder.php
│   │   ├── HealthCategorySeeder.php
│   │   ├── ProductSeeder.php
│   │   └── ... (19 more seeders)
│   └── factories/                 # Model factories
│
├── development-docs/              # 📚 370+ documentation files
│   ├── SETUP_GUIDE.md
│   ├── PRODUCT_MANAGEMENT_README.md
│   ├── BLOG_SYSTEM_COMPLETE.md
│   ├── THEME_SYSTEM_README.md
│   ├── PERMISSION-SYSTEM-DOCUMENTATION.md
│   ├── DELIVERY_SYSTEM_README.md
│   ├── STOCK_MANAGEMENT_README.md
│   └── ... (363 more docs)
│
├── public/
│   ├── images/                    # Static images
│   └── storage -> ../storage/app/public
│
├── resources/
│   ├── css/
│   │   ├── app.css               # Main stylesheet
│   │   └── admin.css             # Admin stylesheet
│   ├── js/
│   │   ├── app.js                # Main JavaScript
│   │   └── admin.js              # Admin JavaScript
│   └── views/
│       ├── admin/                # Admin panel views
│       ├── auth/                 # Authentication views
│       ├── components/           # Blade components
│       ├── frontend/             # Public-facing views
│       ├── layouts/              # Layout templates
│       └── livewire/             # Livewire views
│
├── routes/
│   ├── web.php                   # Frontend routes
│   ├── admin.php                 # Admin routes
│   ├── api.php                   # API routes
│   └── console.php               # Artisan commands
│
├── storage/
│   ├── app/
│   │   └── public/               # User uploads (symlinked)
│   ├── framework/
│   ├── logs/
│   └── media-library/            # Media management
│
├── tests/                         # PHPUnit tests
│
├── .env.example                   # Environment template
├── .windsurfrules                 # AI coding rules
├── composer.json                  # PHP dependencies
├── package.json                   # Node dependencies
├── vite.config.js                 # Vite configuration
├── tailwind.config.js             # Tailwind configuration
├── phpunit.xml                    # PHPUnit configuration
├── editor-task-management.md      # Task tracking
└── README.md                      # This file
```

---

## 🚀 Installation

### **System Requirements**
- **PHP**: 8.2 or higher
- **Composer**: 2.x
- **Node.js**: 18.x LTS or higher
- **Database**: MySQL 8.x or MariaDB 10.x
- **RAM**: 2GB minimum (4GB recommended)
- **Disk Space**: 500MB minimum
- **Web Server**: Apache 2.4+ or Nginx (optional for development)

### **Quick Install (Automated - 5 Minutes)**

```bash
# 1. Clone the repository
git clone <your-repo-url> ecommerce
cd ecommerce

# 2. Run automated setup (installs dependencies, generates key)
composer run setup

# 3. Configure environment
cp .env.example .env
# Edit .env with your database credentials

# 4. Run migrations and seed demo data
php artisan migrate:fresh --seed

# 5. Create storage symlink
php artisan storage:link

# 6. Start all development servers
composer run dev
```

**That's it!** Access the app at `http://localhost:8000`

---

### **Manual Installation (Step-by-Step)**

```bash
# 1. Install PHP dependencies
composer install

# 2. Install Node.js dependencies
npm install

# 3. Create environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure database in .env file
nano .env  # or use your editor

# Required database configuration:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
APP_URL=http://localhost:8000

# 6. Create database
mysql -u root -p
CREATE DATABASE your_database_name;
EXIT;

# 7. Run all migrations
php artisan migrate

# 8. Seed database with demo data (optional but recommended)
php artisan db:seed

# 9. Create storage symlink
php artisan storage:link

# 10. Build frontend assets
npm run build

# 11. Start Laravel development server
php artisan serve

# 12. In another terminal, start Vite dev server
npm run dev
```

---

### **Social Authentication Setup (Optional)**

To enable Google and Facebook login:

1. **Google OAuth Setup:**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project
   - Enable Google+ API
   - Create OAuth 2.0 credentials
   - Add authorized redirect URI: `http://your-domain.com/login/google/callback`

2. **Facebook OAuth Setup:**
   - Go to [Facebook Developers](https://developers.facebook.com/)
   - Create a new app
   - Add Facebook Login product
   - Configure OAuth redirect URI: `http://your-domain.com/login/facebook/callback`

3. **Add credentials to `.env`:**
```env
# Google OAuth
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URL="${APP_URL}/login/google/callback"

# Facebook OAuth
FACEBOOK_CLIENT_ID=your-facebook-app-id
FACEBOOK_CLIENT_SECRET=your-facebook-app-secret
FACEBOOK_REDIRECT_URL="${APP_URL}/login/facebook/callback"
```

---

### **Default Admin Access**

After running seeders, admin account is created:

```
Email: admin@example.com
Password: Admin@123
```

**⚠️ IMPORTANT**: Change the password immediately after first login!

---

### **First-Time Access**

1. **Frontend**: `http://localhost:8000`
2. **Admin Panel**: `http://localhost:8000/admin`
3. **Login**: Use default credentials above
4. **Explore**: Navigate through admin sections

---

## 💻 Development Setup

### **Running Development Environment**

#### **Option 1: All Services Together (Recommended)**

```bash
composer run dev
```

This command runs concurrently:
- ✅ Laravel server (`php artisan serve` on port 8000)
- ✅ Queue worker (`php artisan queue:listen`)
- ✅ Log viewer (`php artisan pail`)
- ✅ Vite dev server (`npm run dev` on port 5173)

Color-coded output makes it easy to see which service logs what.

---

#### **Option 2: Individual Services**

```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Vite hot module replacement
npm run dev

# Terminal 3: Queue worker (for emails, notifications)
php artisan queue:listen --tries=3

# Terminal 4: Real-time log monitoring
php artisan pail --timeout=0
```

---

### **Network Access (Mobile/Tablet Testing)**

To test on mobile devices or other computers on your network:

```bash
# 1. Find your computer's local IP
# Windows: ipconfig
# Mac/Linux: ifconfig or ip addr

# Example output: 192.168.1.100

# 2. Start Laravel server on all network interfaces
php artisan serve --host=0.0.0.0 --port=8000

# 3. Start Vite on network
npm run dev -- --host
```

**Update `vite.config.js`** for proper HMR:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0',      // Listen on all interfaces
        port: 5173,
        hmr: {
            host: '192.168.1.100',  // Replace with YOUR computer's IP
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

**Access from mobile**: `http://192.168.1.100:8000`

---

### **Database Management**

```bash
# Fresh migration (drops all tables and recreates)
php artisan migrate:fresh

# Fresh migration with seeders
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=ProductSeeder

# Rollback last migration
php artisan migrate:rollback

# Reset all migrations
php artisan migrate:reset
```

---

### **Cache Management**

```bash
# Clear all caches
php artisan optimize:clear

# Individual cache clearing
php artisan cache:clear         # Application cache
php artisan config:clear        # Config cache
php artisan route:clear         # Route cache
php artisan view:clear          # Compiled views
php artisan event:clear         # Event cache

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

### **Build for Production**

```bash
# 1. Install production dependencies only
composer install --optimize-autoloader --no-dev

# 2. Compile and minify assets
npm run build

# 3. Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Set permissions (Linux/Mac)
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 🏗️ Module Architecture

This project follows a **modular monolith** architecture where each module is self-contained with its own controllers, models, services, and repositories.

### **Module Structure Pattern**

```
app/Modules/{ModuleName}/
├── Controllers/        # HTTP controllers
├── Models/            # Eloquent models
├── Services/          # Business logic
├── Repositories/      # Data access layer
└── Requests/          # Form validation
```

### **Core Modules**

#### **1. Ecommerce Module**
```
app/Modules/Ecommerce/
├── Product/           # Product management
├── Category/          # Category hierarchy
├── Brand/             # Brand management
├── Order/             # Order processing
└── Delivery/          # Shipping management
```

**Key Features:**
- Product variants with attributes
- Multi-category assignment
- Dynamic pricing and discounts
- Stock tracking integration

#### **2. Blog Module**
```
app/Modules/Blog/
├── Controllers/
│   ├── BlogPostController.php
│   ├── BlogCategoryController.php
│   └── BlogCommentController.php
├── Models/
│   ├── BlogPost.php
│   ├── BlogCategory.php
│   ├── BlogTag.php
│   └── BlogComment.php
└── Services/
    └── BlogService.php
```

**Key Features:**
- WordPress-style post management
- Multiple categories per post
- Tag system
- Comment moderation
- SEO optimization

#### **3. Stock Module**
```
app/Modules/Stock/
├── Controllers/
├── Models/
│   ├── StockMovement.php
│   ├── Warehouse.php
│   └── Supplier.php
└── Services/
    └── StockService.php
```

**Key Features:**
- Real-time inventory tracking
- Multi-warehouse support
- Low stock alerts
- Movement history

#### **4. User Module**
```
app/Modules/User/
├── Controllers/
│   ├── UserController.php
│   └── RoleController.php
├── Models/
│   ├── Role.php
│   └── Permission.php
└── Services/
    └── UserService.php
```

**Key Features:**
- Role-based access control (RBAC)
- 144 granular permissions
- 6 predefined roles
- Activity logging

### **Service-Repository Pattern**

**Service Layer** - Business logic
```php
class ProductService
{
    public function create(array $data): Product
    {
        // Validation, processing, events
    }
}
```

**Repository Layer** - Data access
```php
class ProductRepository
{
    public function findActive(int $perPage = 15)
    {
        return Product::active()->paginate($perPage);
    }
}
```

**Benefits:**
- ✅ Separation of concerns
- ✅ Testable business logic
- ✅ Reusable data queries
- ✅ Easy to maintain

---

## 🎨 Theme System

Dynamic theme system allowing admins to change the entire site's color scheme from the admin panel.

### **Features**
- 🎨 **6 Pre-built Themes** - Default Blue, Green Nature, Red Energy, Purple Royal, Dark Mode, Indigo Professional
- ⚡ **Instant Switching** - Changes apply immediately
- 🎯 **70+ Theme Variables** - Comprehensive color customization
- 💾 **Cached Performance** - Fast loading with cache
- 🔧 **Admin Customization** - Modify colors per theme

### **Quick Setup**

```bash
# 1. Load helper functions
composer dump-autoload

# 2. Create theme table
php artisan migrate --path=database/migrations/2025_11_20_100000_create_theme_settings_table.php

# 3. Load predefined themes
php artisan db:seed --class=ThemeSettingSeeder
```

### **Usage in Blade Templates**

```blade
<!-- Old way (hardcoded) -->
<button class="bg-blue-600 hover:bg-blue-700 text-white">Click</button>

<!-- New way (theme system) -->
<button class="{{ theme('button_primary_bg') }} {{ theme('button_primary_bg_hover') }} {{ theme('button_primary_text') }}">
    Click
</button>
```

### **Available Helper Functions**

```php
// Get single theme value
theme('primary_bg')  // Returns: 'bg-blue-600'

// Get multiple theme values
theme_classes(['primary_bg', 'primary_text', 'rounded-lg'])
// Returns: 'bg-blue-600 text-blue-600 rounded-lg'

// Get active theme info
$theme = theme_active();
echo $theme->label;  // "Default (Blue)"
```

### **Admin Access**
Navigate to: `/admin/theme-settings`

**Documentation**: `development-docs/THEME_SYSTEM_README.md`

---

## 🔐 Permission System

Comprehensive Role-Based Access Control (RBAC) with **144 granular permissions** across **9 modules**.

### **Roles Overview**

| Role | Permissions | Access Level |
|------|------------|--------------|
| **Super Admin** | 144 (100%) | Full system access |
| **Admin** | 129 (89.6%) | All except user/role/system management |
| **Manager** | 77 (53.5%) | Products, orders, stock, delivery |
| **Content Editor** | 39 (27.1%) | Blog and content management |
| **Author** | 15 (10.4%) | Blog writing (limited) |
| **Customer** | 0 (0%) | Frontend access only |

### **Permission Modules**

1. **User & Role Management** (11 permissions)
2. **Product Management** (40 permissions)
3. **Order Management** (17 permissions)
4. **Delivery Management** (15 permissions)
5. **Stock Management** (18 permissions)
6. **Blog Management** (23 permissions)
7. **Content Management** (13 permissions)
8. **Payment & Finance** (6 permissions)
9. **System Settings** (5 permissions)

### **Usage Examples**

**In Routes:**
```php
Route::middleware(['permission:products.view'])->group(function () {
    Route::get('products', [ProductController::class, 'index']);
});
```

**In Blade:**
```blade
@can('products.create')
    <a href="{{ route('admin.products.create') }}">Create Product</a>
@endcan
```

**In Controllers:**
```php
if (!auth()->user()->hasPermission('products.delete')) {
    abort(403, 'Unauthorized action');
}
```

**Setup:**
```bash
php artisan db:seed --class=RolePermissionSeeder
```

**Documentation**: `development-docs/PERMISSION-SYSTEM-DOCUMENTATION.md`

---

## 🗄️ Database Schema

### **Core Tables**

**User Management**
- `users` - User accounts and profiles
- `roles` - Role definitions
- `permissions` - Permission definitions
- `role_permissions` - Role-permission mappings
- `user_roles` - User-role assignments

**E-commerce**
- `products` - Main product table
- `product_variants` - Product variations
- `product_images` - Product image gallery
- `categories` - Product categories
- `brands` - Product brands
- `orders` - Customer orders
- `order_items` - Order line items
- `coupons` - Discount coupons
- `product_reviews` - Customer reviews
- `product_questions` - Product Q&A

**Delivery**
- `delivery_zones` - Geographical zones
- `delivery_methods` - Shipping methods
- `delivery_rates` - Zone-method pricing

**Stock**
- `stock_movements` - Inventory transactions
- `warehouses` - Storage locations
- `suppliers` - Vendor information

**Blog**
- `blog_posts` - Blog articles
- `blog_categories` - Post categories
- `blog_tags` - Post tags
- `blog_comments` - User comments
- `blog_tick_marks` - Key point highlights

**Settings**
- `site_settings` - Site configuration
- `homepage_settings` - Homepage customization
- `theme_settings` - Theme definitions
- `hero_sliders` - Homepage sliders

### **Entity Relationship Diagram (Text)**

```
Users (1) ──────< (N) Orders
Users (1) ──────< (N) BlogPosts
Users (1) ──────< (N) ProductReviews
Users (N) ──────> (N) Roles

Products (1) ────< (N) ProductVariants
Products (1) ────< (N) ProductImages
Products (N) ────> (N) Categories
Products (N) ────> (1) Brand
Products (1) ────< (N) ProductReviews
Products (1) ────< (N) ProductQuestions

Orders (1) ──────< (N) OrderItems
Orders (N) ──────> (N) Coupons
Orders (N) ──────> (1) DeliveryMethod
Orders (N) ──────> (1) User

BlogPosts (N) ───> (N) BlogCategories
BlogPosts (N) ───> (N) BlogTags
BlogPosts (1) ───< (N) BlogComments
BlogPosts (N) ───> (1) User (author)

StockMovements (N) ──> (1) Product
StockMovements (N) ──> (1) Warehouse
```

### **Migrations**

Total: **100+ migration files**

Run all migrations:
```bash
php artisan migrate
```

Fresh install:
```bash
php artisan migrate:fresh --seed
```

---

## 🛠️ CLI Commands

### **Custom Artisan Commands**

| Command | Description |
|---------|-------------|
| `php artisan setup:admin` | Create admin user interactively |
| `php artisan cache:mega-menu` | Clear mega menu cache |
| `php artisan email:newsletter` | Send newsletter emails |
| `php artisan email:promotional` | Send promotional campaigns |
| `php artisan email:recommendations` | Send product recommendations |
| `php artisan wordpress:migrate` | Migrate content from WordPress/WooCommerce |

### **WordPress/WooCommerce Migration**

Migrate your entire WordPress blog and WooCommerce store to Laravel with a single command:

```bash
# Interactive migration (recommended)
php artisan db:seed --class=WordPressMigrationSeeder

# Or direct command
php artisan wordpress:migrate --domain=https://your-site.com
```

**Features:**
- ✅ Migrates all blog posts, categories, tags, and authors
- ✅ Migrates WooCommerce products with variants and attributes
- ✅ Downloads and converts all images (including Bangla filenames)
- ✅ Replaces old WordPress URLs with new Laravel URLs
- ✅ Preserves SEO meta data, slugs, and permalinks
- ✅ Creates author profiles automatically
- ✅ Maintains publish dates and authorship
- ✅ Idempotent (safe to run multiple times)
- ✅ Handles missing data with smart defaults

**Configuration (.env):**
```env
WORDPRESS_DOMAIN=https://your-wordpress-site.com
WOOCOMMERCE_KEY=ck_xxxxxxxxxxxxxxxxxxxxx
WOOCOMMERCE_SECRET=cs_xxxxxxxxxxxxxxxxxxxxx
```

**Options:**
```bash
--domain=URL              # WordPress site URL
--wc-key=KEY             # WooCommerce Consumer Key
--wc-secret=SECRET       # WooCommerce Consumer Secret
--only-posts             # Migrate only blog posts
--only-products          # Migrate only products
--skip-images            # Skip image download (testing)
--batch-size=N           # Items per batch (default: 10)
--start-from=N           # Resume from page N
```

**Quick Start:**
1. Get WooCommerce API keys from: `WP Admin → WooCommerce → Settings → Advanced → REST API`
2. Add credentials to `.env`
3. Run: `php artisan db:seed --class=WordPressMigrationSeeder`
4. Review migrated content in admin panel

**Documentation:** See `WORDPRESS_MIGRATION_QUICK_START.md` and `development-docs/WORDPRESS_MIGRATION_GUIDE.md` for complete details.

### **Common Laravel Commands**

**Development:**
```bash
php artisan serve                    # Start development server
php artisan queue:listen             # Process queue jobs
php artisan pail                     # Monitor logs
```

**Database:**
```bash
php artisan migrate                  # Run migrations
php artisan migrate:fresh --seed    # Fresh install with data
php artisan db:seed                  # Run all seeders
php artisan db:seed --class=Name    # Run specific seeder
```

**Cache:**
```bash
php artisan optimize:clear           # Clear all caches
php artisan cache:clear             # Clear application cache
php artisan config:cache            # Cache configuration
php artisan route:cache             # Cache routes
php artisan view:cache              # Cache views
```

**Maintenance:**
```bash
php artisan down                    # Enable maintenance mode
php artisan up                      # Disable maintenance mode
php artisan storage:link            # Create storage symlink
```

### **Composer Scripts**

```bash
composer run setup                  # Full project setup
composer run dev                    # Start all dev servers
composer run test                   # Run tests
```

---

## 📡 API Documentation

### **API Endpoints (Extensible)**

Currently, the project is frontend-focused, but API routes are ready for extension.

**Base URL**: `http://your-domain.com/api`

**Authentication**: Laravel Sanctum (ready to implement)

### **Planned API Routes**

```
GET    /api/products              # List products
GET    /api/products/{id}         # Product details
POST   /api/cart/add              # Add to cart
GET    /api/categories            # List categories
GET    /api/blog/posts            # List blog posts
```

### **Response Format**

```json
{
    "success": true,
    "data": {
        // Response data
    },
    "message": "Success message",
    "meta": {
        "current_page": 1,
        "total": 100
    }
}
```

### **Enable API Authentication**

1. Install Sanctum:
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

2. Add to `app/Http/Kernel.php`:
```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
],
```

**Documentation**: API documentation can be generated using Laravel Scribe or Swagger.

---

## 🎛️ Admin Panel

Comprehensive admin interface for managing all aspects of the platform.

### **Access**
URL: `/admin`
Default credentials: `admin@example.com` / `Admin@123`

### **Admin Sections**

#### **Dashboard**
- Sales analytics
- Recent orders
- Stock alerts
- Quick stats

#### **Product Management**
- Products (CRUD)
- Categories
- Brands
- Attributes
- Reviews & Q&A

#### **Order Management**
- Orders list
- Order details
- Status updates
- Invoices

#### **Stock Management**
- Current stock
- Stock movements
- Warehouses
- Suppliers
- Alerts

#### **Blog Management**
- Posts
- Categories
- Tags
- Comments

#### **Delivery Management**
- Zones
- Methods
- Rates

#### **User Management**
- Users
- Roles
- Permissions

#### **Settings**
- Site settings
- Homepage settings
- Theme settings
- SEO settings

### **Admin Features**
- ✅ Responsive design
- ✅ Real-time search
- ✅ Advanced filters
- ✅ Bulk actions
- ✅ PDF export
- ✅ Image management
- ✅ Drag-drop ordering
- ✅ Live preview

**Documentation**: `development-docs/ADMIN-DASHBOARD-GUIDE.md`

---

## 🧪 Testing

### **Running Tests**

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ProductTest.php

# Run with coverage
php artisan test --coverage

# Run specific test method
php artisan test --filter=test_product_creation
```

### **Test Structure**

```
tests/
├── Feature/              # Feature tests
│   ├── ProductTest.php
│   ├── OrderTest.php
│   └── BlogTest.php
├── Unit/                 # Unit tests
│   ├── ProductServiceTest.php
│   └── OrderServiceTest.php
└── TestCase.php          # Base test class
```

### **Writing Tests**

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Modules\Ecommerce\Product\Models\Product;

class ProductTest extends TestCase
{
    public function test_can_create_product()
    {
        $response = $this->post('/admin/products', [
            'name' => 'Test Product',
            'price' => 99.99,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
        ]);
    }
}
```

---

## 🚀 Deployment

### **Server Requirements**
- PHP 8.2+ with required extensions
- MySQL 8.0+ or MariaDB 10.5+
- Nginx or Apache
- Composer
- Node.js (for building assets)
- SSL Certificate (recommended)

### **Deployment Steps**

#### **1. Prepare Server**

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

#### **2. Deploy Application**

```bash
# Clone repository
git clone <your-repo> /var/www/ecommerce
cd /var/www/ecommerce

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Environment setup
cp .env.example .env
nano .env  # Configure production settings

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

#### **3. Configure Web Server**

**Nginx Configuration:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/ecommerce/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### **4. SSL Certificate (Let's Encrypt)**

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

#### **5. Setup Queue Worker**

Create supervisor config `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ecommerce/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/ecommerce/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

#### **6. Setup Scheduled Tasks**

Add to crontab:
```bash
* * * * * cd /var/www/ecommerce && php artisan schedule:run >> /dev/null 2>&1
```

### **Environment Variables (Production)**

```env
APP_NAME="Your App Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=production_db
DB_USERNAME=db_user
DB_PASSWORD=secure_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password

QUEUE_CONNECTION=database
```

---

## 📌 Versioning

This project follows [Semantic Versioning](https://semver.org/) (SemVer): `MAJOR.MINOR.PATCH`

### **Current Version: 1.0.0**

### **Version History**

| Version | Date | Changes |
|---------|------|---------|
| **1.0.0** | Nov 2025 | Initial production release |
| | | ✅ Complete ecommerce system |
| | | ✅ Blog CMS with SEO |
| | | ✅ Stock management |
| | | ✅ Permission system (144 permissions) |
| | | ✅ Theme system (6 themes) |
| | | ✅ Delivery management |
| | | ✅ 370+ documentation files |

### **Upgrade Guide**

When upgrading between versions:

```bash
# 1. Backup database
mysqldump -u username -p database_name > backup.sql

# 2. Pull latest code
git pull origin main

# 3. Update dependencies
composer install
npm install

# 4. Run migrations
php artisan migrate

# 5. Clear caches
php artisan optimize:clear

# 6. Rebuild assets
npm run build

# 7. Recache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Release Strategy**

- **Major releases** (x.0.0): Breaking changes, major features
- **Minor releases** (1.x.0): New features, backward compatible
- **Patch releases** (1.0.x): Bug fixes, security patches

---

## 🔒 Security

### **Security Features**

- ✅ **CSRF Protection** - Laravel's built-in CSRF tokens
- ✅ **XSS Prevention** - Blade template escaping
- ✅ **SQL Injection Prevention** - Eloquent ORM parameterized queries
- ✅ **Password Hashing** - Bcrypt encryption
- ✅ **HTTPS Enforcement** - SSL/TLS support
- ✅ **Rate Limiting** - API and form submission throttling
- ✅ **Input Validation** - Form request validation
- ✅ **Session Security** - Secure session handling
- ✅ **Role-Based Access Control** - Granular permissions

### **Security Best Practices**

#### **1. Environment Configuration**

```env
# Production settings
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:random-32-character-key

# Force HTTPS
FORCE_HTTPS=true

# Secure session
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
```

#### **2. File Permissions**

```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Protect sensitive files
chmod 600 .env
chmod 644 composer.json package.json
```

#### **3. Database Security**

- Use strong database passwords
- Create dedicated database user with minimum privileges
- Backup database regularly
- Keep database software updated

#### **4. Update Dependencies**

```bash
# Check for security vulnerabilities
composer audit
npm audit

# Update packages
composer update
npm update
```

### **Reporting Security Vulnerabilities**

If you discover a security vulnerability:

1. **DO NOT** create a public GitHub issue
2. Email: security@yourproject.com
3. Include detailed description and reproduction steps
4. We'll respond within 48 hours

### **Security Checklist**

Before deploying to production:

- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY`
- [ ] Use strong database password
- [ ] Enable HTTPS/SSL
- [ ] Set file permissions correctly
- [ ] Remove default admin credentials
- [ ] Configure firewall rules
- [ ] Enable rate limiting
- [ ] Set up automated backups
- [ ] Update all dependencies
- [ ] Run security audit (`composer audit`)

---

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

### **How to Contribute**

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/amazing-feature
   ```
3. **Make your changes**
4. **Follow coding standards** (PSR-12 for PHP, ESLint for JS)
5. **Write tests** for new features
6. **Commit your changes**
   ```bash
   git commit -m "Add amazing feature"
   ```
7. **Push to your branch**
   ```bash
   git push origin feature/amazing-feature
   ```
8. **Open a Pull Request**

### **Coding Standards**

**PHP (PSR-12):**
```php
<?php

namespace App\Services;

class ProductService
{
    public function create(array $data): Product
    {
        // Use 4 spaces for indentation
        // Follow Laravel naming conventions
    }
}
```

**JavaScript (ESLint):**
```javascript
// Use 2 spaces for indentation
// Use camelCase for variables
const productPrice = 99.99;
```

**Blade Templates:**
```blade
{{-- Use Blade directives --}}
@if ($condition)
    <div class="container">
        {{-- Indent nested elements --}}
    </div>
@endif
```

### **Code Review Process**

1. All changes require review
2. Must pass automated tests
3. Must follow coding standards
4. Must include documentation
5. Must be approved by maintainer

### **Types of Contributions**

- 🐛 **Bug fixes**
- ✨ **New features**
- 📝 **Documentation improvements**
- 🎨 **UI/UX enhancements**
- ⚡ **Performance optimizations**
- 🧪 **Test coverage**

### **Development Guidelines**

- Follow the existing module structure
- Use service-repository pattern
- Write meaningful commit messages
- Add comments for complex logic
- Update documentation
- Test your changes thoroughly

---

## 💬 Support

### **Documentation**

- **README**: You're reading it!
- **Development Docs**: `development-docs/` folder (370+ files)
- **Code Comments**: Inline documentation

### **Getting Help**

1. **Check documentation** in `development-docs/`
2. **Search existing issues** on GitHub
3. **Create new issue** with detailed description
4. **Join community** (if applicable)

### **Useful Documentation Files**

| Topic | File |
|-------|------|
| Setup | `development-docs/SETUP_GUIDE.md` |
| Products | `development-docs/PRODUCT_MANAGEMENT_README.md` |
| Blog | `development-docs/BLOG_SYSTEM_COMPLETE.md` |
| Theme | `development-docs/THEME_SYSTEM_README.md` |
| Permissions | `development-docs/PERMISSION-SYSTEM-DOCUMENTATION.md` |
| Stock | `development-docs/STOCK_MANAGEMENT_README.md` |
| Delivery | `development-docs/DELIVERY_SYSTEM_README.md` |
| Admin | `development-docs/ADMIN-DASHBOARD-GUIDE.md` |

### **Troubleshooting**

#### **Common Issues**

**Issue: Livewire not updating**
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

**Issue: Assets not loading**
```bash
npm run dev -- --host
# Update vite.config.js with your IP
```

**Issue: Storage images not showing**
```bash
php artisan storage:link
# Check file permissions
```

**Issue: Permission denied errors**
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

**Issue: Database connection error**
- Check `.env` database credentials
- Verify database exists
- Test connection: `php artisan tinker` → `DB::connection()->getPdo();`

### **Community**

- **GitHub Issues**: Bug reports and feature requests
- **Discussions**: General questions and ideas
- **Email**: support@yourproject.com

---

## 📄 License

This project is licensed under the **MIT License**.

```
MIT License

Copyright (c) 2025 [Your Name/Organization]

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

See the [LICENSE](LICENSE) file for full details.

---

## 🙏 Credits

### **Built With**

- [Laravel](https://laravel.com) - The PHP Framework for Web Artisans
- [Livewire](https://livewire.laravel.com) - Full-stack framework for Laravel
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Alpine.js](https://alpinejs.dev) - Lightweight JavaScript framework
- [Vite](https://vitejs.dev) - Next Generation Frontend Tooling

### **Key Contributors**

- **Project Lead**: [Your Name]
- **Backend Development**: Laravel 12 + Module Architecture
- **Frontend Development**: Livewire 3 + Tailwind CSS
- **UI/UX Design**: Responsive, Mobile-First Design
- **Documentation**: 370+ Comprehensive Guides

### **Special Thanks**

- Laravel community for excellent documentation
- Tailwind Labs for the amazing CSS framework
- Livewire team for simplifying dynamic UIs
- All open-source contributors

### **Third-Party Packages**

- **intervention/image** - Image processing
- **spatie/image-optimizer** - Image optimization
- **barryvdh/laravel-dompdf** - PDF generation
- **laravel/socialite** - Social authentication
- **tinymce** & **ckeditor5** - Rich text editors
- **cropperjs** - Image cropping
- **sortablejs** - Drag and drop

---

## 📸 Screenshots

> **Note**: Add screenshots of your application here

### **Frontend**

**Homepage**
```
[Screenshot placeholder - Add your homepage screenshot]
```

**Product Page**
```
[Screenshot placeholder - Add product detail page]
```

**Blog**
```
[Screenshot placeholder - Add blog page]
```

**Shopping Cart**
```
[Screenshot placeholder - Add cart page]
```

### **Admin Panel**

**Dashboard**
```
[Screenshot placeholder - Add admin dashboard]
```

**Product Management**
```
[Screenshot placeholder - Add product admin]
```

**Order Management**
```
[Screenshot placeholder - Add order admin]
```

**Stock Management**
```
[Screenshot placeholder - Add stock admin]
```

---

## 📊 Project Statistics

- **Total Files**: 1,000+
- **Lines of Code**: 50,000+
- **Documentation Files**: 370+
- **Database Tables**: 100+
- **Migrations**: 100+
- **Seeders**: 24+
- **Permissions**: 144
- **Roles**: 6
- **Modules**: 6
- **Themes**: 6
- **Commands**: 6
- **Livewire Components**: 30+

---

## 🎯 Roadmap

### **Version 1.1 (Planned)**
- [ ] API v1 with Sanctum authentication
- [ ] Advanced analytics dashboard
- [ ] Multi-language support (i18n)
- [ ] Product wishlist
- [ ] Customer reviews gallery
- [ ] Email marketing automation

### **Version 1.2 (Future)**
- [ ] Multi-vendor marketplace
- [ ] Real-time notifications
- [ ] Advanced reporting
- [ ] Mobile app (Flutter/React Native)
- [ ] Subscription products
- [ ] Affiliate program

### **Version 2.0 (Long-term)**
- [ ] Headless CMS architecture
- [ ] GraphQL API
- [ ] Progressive Web App (PWA)
- [ ] AI-powered recommendations
- [ ] Blockchain integration
- [ ] Multi-currency support

---

## 📞 Contact

- **Project Link**: [https://github.com/yourusername/ecommerce](https://github.com/yourusername/ecommerce)
- **Documentation**: `/development-docs`
- **Email**: your.email@example.com
- **Website**: https://yourwebsite.com

---

## ⭐ Show Your Support

If you find this project helpful, please consider:

- ⭐ **Star this repository** on GitHub
- 🐛 **Report bugs** via Issues
- 💡 **Suggest features** via Discussions
- 📢 **Share** with others
- 🤝 **Contribute** code improvements

---

## 📝 Final Notes

This is a **production-ready** ecommerce and blog platform with:

✅ **Comprehensive Features** - Everything you need out of the box
✅ **Clean Architecture** - Modular, maintainable, scalable
✅ **Extensive Documentation** - 370+ guides and references
✅ **Modern Stack** - Laravel 12, Livewire 3, Tailwind 4
✅ **Security First** - Best practices implemented
✅ **Developer Friendly** - Easy to understand and extend

**Built with ❤️ using Laravel**

---

**Last Updated**: November 28, 2025  
**Version**: 1.0.0  
**Status**: Production Ready ✅
