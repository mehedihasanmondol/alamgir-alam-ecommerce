<?php

namespace Database\Seeders;

use App\Modules\User\Models\Permission;
use App\Modules\User\Models\Role;
use Illuminate\Database\Seeder;

/**
 * ModuleName: User Management
 * Purpose: Seed initial roles and permissions
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing role-permission relationships
        \DB::table('role_permissions')->truncate();
        
        // Create Comprehensive Permissions (use updateOrCreate to avoid duplicates)
        $permissions = [
            // ===================================
            // USER & ROLE MANAGEMENT MODULE
            // ===================================
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'user'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'user'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'module' => 'user'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'module' => 'user'],
            ['name' => 'Toggle User Status', 'slug' => 'users.toggle-status', 'module' => 'user'],
            
            ['name' => 'View Roles', 'slug' => 'roles.view', 'module' => 'user'],
            ['name' => 'Create Roles', 'slug' => 'roles.create', 'module' => 'user'],
            ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'module' => 'user'],
            ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'module' => 'user'],
            ['name' => 'Assign Permissions', 'slug' => 'roles.assign-permissions', 'module' => 'user'],
            
            // ===================================
            // PRODUCT MANAGEMENT MODULE
            // ===================================
            ['name' => 'View Products', 'slug' => 'products.view', 'module' => 'product'],
            ['name' => 'Create Products', 'slug' => 'products.create', 'module' => 'product'],
            ['name' => 'Edit Products', 'slug' => 'products.edit', 'module' => 'product'],
            ['name' => 'Delete Products', 'slug' => 'products.delete', 'module' => 'product'],
            ['name' => 'Manage Product Images', 'slug' => 'products.images', 'module' => 'product'],
            ['name' => 'Manage Product Variants', 'slug' => 'products.variants', 'module' => 'product'],
            
            // Categories
            ['name' => 'View Categories', 'slug' => 'categories.view', 'module' => 'product'],
            ['name' => 'Create Categories', 'slug' => 'categories.create', 'module' => 'product'],
            ['name' => 'Edit Categories', 'slug' => 'categories.edit', 'module' => 'product'],
            ['name' => 'Delete Categories', 'slug' => 'categories.delete', 'module' => 'product'],
            ['name' => 'Toggle Category Status', 'slug' => 'categories.toggle-status', 'module' => 'product'],
            ['name' => 'Duplicate Categories', 'slug' => 'categories.duplicate', 'module' => 'product'],
            
            // Brands
            ['name' => 'View Brands', 'slug' => 'brands.view', 'module' => 'product'],
            ['name' => 'Create Brands', 'slug' => 'brands.create', 'module' => 'product'],
            ['name' => 'Edit Brands', 'slug' => 'brands.edit', 'module' => 'product'],
            ['name' => 'Delete Brands', 'slug' => 'brands.delete', 'module' => 'product'],
            ['name' => 'Toggle Brand Status', 'slug' => 'brands.toggle-status', 'module' => 'product'],
            ['name' => 'Toggle Featured Brand', 'slug' => 'brands.toggle-featured', 'module' => 'product'],
            
            // Attributes
            ['name' => 'View Attributes', 'slug' => 'attributes.view', 'module' => 'product'],
            ['name' => 'Create Attributes', 'slug' => 'attributes.create', 'module' => 'product'],
            ['name' => 'Edit Attributes', 'slug' => 'attributes.edit', 'module' => 'product'],
            ['name' => 'Delete Attributes', 'slug' => 'attributes.delete', 'module' => 'product'],
            
            // Product Q&A
            ['name' => 'View Product Questions', 'slug' => 'questions.view', 'module' => 'product'],
            ['name' => 'Approve Questions', 'slug' => 'questions.approve', 'module' => 'product'],
            ['name' => 'Reject Questions', 'slug' => 'questions.reject', 'module' => 'product'],
            ['name' => 'Approve Answers', 'slug' => 'answers.approve', 'module' => 'product'],
            ['name' => 'Reject Answers', 'slug' => 'answers.reject', 'module' => 'product'],
            ['name' => 'Mark Best Answer', 'slug' => 'answers.best', 'module' => 'product'],
            
            // Product Reviews
            ['name' => 'View Reviews', 'slug' => 'reviews.view', 'module' => 'product'],
            ['name' => 'Approve Reviews', 'slug' => 'reviews.approve', 'module' => 'product'],
            ['name' => 'Reject Reviews', 'slug' => 'reviews.reject', 'module' => 'product'],
            ['name' => 'Delete Reviews', 'slug' => 'reviews.delete', 'module' => 'product'],
            ['name' => 'Bulk Approve Reviews', 'slug' => 'reviews.bulk-approve', 'module' => 'product'],
            ['name' => 'Bulk Delete Reviews', 'slug' => 'reviews.bulk-delete', 'module' => 'product'],
            
            // ===================================
            // ORDER MANAGEMENT MODULE
            // ===================================
            ['name' => 'View Orders', 'slug' => 'orders.view', 'module' => 'order'],
            ['name' => 'Create Orders', 'slug' => 'orders.create', 'module' => 'order'],
            ['name' => 'Edit Orders', 'slug' => 'orders.edit', 'module' => 'order'],
            ['name' => 'Delete Orders', 'slug' => 'orders.delete', 'module' => 'order'],
            ['name' => 'Update Order Status', 'slug' => 'orders.update-status', 'module' => 'order'],
            ['name' => 'Cancel Orders', 'slug' => 'orders.cancel', 'module' => 'order'],
            ['name' => 'View Order Invoice', 'slug' => 'orders.invoice', 'module' => 'order'],
            
            // Customer Management
            ['name' => 'View Customers', 'slug' => 'customers.view', 'module' => 'order'],
            ['name' => 'Edit Customers', 'slug' => 'customers.edit', 'module' => 'order'],
            ['name' => 'Update Customer Info', 'slug' => 'customers.update-info', 'module' => 'order'],
            
            // Coupon Management
            ['name' => 'View Coupons', 'slug' => 'coupons.view', 'module' => 'order'],
            ['name' => 'Create Coupons', 'slug' => 'coupons.create', 'module' => 'order'],
            ['name' => 'Edit Coupons', 'slug' => 'coupons.edit', 'module' => 'order'],
            ['name' => 'Delete Coupons', 'slug' => 'coupons.delete', 'module' => 'order'],
            ['name' => 'View Coupon Statistics', 'slug' => 'coupons.statistics', 'module' => 'order'],
            
            // ===================================
            // DELIVERY MANAGEMENT MODULE
            // ===================================
            ['name' => 'View Delivery Zones', 'slug' => 'delivery-zones.view', 'module' => 'delivery'],
            ['name' => 'Create Delivery Zones', 'slug' => 'delivery-zones.create', 'module' => 'delivery'],
            ['name' => 'Edit Delivery Zones', 'slug' => 'delivery-zones.edit', 'module' => 'delivery'],
            ['name' => 'Delete Delivery Zones', 'slug' => 'delivery-zones.delete', 'module' => 'delivery'],
            ['name' => 'Toggle Zone Status', 'slug' => 'delivery-zones.toggle-status', 'module' => 'delivery'],
            
            ['name' => 'View Delivery Methods', 'slug' => 'delivery-methods.view', 'module' => 'delivery'],
            ['name' => 'Create Delivery Methods', 'slug' => 'delivery-methods.create', 'module' => 'delivery'],
            ['name' => 'Edit Delivery Methods', 'slug' => 'delivery-methods.edit', 'module' => 'delivery'],
            ['name' => 'Delete Delivery Methods', 'slug' => 'delivery-methods.delete', 'module' => 'delivery'],
            ['name' => 'Toggle Method Status', 'slug' => 'delivery-methods.toggle-status', 'module' => 'delivery'],
            
            ['name' => 'View Delivery Rates', 'slug' => 'delivery-rates.view', 'module' => 'delivery'],
            ['name' => 'Create Delivery Rates', 'slug' => 'delivery-rates.create', 'module' => 'delivery'],
            ['name' => 'Edit Delivery Rates', 'slug' => 'delivery-rates.edit', 'module' => 'delivery'],
            ['name' => 'Delete Delivery Rates', 'slug' => 'delivery-rates.delete', 'module' => 'delivery'],
            ['name' => 'Toggle Rate Status', 'slug' => 'delivery-rates.toggle-status', 'module' => 'delivery'],
            
            // ===================================
            // STOCK MANAGEMENT MODULE
            // ===================================
            ['name' => 'View Stock', 'slug' => 'stock.view', 'module' => 'stock'],
            ['name' => 'View Stock Movements', 'slug' => 'stock.movements', 'module' => 'stock'],
            ['name' => 'Add Stock', 'slug' => 'stock.add', 'module' => 'stock'],
            ['name' => 'Remove Stock', 'slug' => 'stock.remove', 'module' => 'stock'],
            ['name' => 'Adjust Stock', 'slug' => 'stock.adjust', 'module' => 'stock'],
            ['name' => 'Transfer Stock', 'slug' => 'stock.transfer', 'module' => 'stock'],
            ['name' => 'View Stock Alerts', 'slug' => 'stock.alerts', 'module' => 'stock'],
            ['name' => 'Resolve Stock Alerts', 'slug' => 'stock.alerts-resolve', 'module' => 'stock'],
            ['name' => 'View Current Stock', 'slug' => 'stock.current', 'module' => 'stock'],
            
            // Warehouse Management
            ['name' => 'View Warehouses', 'slug' => 'warehouses.view', 'module' => 'stock'],
            ['name' => 'Create Warehouses', 'slug' => 'warehouses.create', 'module' => 'stock'],
            ['name' => 'Edit Warehouses', 'slug' => 'warehouses.edit', 'module' => 'stock'],
            ['name' => 'Delete Warehouses', 'slug' => 'warehouses.delete', 'module' => 'stock'],
            ['name' => 'Set Default Warehouse', 'slug' => 'warehouses.set-default', 'module' => 'stock'],
            
            // Supplier Management
            ['name' => 'View Suppliers', 'slug' => 'suppliers.view', 'module' => 'stock'],
            ['name' => 'Create Suppliers', 'slug' => 'suppliers.create', 'module' => 'stock'],
            ['name' => 'Edit Suppliers', 'slug' => 'suppliers.edit', 'module' => 'stock'],
            ['name' => 'Delete Suppliers', 'slug' => 'suppliers.delete', 'module' => 'stock'],
            
            // ===================================
            // BLOG MANAGEMENT MODULE
            // ===================================
            ['name' => 'View Posts', 'slug' => 'posts.view', 'module' => 'blog'],
            ['name' => 'Create Posts', 'slug' => 'posts.create', 'module' => 'blog'],
            ['name' => 'Edit Posts', 'slug' => 'posts.edit', 'module' => 'blog'],
            ['name' => 'Delete Posts', 'slug' => 'posts.delete', 'module' => 'blog'],
            ['name' => 'Publish Posts', 'slug' => 'posts.publish', 'module' => 'blog'],
            ['name' => 'Upload Images', 'slug' => 'posts.upload-image', 'module' => 'blog'],
            ['name' => 'Manage Tick Marks', 'slug' => 'posts.tick-marks', 'module' => 'blog'],
            ['name' => 'Toggle Verification', 'slug' => 'posts.toggle-verification', 'module' => 'blog'],
            ['name' => 'Toggle Editor Choice', 'slug' => 'posts.toggle-editor-choice', 'module' => 'blog'],
            ['name' => 'Toggle Trending', 'slug' => 'posts.toggle-trending', 'module' => 'blog'],
            ['name' => 'Toggle Premium', 'slug' => 'posts.toggle-premium', 'module' => 'blog'],
            
            // Blog Categories
            ['name' => 'View Blog Categories', 'slug' => 'blog-categories.view', 'module' => 'blog'],
            ['name' => 'Create Blog Categories', 'slug' => 'blog-categories.create', 'module' => 'blog'],
            ['name' => 'Edit Blog Categories', 'slug' => 'blog-categories.edit', 'module' => 'blog'],
            ['name' => 'Delete Blog Categories', 'slug' => 'blog-categories.delete', 'module' => 'blog'],
            
            // Blog Tags
            ['name' => 'View Blog Tags', 'slug' => 'blog-tags.view', 'module' => 'blog'],
            ['name' => 'Create Blog Tags', 'slug' => 'blog-tags.create', 'module' => 'blog'],
            ['name' => 'Edit Blog Tags', 'slug' => 'blog-tags.edit', 'module' => 'blog'],
            ['name' => 'Delete Blog Tags', 'slug' => 'blog-tags.delete', 'module' => 'blog'],
            
            // Blog Comments
            ['name' => 'View Blog Comments', 'slug' => 'blog-comments.view', 'module' => 'blog'],
            ['name' => 'Approve Blog Comments', 'slug' => 'blog-comments.approve', 'module' => 'blog'],
            ['name' => 'Delete Blog Comments', 'slug' => 'blog-comments.delete', 'module' => 'blog'],
            
            // ===================================
            // CONTENT MANAGEMENT MODULE
            // ===================================
            ['name' => 'View Homepage Settings', 'slug' => 'homepage-settings.view', 'module' => 'content'],
            ['name' => 'Edit Homepage Settings', 'slug' => 'homepage-settings.edit', 'module' => 'content'],
            
            ['name' => 'View Promotional Banners', 'slug' => 'banners.view', 'module' => 'content'],
            ['name' => 'Create Promotional Banners', 'slug' => 'banners.create', 'module' => 'content'],
            ['name' => 'Edit Promotional Banners', 'slug' => 'banners.edit', 'module' => 'content'],
            ['name' => 'Delete Promotional Banners', 'slug' => 'banners.delete', 'module' => 'content'],
            ['name' => 'Toggle Banner Status', 'slug' => 'banners.toggle-status', 'module' => 'content'],
            
            ['name' => 'View Sale Offers', 'slug' => 'sale-offers.view', 'module' => 'content'],
            ['name' => 'Create Sale Offers', 'slug' => 'sale-offers.create', 'module' => 'content'],
            ['name' => 'Edit Sale Offers', 'slug' => 'sale-offers.edit', 'module' => 'content'],
            ['name' => 'Delete Sale Offers', 'slug' => 'sale-offers.delete', 'module' => 'content'],
            ['name' => 'Toggle Sale Offer Status', 'slug' => 'sale-offers.toggle-status', 'module' => 'content'],
            
            ['name' => 'Manage Secondary Menu', 'slug' => 'secondary-menu.manage', 'module' => 'content'],
            ['name' => 'Manage Footer', 'slug' => 'footer.manage', 'module' => 'content'],
            
            // Trending/Featured Products
            ['name' => 'Manage Trending Products', 'slug' => 'trending-products.manage', 'module' => 'content'],
            ['name' => 'Manage Best Sellers', 'slug' => 'best-sellers.manage', 'module' => 'content'],
            ['name' => 'Manage New Arrivals', 'slug' => 'new-arrivals.manage', 'module' => 'content'],
            
            // ===================================
            // REPORTS & ANALYTICS MODULE
            // ===================================
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'reports'],
            ['name' => 'View Sales Reports', 'slug' => 'reports.sales', 'module' => 'reports'],
            ['name' => 'View Inventory Reports', 'slug' => 'reports.inventory', 'module' => 'reports'],
            ['name' => 'View Product Reports', 'slug' => 'reports.products', 'module' => 'reports'],
            ['name' => 'View Customer Reports', 'slug' => 'reports.customers', 'module' => 'reports'],
            ['name' => 'View Delivery Reports', 'slug' => 'reports.delivery', 'module' => 'reports'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'module' => 'reports'],
            
            // ===================================
            // PAYMENT & FINANCE MODULE
            // ===================================
            ['name' => 'View Payment Gateways', 'slug' => 'payment-gateways.view', 'module' => 'finance'],
            ['name' => 'Edit Payment Gateways', 'slug' => 'payment-gateways.edit', 'module' => 'finance'],
            ['name' => 'Toggle Payment Gateway Status', 'slug' => 'payment-gateways.toggle-status', 'module' => 'finance'],
            
            ['name' => 'View Finance Reports', 'slug' => 'finance.view', 'module' => 'finance'],
            ['name' => 'View Transactions', 'slug' => 'finance.transactions', 'module' => 'finance'],
            ['name' => 'Export Finance Data', 'slug' => 'finance.export', 'module' => 'finance'],
            
            // ===================================
            // SYSTEM SETTINGS MODULE
            // ===================================
            ['name' => 'View Site Settings', 'slug' => 'settings.view', 'module' => 'system'],
            ['name' => 'Edit Site Settings', 'slug' => 'settings.edit', 'module' => 'system'],
            ['name' => 'Manage Logo', 'slug' => 'settings.logo', 'module' => 'system'],
            
            // System Settings (Cache & Maintenance)
            ['name' => 'View System Settings', 'slug' => 'system.settings.view', 'module' => 'system'],
            ['name' => 'Manage Cache', 'slug' => 'system.cache', 'module' => 'system'],
            ['name' => 'Manage Maintenance Mode', 'slug' => 'system.maintenance', 'module' => 'system'],
            ['name' => 'View System Logs', 'slug' => 'system.logs', 'module' => 'system'],
            
            // ===================================
            // FEEDBACK MODULE
            // ===================================
            ['name' => 'View Feedback', 'slug' => 'feedback.view', 'module' => 'feedback'],
            ['name' => 'Approve Feedback', 'slug' => 'feedback.approve', 'module' => 'feedback'],
            ['name' => 'Reject Feedback', 'slug' => 'feedback.reject', 'module' => 'feedback'],
            ['name' => 'Delete Feedback', 'slug' => 'feedback.delete', 'module' => 'feedback'],
            ['name' => 'Feature Feedback', 'slug' => 'feedback.feature', 'module' => 'feedback'],

            // ===================================
            // APPOINTMENT MODULE
            // ===================================
            ['name' => 'View Appointments', 'slug' => 'appointments.view', 'module' => 'appointments'],
            ['name' => 'Confirm Appointments', 'slug' => 'appointments.confirm', 'module' => 'appointments'],
            ['name' => 'Cancel Appointments', 'slug' => 'appointments.cancel', 'module' => 'appointments'],
            ['name' => 'Complete Appointments', 'slug' => 'appointments.complete', 'module' => 'appointments'],
            ['name' => 'Delete Appointments', 'slug' => 'appointments.delete', 'module' => 'appointments'],
            ['name' => 'Manage Chambers', 'slug' => 'chambers.manage', 'module' => 'appointments'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']], // Match by slug
                $permission // Update these fields
            );
        }

        // Create Roles (use firstOrCreate to avoid duplicates)
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Full system access with all permissions',
            'is_active' => true,
        ]);

        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
            'name' => 'Admin',
            'description' => 'Administrative access to most features',
            'is_active' => true,
        ]);

        $managerRole = Role::firstOrCreate(
            ['slug' => 'manager'],
            [
            'name' => 'Manager',
            'description' => 'Manage products, orders, and stock',
            'is_active' => true,
        ]);

        $editorRole = Role::firstOrCreate(
            ['slug' => 'editor'],
            [
            'name' => 'Content Editor',
            'description' => 'Manage blog posts and content',
            'is_active' => true,
        ]);

        $authorRole = Role::firstOrCreate(
            ['slug' => 'author'],
            [
            'name' => 'Author',
            'description' => 'Write and manage own blog posts',
            'is_active' => true,
        ]);

        $customerRole = Role::firstOrCreate(
            ['slug' => 'customer'],
            [
            'name' => 'Customer',
            'description' => 'Regular customer with basic access',
            'is_active' => true,
        ]);

        // ===================================
        // ASSIGN PERMISSIONS TO ROLES
        // ===================================
        
        // 1. SUPER ADMIN - Full System Access (All Permissions)
        $superAdminRole->permissions()->sync(Permission::all());
        $this->command->info('✓ Super Admin: All permissions (' . Permission::count() . ' permissions)');

        // 2. ADMIN - Administrative Access (All except user/role/system management)
        $adminPermissions = Permission::whereNotIn('module', ['user', 'system'])->get();
        $adminRole->permissions()->sync($adminPermissions);
        $this->command->info('✓ Admin: ' . $adminPermissions->count() . ' permissions');

        // 3. MANAGER - Business Operations (Product, Order, Stock, Delivery, Reports)
        $managerPermissions = Permission::whereIn('module', ['product', 'order', 'stock', 'delivery', 'reports'])
            ->where(function($query) {
                // Exclude sensitive actions
                $query->whereNotIn('slug', [
                    'products.delete',
                    'categories.delete',
                    'brands.delete',
                    'orders.delete',
                    'warehouses.delete',
                    'suppliers.delete'
                ]);
            })
            ->get();
        $managerRole->permissions()->sync($managerPermissions);
        $this->command->info('✓ Manager: ' . $managerPermissions->count() . ' permissions');

        // 4. CONTENT EDITOR - Full Blog & Content Management
        $editorPermissions = Permission::whereIn('module', ['blog', 'content'])->get();
        $editorRole->permissions()->sync($editorPermissions);
        $this->command->info('✓ Content Editor: ' . $editorPermissions->count() . ' permissions');

        // 5. AUTHOR - Blog Writing (Limited Blog Access)
        $authorPermissions = Permission::whereIn('slug', [
            // Blog Posts
            'posts.view',
            'posts.create',
            'posts.edit',
            'posts.publish',
            'posts.upload-image',
            'posts.tick-marks',
            'posts.toggle-verification',
            'posts.toggle-editor-choice',
            'posts.toggle-trending',
            'posts.toggle-premium',
            // Blog Categories (Read Only)
            'blog-categories.view',
            // Blog Tags (Read Only)
            'blog-tags.view',
            // Blog Comments
            'blog-comments.view',
            'blog-comments.approve',
            'blog-comments.delete',
        ])->get();
        $authorRole->permissions()->sync($authorPermissions);
        $this->command->info('✓ Author: ' . $authorPermissions->count() . ' permissions');

        // 6. CUSTOMER - No Admin Permissions (Frontend Only)
        $customerRole->permissions()->sync([]);
        $this->command->info('✓ Customer: 0 permissions (Frontend access only)');

        $this->command->info("\n========================================");
        $this->command->info('✓ Permission System Setup Complete!');
        $this->command->info('========================================');
        $this->command->info('Total Permissions: ' . Permission::count());
        $this->command->info('Total Roles: ' . Role::count());
        $this->command->info('========================================\n');
    }
}
