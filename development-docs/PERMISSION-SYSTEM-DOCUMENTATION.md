# 🔐 Comprehensive Permission System Documentation

**Project:** Laravel Ecommerce + Blog  
**Version:** 1.0  
**Last Updated:** November 17, 2025  
**Total Permissions:** 144  
**Total Roles:** 6  

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Permission Structure](#permission-structure)
3. [All Permissions by Module](#all-permissions-by-module)
4. [Role Permission Matrix](#role-permission-matrix)
5. [Implementation Guide](#implementation-guide)
6. [Usage Examples](#usage-examples)

---

## 🎯 Overview

This system implements a comprehensive Role-Based Access Control (RBAC) with **144 granular permissions** across **9 modules**:

- **User & Role Management** (11 permissions)
- **Product Management** (40 permissions)
- **Order Management** (17 permissions)
- **Delivery Management** (15 permissions)
- **Stock Management** (18 permissions)
- **Blog Management** (23 permissions)
- **Content Management** (13 permissions)
- **Payment & Finance** (6 permissions)
- **System Settings** (5 permissions)

---

## 🏗️ Permission Structure

### Naming Convention
```
{module}.{action}
```

**Examples:**
- `users.view` - View users
- `products.create` - Create products
- `orders.update-status` - Update order status

### Module Categories

| Module | Slug | Description |
|--------|------|-------------|
| User Management | `user` | Users and roles |
| Product Management | `product` | Products, categories, brands, attributes |
| Order Management | `order` | Orders, customers, coupons |
| Delivery Management | `delivery` | Zones, methods, rates |
| Stock Management | `stock` | Stock, warehouses, suppliers |
| Blog Management | `blog` | Posts, categories, tags, comments |
| Content Management | `content` | Homepage, banners, menus |
| Finance | `finance` | Payments, transactions, reports |
| System | `system` | Settings, logs, cache |

---

## 📊 All Permissions by Module

### 1️⃣ USER & ROLE MANAGEMENT (11 Permissions)

#### User Management (5)
- `users.view` - View Users
- `users.create` - Create Users
- `users.edit` - Edit Users
- `users.delete` - Delete Users
- `users.toggle-status` - Toggle User Status

#### Role Management (6)
- `roles.view` - View Roles
- `roles.create` - Create Roles
- `roles.edit` - Edit Roles
- `roles.delete` - Delete Roles
- `roles.assign-permissions` - Assign Permissions

---

### 2️⃣ PRODUCT MANAGEMENT (40 Permissions)

#### Products (6)
- `products.view` - View Products
- `products.create` - Create Products
- `products.edit` - Edit Products
- `products.delete` - Delete Products
- `products.images` - Manage Product Images
- `products.variants` - Manage Product Variants

#### Categories (6)
- `categories.view` - View Categories
- `categories.create` - Create Categories
- `categories.edit` - Edit Categories
- `categories.delete` - Delete Categories
- `categories.toggle-status` - Toggle Category Status
- `categories.duplicate` - Duplicate Categories

#### Brands (6)
- `brands.view` - View Brands
- `brands.create` - Create Brands
- `brands.edit` - Edit Brands
- `brands.delete` - Delete Brands
- `brands.toggle-status` - Toggle Brand Status
- `brands.toggle-featured` - Toggle Featured Brand

#### Attributes (4)
- `attributes.view` - View Attributes
- `attributes.create` - Create Attributes
- `attributes.edit` - Edit Attributes
- `attributes.delete` - Delete Attributes

#### Product Q&A (6)
- `questions.view` - View Product Questions
- `questions.approve` - Approve Questions
- `questions.reject` - Reject Questions
- `answers.approve` - Approve Answers
- `answers.reject` - Reject Answers
- `answers.best` - Mark Best Answer

#### Product Reviews (6)
- `reviews.view` - View Reviews
- `reviews.approve` - Approve Reviews
- `reviews.reject` - Reject Reviews
- `reviews.delete` - Delete Reviews
- `reviews.bulk-approve` - Bulk Approve Reviews
- `reviews.bulk-delete` - Bulk Delete Reviews

---

### 3️⃣ ORDER MANAGEMENT (17 Permissions)

#### Orders (7)
- `orders.view` - View Orders
- `orders.create` - Create Orders
- `orders.edit` - Edit Orders
- `orders.delete` - Delete Orders
- `orders.update-status` - Update Order Status
- `orders.cancel` - Cancel Orders
- `orders.invoice` - View Order Invoice

#### Customers (3)
- `customers.view` - View Customers
- `customers.edit` - Edit Customers
- `customers.update-info` - Update Customer Info

#### Coupons (5)
- `coupons.view` - View Coupons
- `coupons.create` - Create Coupons
- `coupons.edit` - Edit Coupons
- `coupons.delete` - Delete Coupons
- `coupons.statistics` - View Coupon Statistics

---

### 4️⃣ DELIVERY MANAGEMENT (15 Permissions)

#### Delivery Zones (5)
- `delivery-zones.view` - View Delivery Zones
- `delivery-zones.create` - Create Delivery Zones
- `delivery-zones.edit` - Edit Delivery Zones
- `delivery-zones.delete` - Delete Delivery Zones
- `delivery-zones.toggle-status` - Toggle Zone Status

#### Delivery Methods (5)
- `delivery-methods.view` - View Delivery Methods
- `delivery-methods.create` - Create Delivery Methods
- `delivery-methods.edit` - Edit Delivery Methods
- `delivery-methods.delete` - Delete Delivery Methods
- `delivery-methods.toggle-status` - Toggle Method Status

#### Delivery Rates (5)
- `delivery-rates.view` - View Delivery Rates
- `delivery-rates.create` - Create Delivery Rates
- `delivery-rates.edit` - Edit Delivery Rates
- `delivery-rates.delete` - Delete Delivery Rates
- `delivery-rates.toggle-status` - Toggle Rate Status

---

### 5️⃣ STOCK MANAGEMENT (18 Permissions)

#### Stock Operations (9)
- `stock.view` - View Stock
- `stock.movements` - View Stock Movements
- `stock.add` - Add Stock
- `stock.remove` - Remove Stock
- `stock.adjust` - Adjust Stock
- `stock.transfer` - Transfer Stock
- `stock.alerts` - View Stock Alerts
- `stock.alerts-resolve` - Resolve Stock Alerts
- `stock.current` - View Current Stock

#### Warehouses (5)
- `warehouses.view` - View Warehouses
- `warehouses.create` - Create Warehouses
- `warehouses.edit` - Edit Warehouses
- `warehouses.delete` - Delete Warehouses
- `warehouses.set-default` - Set Default Warehouse

#### Suppliers (4)
- `suppliers.view` - View Suppliers
- `suppliers.create` - Create Suppliers
- `suppliers.edit` - Edit Suppliers
- `suppliers.delete` - Delete Suppliers

---

### 6️⃣ BLOG MANAGEMENT (23 Permissions)

#### Blog Posts (11)
- `posts.view` - View Posts
- `posts.create` - Create Posts
- `posts.edit` - Edit Posts
- `posts.delete` - Delete Posts
- `posts.publish` - Publish Posts
- `posts.upload-image` - Upload Images
- `posts.tick-marks` - Manage Tick Marks
- `posts.toggle-verification` - Toggle Verification
- `posts.toggle-editor-choice` - Toggle Editor Choice
- `posts.toggle-trending` - Toggle Trending
- `posts.toggle-premium` - Toggle Premium

#### Blog Categories (4)
- `blog-categories.view` - View Blog Categories
- `blog-categories.create` - Create Blog Categories
- `blog-categories.edit` - Edit Blog Categories
- `blog-categories.delete` - Delete Blog Categories

#### Blog Tags (4)
- `blog-tags.view` - View Blog Tags
- `blog-tags.create` - Create Blog Tags
- `blog-tags.edit` - Edit Blog Tags
- `blog-tags.delete` - Delete Blog Tags

#### Blog Comments (3)
- `blog-comments.view` - View Blog Comments
- `blog-comments.approve` - Approve Blog Comments
- `blog-comments.delete` - Delete Blog Comments

---

### 7️⃣ CONTENT MANAGEMENT (13 Permissions)

#### Homepage (2)
- `homepage-settings.view` - View Homepage Settings
- `homepage-settings.edit` - Edit Homepage Settings

#### Promotional Banners (5)
- `banners.view` - View Promotional Banners
- `banners.create` - Create Promotional Banners
- `banners.edit` - Edit Promotional Banners
- `banners.delete` - Delete Promotional Banners
- `banners.toggle-status` - Toggle Banner Status

#### Sale Offers (5)
- `sale-offers.view` - View Sale Offers
- `sale-offers.create` - Create Sale Offers
- `sale-offers.edit` - Edit Sale Offers
- `sale-offers.delete` - Delete Sale Offers
- `sale-offers.toggle-status` - Toggle Sale Offer Status

#### Menus & Footer (2)
- `secondary-menu.manage` - Manage Secondary Menu
- `footer.manage` - Manage Footer

#### Featured Products (3)
- `trending-products.manage` - Manage Trending Products
- `best-sellers.manage` - Manage Best Sellers
- `new-arrivals.manage` - Manage New Arrivals

---

### 8️⃣ PAYMENT & FINANCE (6 Permissions)

#### Payment Gateways (3)
- `payment-gateways.view` - View Payment Gateways
- `payment-gateways.edit` - Edit Payment Gateways
- `payment-gateways.toggle-status` - Toggle Payment Gateway Status

#### Finance (3)
- `finance.view` - View Finance Reports
- `finance.transactions` - View Transactions
- `finance.export` - Export Finance Data

---

### 9️⃣ SYSTEM SETTINGS (5 Permissions)

- `settings.view` - View Site Settings
- `settings.edit` - Edit Site Settings
- `settings.logo` - Manage Logo
- `system.logs` - View System Logs
- `system.cache` - Manage Cache

---

## 🎭 Role Permission Matrix

### 1. Super Admin (144 permissions)
**Access:** ✅ Everything

| Module | Access Level |
|--------|-------------|
| User Management | Full Access (11/11) |
| Product Management | Full Access (40/40) |
| Order Management | Full Access (17/17) |
| Delivery Management | Full Access (15/15) |
| Stock Management | Full Access (18/18) |
| Blog Management | Full Access (23/23) |
| Content Management | Full Access (13/13) |
| Finance | Full Access (6/6) |
| System Settings | Full Access (5/5) |

---

### 2. Admin (129 permissions)
**Access:** ✅ All except User/Role/System Management

| Module | Access Level |
|--------|-------------|
| User Management | ❌ No Access (0/11) |
| Product Management | ✅ Full Access (40/40) |
| Order Management | ✅ Full Access (17/17) |
| Delivery Management | ✅ Full Access (15/15) |
| Stock Management | ✅ Full Access (18/18) |
| Blog Management | ✅ Full Access (23/23) |
| Content Management | ✅ Full Access (13/13) |
| Finance | ✅ Full Access (6/6) |
| System Settings | ❌ No Access (0/5) |

**Can Do:**
- Manage all products, orders, and stock
- Manage blog and content
- View and manage payments
- Cannot create users or change system settings

---

### 3. Manager (77 permissions)
**Access:** ✅ Product, Order, Stock, Delivery (No Delete)

| Module | Access Level |
|--------|-------------|
| User Management | ❌ No Access |
| Product Management | ⚠️ Limited (34/40) - No Delete |
| Order Management | ⚠️ Limited (16/17) - No Delete |
| Delivery Management | ✅ Full Access (15/15) |
| Stock Management | ⚠️ Limited (16/18) - No Delete |
| Blog Management | ❌ No Access |
| Content Management | ❌ No Access |
| Finance | ❌ No Access |
| System Settings | ❌ No Access |

**Can Do:**
- View, create, edit products/orders/stock
- Manage delivery settings
- Cannot delete records
- Cannot access blog, content, or finance

---

### 4. Content Editor (39 permissions)
**Access:** ✅ Blog & Content Management

| Module | Access Level |
|--------|-------------|
| User Management | ❌ No Access |
| Product Management | ❌ No Access |
| Order Management | ❌ No Access |
| Delivery Management | ❌ No Access |
| Stock Management | ❌ No Access |
| Blog Management | ✅ Full Access (23/23) |
| Content Management | ✅ Full Access (13/13) |
| Finance | ❌ No Access |
| System Settings | ❌ No Access |

**Can Do:**
- Full blog management (posts, categories, tags, comments)
- Manage homepage content and banners
- Manage featured products
- Cannot access ecommerce operations

---

### 5. Author (15 permissions)
**Access:** ✅ Blog Writing (Limited)

| Module | Access Level |
|--------|-------------|
| User Management | ❌ No Access |
| Product Management | ❌ No Access |
| Order Management | ❌ No Access |
| Delivery Management | ❌ No Access |
| Stock Management | ❌ No Access |
| Blog Management | ⚠️ Limited (15/23) |
| Content Management | ❌ No Access |
| Finance | ❌ No Access |
| System Settings | ❌ No Access |

**Can Do:**
- Create and edit own blog posts
- Upload images for posts
- Manage tick marks on posts
- View categories and tags (read-only)
- Approve/delete comments
- **Cannot:** Delete posts, manage categories/tags

---

### 6. Customer (0 permissions)
**Access:** ❌ No Admin Panel Access

| Module | Access Level |
|--------|-------------|
| All Modules | ❌ No Access |

**Can Do:**
- Access frontend features only
- View products and blog
- Place orders
- Manage own profile
- View own orders

---

## 🔧 Implementation Guide

### 1. Route Protection

#### Method 1: Middleware
```php
// Single permission
Route::middleware(['permission:products.view'])->group(function () {
    Route::get('products', [ProductController::class, 'index']);
});

// Multiple permissions (OR)
Route::middleware(['permission:products.view|orders.view'])->group(function () {
    // User needs either permission
});

// Multiple permissions (AND)
Route::middleware([
    'permission:products.view',
    'permission:products.edit'
])->group(function () {
    // User needs both permissions
});
```

#### Method 2: Controller
```php
public function __construct()
{
    $this->middleware('permission:products.view')->only(['index', 'show']);
    $this->middleware('permission:products.create')->only(['create', 'store']);
    $this->middleware('permission:products.edit')->only(['edit', 'update']);
    $this->middleware('permission:products.delete')->only('destroy');
}
```

### 2. Blade Templates

```blade
@can('products.view')
    <a href="{{ route('admin.products.index') }}">View Products</a>
@endcan

@can('products.create')
    <a href="{{ route('admin.products.create') }}">Create Product</a>
@endcan

{{-- Check multiple permissions --}}
@if(auth()->user()->hasPermission('products.edit') && auth()->user()->hasPermission('products.delete'))
    <button>Edit & Delete</button>
@endif
```

### 3. Controller Logic

```php
// Check permission
if (!auth()->user()->hasPermission('products.delete')) {
    abort(403, 'You do not have permission to delete products');
}

// Check multiple permissions
if (auth()->user()->hasAnyPermission(['products.edit', 'products.delete'])) {
    // User has at least one permission
}

// Get all user permissions
$permissions = auth()->user()->getAllPermissions();
```

### 4. Service Layer

```php
public function deleteProduct($id)
{
    if (!auth()->user()->hasPermission('products.delete')) {
        throw new UnauthorizedException('Cannot delete products');
    }
    
    // Delete logic
}
```

---

## 💡 Usage Examples

### Example 1: Product Management Routes
```php
Route::middleware(['auth', 'admin.access'])->prefix('admin')->group(function () {
    // View products (Manager, Admin, Super Admin)
    Route::middleware(['permission:products.view'])->group(function () {
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{id}', [ProductController::class, 'show']);
    });
    
    // Create products (Manager, Admin, Super Admin)
    Route::middleware(['permission:products.create'])->group(function () {
        Route::get('products/create', [ProductController::class, 'create']);
        Route::post('products', [ProductController::class, 'store']);
    });
    
    // Delete products (Admin, Super Admin only - Manager excluded)
    Route::middleware(['permission:products.delete'])->group(function () {
        Route::delete('products/{id}', [ProductController::class, 'destroy']);
    });
});
```

### Example 2: Blog Management Routes
```php
Route::middleware(['auth', 'admin.access'])->prefix('admin/blog')->group(function () {
    // View posts (Author, Editor, Admin, Super Admin)
    Route::middleware(['permission:posts.view'])->group(function () {
        Route::get('posts', [PostController::class, 'index']);
    });
    
    // Create posts (Author, Editor, Admin, Super Admin)
    Route::middleware(['permission:posts.create'])->group(function () {
        Route::post('posts', [PostController::class, 'store']);
    });
    
    // Manage categories (Editor, Admin, Super Admin only)
    Route::middleware(['permission:blog-categories.create'])->group(function () {
        Route::post('categories', [CategoryController::class, 'store']);
    });
});
```

### Example 3: Dynamic Menu
```blade
<nav>
    @if(auth()->user()->hasPermission('users.view'))
        <a href="{{ route('admin.users.index') }}">Users</a>
    @endif
    
    @if(auth()->user()->hasPermission('products.view'))
        <a href="{{ route('admin.products.index') }}">Products</a>
    @endif
    
    @if(auth()->user()->hasPermission('orders.view'))
        <a href="{{ route('admin.orders.index') }}">Orders</a>
    @endif
    
    @if(auth()->user()->hasPermission('posts.view'))
        <a href="{{ route('admin.blog.posts.index') }}">Blog</a>
    @endif
    
    @if(auth()->user()->hasPermission('settings.view'))
        <a href="{{ route('admin.settings.index') }}">Settings</a>
    @endif
</nav>
```

---

## 📈 Permission Statistics

| Category | Count |
|----------|-------|
| **Total Permissions** | 144 |
| **Total Roles** | 6 |
| **Modules** | 9 |
| **Super Admin Permissions** | 144 (100%) |
| **Admin Permissions** | 129 (89.6%) |
| **Manager Permissions** | 77 (53.5%) |
| **Content Editor Permissions** | 39 (27.1%) |
| **Author Permissions** | 15 (10.4%) |
| **Customer Permissions** | 0 (0%) |

---

## 🔄 Maintenance

### Adding New Permissions

1. **Update RolePermissionSeeder.php**
```php
['name' => 'New Permission', 'slug' => 'module.action', 'module' => 'module_name'],
```

2. **Run Seeder**
```bash
php artisan db:seed --class=RolePermissionSeeder
```

3. **Assign to Roles**
```php
$role->permissions()->attach($permissionId);
```

### Checking Permission Usage

```sql
-- Find unused permissions
SELECT p.* FROM permissions p
LEFT JOIN role_permissions rp ON p.id = rp.permission_id
WHERE rp.permission_id IS NULL;

-- Count permissions per role
SELECT r.name, COUNT(rp.permission_id) as permission_count
FROM roles r
LEFT JOIN role_permissions rp ON r.id = rp.role_id
GROUP BY r.id, r.name;
```

---

## ✅ Best Practices

1. **Always use permission checks** in routes, controllers, and views
2. **Follow naming convention**: `{module}.{action}`
3. **Test permissions** for each role after changes
4. **Document new permissions** when adding features
5. **Review permissions regularly** for security
6. **Use middleware** for route protection
7. **Check permissions in services** for business logic
8. **Provide clear error messages** when access is denied

---

## 🎓 Quick Reference Card

| Need | Check |
|------|-------|
| View users | `users.view` |
| Create products | `products.create` |
| Edit orders | `orders.edit` |
| Delete categories | `categories.delete` |
| Manage stock | `stock.view`, `stock.add`, `stock.remove` |
| Write blog posts | `posts.create`, `posts.edit` |
| Manage content | `homepage-settings.edit`, `banners.manage` |
| System settings | `settings.view`, `settings.edit` |

---

**Generated:** November 17, 2025  
**Last Seeded:** Run `php artisan db:seed --class=RolePermissionSeeder`  
**Database:** 144 permissions across 6 roles successfully configured ✅
