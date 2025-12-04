<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

/**
 * Global Admin Search Component
 * Searches admin panel navigations and features with permission filtering
 * 
 * @category Livewire
 * @package  App\Livewire\Admin
 * @created  2025-11-24
 */
class GlobalAdminSearch extends Component
{
    public $query = '';
    public $results = [];
    public $showResults = false;

    /**
     * Admin panel navigation items with routes, icons, and required permissions
     * ONLY includes routes that actually exist in the application
     */
    protected function getNavigationItems()
    {
        return [
            // Dashboard
            [
                'title' => 'Dashboard',
                'description' => 'Admin dashboard overview with statistics',
                'route' => 'admin.dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'permission' => null,
                'category' => 'Dashboard',
                'keywords' => ['home', 'overview', 'stats', 'statistics', 'analytics']
            ],
            
            // User Management
            [
                'title' => 'Users',
                'description' => 'Manage admin users',
                'route' => 'admin.users.index',
                'icon' => 'fas fa-users',
                'permission' => 'users.view',
                'category' => 'Users',
                'keywords' => ['admin users', 'staff', 'team', 'administrators']
            ],
            [
                'title' => 'Roles & Permissions',
                'description' => 'Manage user roles and permissions',
                'route' => 'admin.roles.index',
                'icon' => 'fas fa-shield-alt',
                'permission' => 'roles.view',
                'category' => 'Users',
                'keywords' => ['access control', 'permissions', 'authorization']
            ],
            [
                'title' => 'Email Preferences',
                'description' => 'Manage customer email notification preferences',
                'route' => 'admin.email-preferences.index',
                'icon' => 'fas fa-envelope-open-text',
                'permission' => 'users.view',
                'category' => 'Users',
                'keywords' => ['email settings', 'notifications', 'newsletter', 'promotions', 'order updates', 'recommendations', 'unsubscribe']
            ],
            [
                'title' => 'Email Setup Guide',
                'description' => 'Server setup and cron job configuration guide',
                'route' => 'admin.email-preferences.guideline',
                'icon' => 'fas fa-book',
                'permission' => 'users.view',
                'category' => 'Users',
                'keywords' => ['email setup', 'cron job', 'smtp', 'server configuration', 'automation', 'guideline', 'help']
            ],
            [
                'title' => 'Email Schedule Setup',
                'description' => 'Configure automated email campaign schedules',
                'route' => 'admin.email-preferences.schedule-setup',
                'icon' => 'fas fa-calendar-alt',
                'permission' => 'users.view',
                'category' => 'Users',
                'keywords' => ['schedule', 'automation', 'cron', 'timing', 'frequency', 'email schedule', 'campaigns']
            ],
            [
                'title' => 'Email Content Editor',
                'description' => 'Compose and test email campaigns with rich editor',
                'route' => 'admin.email-preferences.mail-setup',
                'icon' => 'fas fa-edit',
                'permission' => 'users.view',
                'category' => 'Users',
                'keywords' => ['email editor', 'compose', 'ckeditor', 'mail content', 'test email', 'template', 'newsletter']
            ],
            
            // E-commerce
            [
                'title' => 'Products',
                'description' => 'Manage products and variants',
                'route' => 'admin.products.index',
                'icon' => 'fas fa-box',
                'permission' => 'products.view',
                'category' => 'Ecommerce',
                'keywords' => ['items', 'catalog', 'inventory', 'variants']
            ],
            [
                'title' => 'Orders',
                'description' => 'View and manage customer orders',
                'route' => 'admin.orders.index',
                'icon' => 'fas fa-shopping-cart',
                'permission' => 'products.view',
                'category' => 'Ecommerce',
                'keywords' => ['sales', 'purchases', 'transactions', 'checkout']
            ],
            [
                'title' => 'Categories',
                'description' => 'Manage product categories',
                'route' => 'admin.categories.index',
                'icon' => 'fas fa-tags',
                'permission' => 'products.view',
                'category' => 'Ecommerce',
                'keywords' => ['category', 'taxonomy', 'classification']
            ],
            [
                'title' => 'Brands',
                'description' => 'Manage product brands',
                'route' => 'admin.brands.index',
                'icon' => 'fas fa-copyright',
                'permission' => 'products.view',
                'category' => 'Ecommerce',
                'keywords' => ['manufacturer', 'brand']
            ],
            [
                'title' => 'Product Attributes',
                'description' => 'Manage product attributes and variations',
                'route' => 'admin.attributes.index',
                'icon' => 'fas fa-sliders-h',
                'permission' => 'products.view',
                'category' => 'Ecommerce',
                'keywords' => ['size', 'color', 'attributes', 'variations', 'options']
            ],
            [
                'title' => 'Product Q&A',
                'description' => 'Manage customer product questions',
                'route' => 'admin.product-questions.index',
                'icon' => 'fas fa-question-circle',
                'permission' => 'products.view',
                'category' => 'Ecommerce',
                'keywords' => ['questions', 'answers', 'Q&A', 'queries']
            ],
            [
                'title' => 'Product Reviews',
                'description' => 'Manage customer product reviews',
                'route' => 'admin.reviews.index',
                'icon' => 'fas fa-star',
                'permission' => 'products.view',
                'category' => 'Ecommerce',
                'keywords' => ['ratings', 'feedback', 'testimonials', 'customer reviews']
            ],
            [
                'title' => 'Coupons',
                'description' => 'Manage discount coupons',
                'route' => 'admin.coupons.index',
                'icon' => 'fas fa-ticket-alt',
                'permission' => 'orders.view',
                'category' => 'Ecommerce',
                'keywords' => ['discount', 'promo', 'voucher', 'offer']
            ],
            
            // Stock Management
            [
                'title' => 'Stock Management',
                'description' => 'Manage product inventory and stock',
                'route' => 'admin.stock.index',
                'icon' => 'fas fa-boxes',
                'permission' => 'stock.view',
                'category' => 'Inventory',
                'keywords' => ['inventory', 'warehouse', 'quantity', 'stock levels']
            ],
            
            [
                'title' => 'Stock Reports',
                'description' => 'View stock reports with filters and exports',
                'route' => 'admin.stock.reports.index',
                'icon' => 'fas fa-chart-bar',
                'permission' => 'stock.view',
                'category' => 'Inventory',
                'keywords' => ['inventory report', 'stock analysis', 'low stock']
            ],
            [
                'title' => 'Warehouses',
                'description' => 'Manage warehouse locations',
                'route' => 'admin.warehouses.index',
                'icon' => 'fas fa-warehouse',
                'permission' => 'stock.view',
                'category' => 'Inventory',
                'keywords' => ['storage', 'location', 'facility']
            ],
            [
                'title' => 'Suppliers',
                'description' => 'Manage product suppliers',
                'route' => 'admin.suppliers.index',
                'icon' => 'fas fa-truck',
                'permission' => 'stock.view',
                'category' => 'Inventory',
                'keywords' => ['vendors', 'providers', 'distributors']
            ],
            
            // Shipping & Delivery
            [
                'title' => 'Delivery Zones',
                'description' => 'Manage delivery zones and areas',
                'route' => 'admin.delivery.zones.index',
                'icon' => 'fas fa-map-marked-alt',
                'permission' => 'orders.view',
                'category' => 'Settings',
                'keywords' => ['shipping zones', 'delivery areas', 'regions']
            ],
            [
                'title' => 'Delivery Methods',
                'description' => 'Configure delivery methods',
                'route' => 'admin.delivery.methods.index',
                'icon' => 'fas fa-shipping-fast',
                'permission' => 'orders.view',
                'category' => 'Settings',
                'keywords' => ['shipping methods', 'courier', 'delivery options']
            ],
            [
                'title' => 'Delivery Rates',
                'description' => 'Set delivery rates and pricing',
                'route' => 'admin.delivery.rates.index',
                'icon' => 'fas fa-dollar-sign',
                'permission' => 'orders.view',
                'category' => 'Settings',
                'keywords' => ['shipping cost', 'delivery price', 'rates']
            ],
            
            // Payments
            [
                'title' => 'Payment Gateways',
                'description' => 'Configure payment methods',
                'route' => 'admin.payment-gateways.index',
                'icon' => 'fas fa-credit-card',
                'permission' => 'orders.view',
                'category' => 'Settings',
                'keywords' => ['payments', 'bkash', 'nagad', 'ssl', 'gateway']
            ],
            
            // Reports & Analytics
            [
                'title' => 'Reports Dashboard',
                'description' => 'View reports dashboard',
                'route' => 'admin.reports.index',
                'icon' => 'fas fa-tachometer-alt',
                'permission' => 'products.view',
                'category' => 'Reports',
                'keywords' => ['reports', 'analytics', 'dashboard', 'stats']
            ],
            [
                'title' => 'Sales Report',
                'description' => 'View sales reports and analytics',
                'route' => 'admin.reports.sales',
                'icon' => 'fas fa-dollar-sign',
                'permission' => 'products.view',
                'category' => 'Reports',
                'keywords' => ['revenue', 'sales analytics', 'earnings']
            ],
            [
                'title' => 'Product Performance',
                'description' => 'View product performance reports',
                'route' => 'admin.reports.products',
                'icon' => 'fas fa-box',
                'permission' => 'products.view',
                'category' => 'Reports',
                'keywords' => ['bestsellers', 'product analytics']
            ],
            [
                'title' => 'Inventory Report',
                'description' => 'View inventory reports',
                'route' => 'admin.reports.inventory',
                'icon' => 'fas fa-warehouse',
                'permission' => 'products.view',
                'category' => 'Reports',
                'keywords' => ['stock report', 'inventory analytics']
            ],
            [
                'title' => 'Customer Report',
                'description' => 'View customer analytics',
                'route' => 'admin.reports.customers',
                'icon' => 'fas fa-users',
                'permission' => 'products.view',
                'category' => 'Reports',
                'keywords' => ['user analytics', 'customer stats']
            ],
            [
                'title' => 'Delivery Report',
                'description' => 'View delivery analytics',
                'route' => 'admin.reports.delivery',
                'icon' => 'fas fa-truck',
                'permission' => 'products.view',
                'category' => 'Reports',
                'keywords' => ['shipping report', 'delivery analytics']
            ],
            
            // Blog
            [
                'title' => 'Blog Posts',
                'description' => 'Manage blog articles',
                'route' => 'admin.blog.posts.index',
                'icon' => 'fas fa-file-alt',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['articles', 'news', 'blog', 'posts']
            ],
            [
                'title' => 'Blog Categories',
                'description' => 'Manage blog categories',
                'route' => 'admin.blog.categories.index',
                'icon' => 'fas fa-folder',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['blog taxonomy', 'categories']
            ],
            [
                'title' => 'Blog Tags',
                'description' => 'Manage blog tags',
                'route' => 'admin.blog.tags.index',
                'icon' => 'fas fa-tag',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['blog labels', 'tags']
            ],
            [
                'title' => 'Blog Comments',
                'description' => 'Moderate blog comments',
                'route' => 'admin.blog.comments.index',
                'icon' => 'fas fa-comments',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['moderation', 'feedback', 'comments']
            ],
            
            // Content Management
            [
                'title' => 'Homepage Settings',
                'description' => 'Configure homepage layout',
                'route' => 'admin.homepage-settings.index',
                'icon' => 'fas fa-home',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['landing page', 'hero', 'slider', 'homepage']
            ],
            [
                'title' => 'Secondary Menu',
                'description' => 'Manage secondary navigation',
                'route' => 'admin.secondary-menu.index',
                'icon' => 'fas fa-bars',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['navigation', 'menu', 'links']
            ],
            [
                'title' => 'Footer Management',
                'description' => 'Manage footer content',
                'route' => 'admin.footer-management.index',
                'icon' => 'fas fa-shoe-prints',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['footer links', 'footer content']
            ],
            
            // System Settings - Main Pages
            [
                'title' => 'Site Settings',
                'description' => 'General website configuration',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-cog',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['configuration', 'general settings', 'website']
            ],
            [
                'title' => 'Theme Settings',
                'description' => 'Customize appearance',
                'route' => 'admin.theme-settings.index',
                'icon' => 'fas fa-palette',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['colors', 'design', 'appearance', 'styling']
            ],
            
            // Site Settings - Individual Settings (General)
            [
                'title' => 'Site Name',
                'description' => 'Configure website name',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-signature',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['site name', 'website name', 'brand name', 'general settings']
            ],
            [
                'title' => 'Site Tagline',
                'description' => 'Configure website tagline/slogan',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-quote-right',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['tagline', 'slogan', 'subtitle', 'general settings']
            ],
            [
                'title' => 'Contact Email',
                'description' => 'Configure primary contact email',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-envelope',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['email', 'contact', 'support email', 'general settings']
            ],
            [
                'title' => 'Contact Phone',
                'description' => 'Configure primary contact phone number',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-phone',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['phone', 'contact', 'telephone', 'general settings']
            ],
            [
                'title' => 'TinyMCE API Key',
                'description' => 'Configure TinyMCE rich text editor API key',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-key',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['tinymce', 'editor', 'api key', 'general settings']
            ],
            
            // Site Settings - Appearance
            [
                'title' => 'Site Logo',
                'description' => 'Upload website logo',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-image',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['logo', 'brand logo', 'site logo', 'appearance']
            ],
            [
                'title' => 'Favicon',
                'description' => 'Upload website favicon',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-star',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['favicon', 'icon', 'browser icon', 'appearance']
            ],
            [
                'title' => 'Admin Panel Logo',
                'description' => 'Upload admin panel logo',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-shield-alt',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['admin logo', 'panel logo', 'appearance']
            ],
            [
                'title' => 'Primary Color',
                'description' => 'Configure primary brand color',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-palette',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['color', 'brand color', 'theme color', 'appearance']
            ],
            
            // Site Settings - Social Media
            [
                'title' => 'Facebook URL',
                'description' => 'Configure Facebook page URL',
                'route' => 'admin.site-settings.index',
                'icon' => 'fab fa-facebook',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['facebook', 'social media', 'social links']
            ],
            [
                'title' => 'Twitter URL',
                'description' => 'Configure Twitter profile URL',
                'route' => 'admin.site-settings.index',
                'icon' => 'fab fa-twitter',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['twitter', 'social media', 'social links']
            ],
            [
                'title' => 'Instagram URL',
                'description' => 'Configure Instagram profile URL',
                'route' => 'admin.site-settings.index',
                'icon' => 'fab fa-instagram',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['instagram', 'social media', 'social links']
            ],
            [
                'title' => 'YouTube URL',
                'description' => 'Configure YouTube channel URL',
                'route' => 'admin.site-settings.index',
                'icon' => 'fab fa-youtube',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['youtube', 'social media', 'social links']
            ],
            
            // Site Settings - SEO
            [
                'title' => 'Meta Description',
                'description' => 'Configure website meta description for SEO',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-search',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['meta description', 'seo', 'description', 'search engine']
            ],
            [
                'title' => 'Meta Keywords',
                'description' => 'Configure website meta keywords for SEO',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-tags',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['meta keywords', 'seo', 'keywords', 'search engine']
            ],
            
            // Site Settings - Blog Settings
            [
                'title' => 'Blog Title',
                'description' => 'Configure blog section title',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-heading',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['blog title', 'blog settings', 'blog']
            ],
            [
                'title' => 'Blog Tagline',
                'description' => 'Configure blog tagline',
                'route' => 'admin.site-settings.index',
                'icon' => 'fas fa-quote-left',
                'permission' => 'users.view',
                'category' => 'Settings',
                'keywords' => ['blog tagline', 'blog subtitle', 'blog settings']
            ],
            
            // Homepage Settings - Individual Settings
            [
                'title' => 'Homepage Title',
                'description' => 'Configure homepage main title',
                'route' => 'admin.homepage-settings.index',
                'icon' => 'fas fa-heading',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['homepage title', 'site title', 'hero title', 'homepage']
            ],
            [
                'title' => 'Featured Products Section',
                'description' => 'Configure featured products display',
                'route' => 'admin.homepage-settings.index',
                'icon' => 'fas fa-star',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['featured products', 'homepage', 'featured section']
            ],
            [
                'title' => 'Homepage Banner',
                'description' => 'Configure promotional banner',
                'route' => 'admin.homepage-settings.index',
                'icon' => 'fas fa-ad',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['banner', 'promo banner', 'promotional', 'homepage']
            ],
            [
                'title' => 'Top Header Links',
                'description' => 'Configure top header announcement links',
                'route' => 'admin.homepage-settings.index',
                'icon' => 'fas fa-link',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['top header', 'header links', 'announcement', 'homepage']
            ],
            [
                'title' => 'Hero Slider',
                'description' => 'Configure homepage hero slider',
                'route' => 'admin.homepage-settings.index',
                'icon' => 'fas fa-images',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['slider', 'hero slider', 'carousel', 'homepage']
            ],
            
            // Footer Management - Individual Settings
            [
                'title' => 'Newsletter Settings',
                'description' => 'Configure newsletter subscription section',
                'route' => 'admin.footer-management.index',
                'icon' => 'fas fa-envelope-open-text',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['newsletter', 'subscription', 'footer', 'email signup']
            ],
            [
                'title' => 'Footer Social Media',
                'description' => 'Configure footer social media links',
                'route' => 'admin.footer-management.index',
                'icon' => 'fas fa-share-alt',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['social media', 'footer', 'facebook', 'twitter', 'instagram']
            ],
            [
                'title' => 'Mobile Apps Footer',
                'description' => 'Configure mobile apps section in footer',
                'route' => 'admin.footer-management.index',
                'icon' => 'fas fa-mobile-alt',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['mobile apps', 'footer', 'app store', 'google play', 'qr code']
            ],
            [
                'title' => 'Footer Links',
                'description' => 'Manage footer navigation links',
                'route' => 'admin.footer-management.index',
                'icon' => 'fas fa-link',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['footer links', 'navigation', 'footer menu', 'about', 'company', 'resources']
            ],
            [
                'title' => 'Rewards Section Footer',
                'description' => 'Configure rewards program section',
                'route' => 'admin.footer-management.index',
                'icon' => 'fas fa-gift',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['rewards', 'footer', 'loyalty program', 'rewards program']
            ],
            [
                'title' => 'Copyright Text',
                'description' => 'Configure copyright notice',
                'route' => 'admin.footer-management.index',
                'icon' => 'fas fa-copyright',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['copyright', 'footer', 'legal', 'copyright notice']
            ],
            [
                'title' => 'Footer Blog Posts',
                'description' => 'Manage featured blog posts in footer',
                'route' => 'admin.footer-management.index',
                'icon' => 'fas fa-newspaper',
                'permission' => 'users.view',
                'category' => 'Content',
                'keywords' => ['footer blog', 'blog posts', 'footer', 'featured posts']
            ],
            
        ];
    }

    /**
     * Update search results when query changes
     */
    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            $this->showResults = false;
            return;
        }

        $user = Auth::user();
        $searchQuery = strtolower($this->query);
        $items = $this->getNavigationItems();
        
        $this->results = collect($items)->filter(function($item) use ($user, $searchQuery) {
            // Check permission
            if ($item['permission'] && !$user->hasPermission($item['permission'])) {
                return false;
            }
            
            // Search in title, description, and keywords
            $searchableText = strtolower(
                $item['title'] . ' ' . 
                $item['description'] . ' ' . 
                implode(' ', $item['keywords'])
            );
            
            return str_contains($searchableText, $searchQuery);
        })
        ->take(8)
        ->values()
        ->toArray();

        $this->showResults = true;
    }

    /**
     * Clear search
     */
    public function clearSearch()
    {
        $this->query = '';
        $this->results = [];
        $this->showResults = false;
    }

    /**
     * Navigate to selected item
     */
    public function selectItem($route)
    {
        $this->clearSearch();
        return redirect()->route($route);
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.admin.global-admin-search');
    }
}
