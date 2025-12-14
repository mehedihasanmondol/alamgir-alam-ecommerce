<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * ModuleName: Site Settings Seeder
 * Purpose: Seed default site settings including logo and branding
 * 
 * @category Seeders
 * @package  Database\Seeders
 * @created  2025-11-11
 */
class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Only creates new settings or updates existing ones if values differ.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'iHerb',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Site Name',
                'description' => 'The name of your website',
                'order' => 1,
            ],
            [
                'key' => 'tinymce_api_key',
                'value' => '8wacbe3zs5mntet5c9u50n4tenlqvgqm9bn1k6uctyqo3o7m',
                'type' => 'text',
                'group' => 'general',
                'label' => 'TinyMCE API Key',
                'description' => 'API key for TinyMCE rich text editor (get from tiny.cloud)',
                'order' => 2,
            ],
            [
                'key' => 'site_tagline',
                'value' => 'Your Health & Wellness Store',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Site Tagline',
                'description' => 'A short description or tagline for your site',
                'order' => 2,
            ],
            [
                'key' => 'site_email',
                'value' => 'support@iherb.com',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Contact Email',
                'description' => 'Primary contact email address',
                'order' => 3,
            ],
            [
                'key' => 'site_phone',
                'value' => '+1-800-123-4567',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Contact Phone',
                'description' => 'Primary contact phone number',
                'order' => 4,
            ],

            // Appearance Settings
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'appearance',
                'label' => 'Site Logo',
                'description' => 'Upload your site logo (recommended size: 200x60px)',
                'order' => 1,
            ],
            [
                'key' => 'site_favicon',
                'value' => null,
                'type' => 'image',
                'group' => 'appearance',
                'label' => 'Favicon',
                'description' => 'Upload your site favicon (recommended size: 32x32px)',
                'order' => 2,
            ],
            [
                'key' => 'admin_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'appearance',
                'label' => 'Admin Panel Logo',
                'description' => 'Upload logo for admin panel (recommended size: 180x50px)',
                'order' => 3,
            ],
            [
                'key' => 'primary_color',
                'value' => '#16a34a',
                'type' => 'text',
                'group' => 'appearance',
                'label' => 'Primary Color',
                'description' => 'Primary brand color (hex code)',
                'order' => 4,
            ],

            // Social Media
            [
                'key' => 'facebook_url',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'label' => 'Facebook URL',
                'description' => 'Your Facebook page URL',
                'order' => 1,
            ],
            [
                'key' => 'twitter_url',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'label' => 'Twitter URL',
                'description' => 'Your Twitter profile URL',
                'order' => 2,
            ],
            [
                'key' => 'instagram_url',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'label' => 'Instagram URL',
                'description' => 'Your Instagram profile URL',
                'order' => 3,
            ],
            [
                'key' => 'youtube_url',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'label' => 'YouTube URL',
                'description' => 'Your YouTube channel URL',
                'order' => 4,
            ],

            // SEO Settings
            [
                'key' => 'meta_description',
                'value' => 'Shop for health and wellness products online',
                'type' => 'textarea',
                'group' => 'seo',
                'label' => 'Meta Description',
                'description' => 'Default meta description for your site',
                'order' => 1,
            ],
            [
                'key' => 'meta_keywords',
                'value' => 'health, wellness, supplements, vitamins',
                'type' => 'textarea',
                'group' => 'seo',
                'label' => 'Meta Keywords',
                'description' => 'Default meta keywords for your site',
                'order' => 2,
            ],

            // Feedback Settings
            [
                'key' => 'feedback_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'feedback',
                'label' => 'Enable Feedback',
                'description' => 'Enable or disable feedback functionality',
                'order' => 1,
            ],
            [
                'key' => 'feedback_rating_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'feedback',
                'label' => 'Enable Rating',
                'description' => 'Enable or disable rating functionality for feedback',
                'order' => 2,
            ],
            [
                'key' => 'feedback_per_page_frontend',
                'value' => '10',
                'type' => 'number',
                'group' => 'feedback',
                'label' => 'Feedback Per Page (Frontend)',
                'description' => 'Number of feedback to display per page on frontend',
                'order' => 3,
            ],
            [
                'key' => 'feedback_per_author_page',
                'value' => '5',
                'type' => 'number',
                'group' => 'feedback',
                'label' => 'Feedback Per Author Page',
                'description' => 'Number of feedback to display per author page',
                'order' => 4,
            ],
            [
                'key' => 'feedback_email_required',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'feedback',
                'label' => 'Email Required',
                'description' => 'Require email address for feedback submission',
                'order' => 5,
            ],
            [
                'key' => 'feedback_auto_approve',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'feedback',
                'label' => 'Auto Approve Feedback',
                'description' => 'Automatically approve feedback submissions',
                'order' => 6,
            ],
            [
                'key' => 'feedback_show_images',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'feedback',
                'label' => 'Show Images',
                'description' => 'Display images in feedback submissions',
                'order' => 7,
            ],
            [
                'key' => 'feedback_title',
                'value' => 'Customer Feedback',
                'type' => 'text',
                'group' => 'feedback',
                'label' => 'Feedback Section Title',
                'description' => 'The title displayed for the feedback section',
                'order' => 8,
            ],
            [
                'key' => 'feedback_time_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'feedback',
                'label' => 'Show Time',
                'description' => 'Display time posted for feedback items',
                'order' => 9,
            ],
            [
                'key' => 'feedback_helpful_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'feedback',
                'label' => 'Enable Helpful Votes',
                'description' => 'Enable or disable helpful/not helpful voting buttons',
                'order' => 10,
            ],

            // Author Page Layout Settings
            [
                'key' => 'author_page_appointment_width',
                'value' => 'full',
                'type' => 'select',
                'group' => 'author_page',
                'label' => 'Appointment Form Width',
                'description' => 'Width of the appointment form section on author page',
                'options' => json_encode(['full' => 'Full Width', 'half' => 'Half Width', 'one-third' => 'One Third', 'two-third' => 'Two third', 'quarter' => 'Quarter Width', 'three-quarter' => 'Three Quarter']),
                'order' => 1,
            ],
            [
                'key' => 'author_page_feedback_width',
                'value' => 'full',
                'type' => 'select',
                'group' => 'author_page',
                'label' => 'Feedback Section Width',
                'description' => 'Width of the feedback section on author page',
                'options' => json_encode(['full' => 'Full Width', 'half' => 'Half Width', 'one-third' => 'One Third', 'two-third' => 'Two third', 'quarter' => 'Quarter Width', 'three-quarter' => 'Three Quarter']),
                'order' => 2,
            ],

            // Blog Settings
            [
                'key' => 'blog_title',
                'value' => 'Health & Wellness Blog',
                'type' => 'text',
                'group' => 'blog',
                'label' => 'Blog Title',
                'description' => 'The main title for your blog section',
                'order' => 1,
            ],
            [
                'key' => 'blog_tagline',
                'value' => 'Your source for health tips, wellness advice, and product insights',
                'type' => 'text',
                'group' => 'blog',
                'label' => 'Blog Tagline',
                'description' => 'A short tagline or subtitle for your blog',
                'order' => 2,
            ],
            [
                'key' => 'blog_description',
                'value' => 'Discover the latest health and wellness tips, product reviews, and expert advice to help you live your best life. Our blog covers everything from nutrition and fitness to natural remedies and lifestyle tips.',
                'type' => 'textarea',
                'group' => 'blog',
                'label' => 'Blog Description',
                'description' => 'A detailed description of your blog for SEO and about sections',
                'order' => 3,
            ],
            [
                'key' => 'blog_keywords',
                'value' => 'health blog, wellness tips, nutrition advice, fitness, supplements, natural health, lifestyle, product reviews',
                'type' => 'textarea',
                'group' => 'blog',
                'label' => 'Blog Keywords',
                'description' => 'SEO keywords for your blog (comma-separated)',
                'order' => 4,
            ],
            [
                'key' => 'blog_image',
                'value' => null,
                'type' => 'image',
                'group' => 'blog',
                'label' => 'Blog SEO Image',
                'description' => 'Default image for blog page social media sharing (recommended size: 1200x630px)',
                'order' => 5,
            ],
            [
                'key' => 'blog_posts_per_page',
                'value' => '12',
                'type' => 'text',
                'group' => 'blog',
                'label' => 'Posts Per Page',
                'description' => 'Number of blog posts to display per page',
                'order' => 6,
            ],
            [
                'key' => 'blog_show_author',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'blog',
                'label' => 'Show Author',
                'description' => 'Display author information on blog posts',
                'order' => 7,
            ],
            [
                'key' => 'blog_show_date',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'blog',
                'label' => 'Show Date',
                'description' => 'Display publication date on blog posts',
                'order' => 8,
            ],
            [
                'key' => 'blog_show_comments',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'blog',
                'label' => 'Enable Comments',
                'description' => 'Allow comments on blog posts',
                'order' => 9,
            ],
            [
                'key' => 'blog_show_tags',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'blog',
                'label' => 'Show Tags',
                'description' => 'Display tags on blog posts',
                'order' => 10,
            ],
            [
                'key' => 'blog_show_views',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'blog',
                'label' => 'Show Views Count',
                'description' => 'Display total views count on blog posts',
                'order' => 11,
            ],
            [
                'key' => 'blog_show_reading_time',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'blog',
                'label' => 'Show Reading Time',
                'description' => 'Display estimated reading time on blog posts',
                'order' => 12,
            ],

            // Homepage Settings
            [
                'key' => 'homepage_type',
                'value' => 'default',
                'type' => 'select',
                'group' => 'homepage',
                'label' => 'Homepage Type',
                'description' => 'Select what content to display on the homepage',
                'order' => 1,
            ],
            [
                'key' => 'homepage_author_id',
                'value' => null,
                'type' => 'select',
                'group' => 'homepage',
                'label' => 'Featured Author',
                'description' => 'Select an author to display (only for Author Profile homepage type)',
                'order' => 2,
            ],

            // Stock Settings
            [
                'key' => 'manual_stock_update_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'stock',
                'label' => 'Enable Manual Stock Updates',
                'description' => 'Allow manual stock updates in product edit form. If disabled, stock can only be managed via Stock Management system.',
                'order' => 1,
            ],
            [
                'key' => 'enable_out_of_stock_restriction',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'stock',
                'label' => 'Enable Out of Stock Restriction',
                'description' => 'When ENABLED: Users cannot add/order out-of-stock products, stock quantity is visible, "Out of Stock" messages shown. When DISABLED: Users can order out-of-stock products, stock quantity is completely hidden from frontend.',
                'order' => 2,
            ],

            // Invoice Settings
            [
                'key' => 'invoice_header_banner',
                'value' => null,
                'type' => 'image',
                'group' => 'invoice',
                'label' => 'Invoice Header Banner',
                'description' => 'Upload invoice header banner/logo (recommended size: 800x150px)',
                'order' => 1,
            ],
            [
                'key' => 'invoice_company_name',
                'value' => 'iHerb',
                'type' => 'text',
                'group' => 'invoice',
                'label' => 'Company Name',
                'description' => 'Company name to display on invoices',
                'order' => 2,
            ],
            [
                'key' => 'invoice_company_address',
                'value' => '123 Business Street, Dhaka 1000, Bangladesh',
                'type' => 'textarea',
                'group' => 'invoice',
                'label' => 'Company Address',
                'description' => 'Full company address for invoices',
                'order' => 3,
            ],
            [
                'key' => 'invoice_company_phone',
                'value' => '+880 1XXX-XXXXXX',
                'type' => 'text',
                'group' => 'invoice',
                'label' => 'Company Phone',
                'description' => 'Company phone number for invoices',
                'order' => 4,
            ],
            [
                'key' => 'invoice_company_email',
                'value' => 'info@iherb.com',
                'type' => 'text',
                'group' => 'invoice',
                'label' => 'Company Email',
                'description' => 'Company email for invoices',
                'order' => 5,
            ],
            [
                'key' => 'invoice_footer_text',
                'value' => 'Thank you for your business!',
                'type' => 'textarea',
                'group' => 'invoice',
                'label' => 'Footer Text',
                'description' => 'Text to display at the bottom of invoices',
                'order' => 6,
            ],
            [
                'key' => 'invoice_footer_note',
                'value' => 'This is a computer-generated invoice and does not require a signature.',
                'type' => 'textarea',
                'group' => 'invoice',
                'label' => 'Footer Note',
                'description' => 'Additional note for invoice footer',
                'order' => 7,
            ],

            // Login Page Settings
            [
                'key' => 'login_page_title',
                'value' => 'Why iHerb?',
                'type' => 'text',
                'group' => 'login',
                'label' => 'Login Page Title',
                'description' => 'Title for the right side content section',
                'order' => 1,
            ],
            [
                'key' => 'login_page_content',
                'value' => '<div class="space-y-5">
    <!-- Feature 1 -->
    <div class="flex items-start">
        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
        </div>
        <div class="ml-4">
            <p class="text-sm text-gray-700">
                Guaranteed freshness through our commitment to Good Manufacturing Practices (GMP).
            </p>
        </div>
    </div>

    <!-- Feature 2 -->
    <div class="flex items-start">
        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
        </div>
        <div class="ml-4">
            <p class="text-sm text-gray-700">
                Genuine reviews only from verified customers
            </p>
        </div>
    </div>

    <!-- Feature 3 -->
    <div class="flex items-start">
        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
        </div>
        <div class="ml-4">
            <p class="text-sm text-gray-700">
                No third-party sales. Direct from suppliers and authorised distributors
            </p>
        </div>
    </div>

    <!-- Feature 4 -->
    <div class="flex items-start">
        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
        </div>
        <div class="ml-4">
            <p class="text-sm text-gray-700">
                Independent lab testing on iHerb\'s House Brands
            </p>
        </div>
    </div>

    <!-- Feature 5 -->
    <div class="flex items-start">
        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <div class="ml-4">
            <p class="text-sm text-gray-700">
                Expiration dates on product descriptions
            </p>
        </div>
    </div>

    <!-- Feature 6 -->
    <div class="flex items-start">
        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
        </div>
        <div class="ml-4">
            <p class="text-sm text-gray-700">
                24/7 customer support. Easy returns and refunds.
            </p>
        </div>
    </div>

    <!-- Reviews -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center">
                        <span class="text-2xl font-bold text-gray-900">4.8</span>
                        <div class="ml-2 flex">
                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">iHerb</p>
                    <p class="text-xs text-gray-500">Store Reviews</p>
                </div>
            </div>
        </div>
    </div>
</div>',
                'type' => 'ckeditor',
                'group' => 'login',
                'label' => 'Login Page Content',
                'description' => 'Rich content for the right side of login page (HTML supported)',
                'order' => 2,
            ],
            [
                'key' => 'enable_google_login',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'login',
                'label' => 'Enable Google Login',
                'description' => 'Allow users to sign in with their Google account',
                'order' => 3,
            ],
            [
                'key' => 'enable_facebook_login',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'login',
                'label' => 'Enable Facebook Login',
                'description' => 'Allow users to sign in with their Facebook account',
                'order' => 4,
            ],
            [
                'key' => 'enable_apple_login',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'login',
                'label' => 'Enable Apple Login',
                'description' => 'Allow users to sign in with their Apple account',
                'order' => 5,
            ],
            [
                'key' => 'login_terms_conditions',
                'value' => '<p>By continuing, you\'ve read and agree to our <a href="/terms-and-conditions" class="text-blue-600 hover:underline">Terms and Conditions</a> and <a href="/privacy-policy" class="text-blue-600 hover:underline">Privacy Policy</a>.</p>',
                'type' => 'ckeditor',
                'group' => 'login',
                'label' => 'Terms & Conditions Text',
                'description' => 'Terms and conditions text displayed on login page (HTML supported)',
                'order' => 6,
            ],
            [
                'key' => 'login_help_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'login',
                'label' => 'Enable Help Link',
                'description' => 'Show "Need help?" link on login page',
                'order' => 7,
            ],
            [
                'key' => 'login_help_url',
                'value' => '/help/login',
                'type' => 'text',
                'group' => 'login',
                'label' => 'Help Link URL',
                'description' => 'URL for the "Need help?" link',
                'order' => 8,
            ],
            [
                'key' => 'login_help_text',
                'value' => 'Need help?',
                'type' => 'text',
                'group' => 'login',
                'label' => 'Help Link Text',
                'description' => 'Text to display for the help link',
                'order' => 9,
            ],

            // Social Authentication Settings (Login Group)
            [
                'key' => 'google_client_id',
                'value' => '',
                'type' => 'text',
                'group' => 'login',
                'label' => 'Google Client ID',
                'description' => 'OAuth 2.0 Client ID from Google Cloud Console',
                'order' => 10,
            ],
            [
                'key' => 'google_client_secret',
                'value' => '',
                'type' => 'text',
                'group' => 'login',
                'label' => 'Google Client Secret',
                'description' => 'OAuth 2.0 Client Secret from Google Cloud Console',
                'order' => 11,
            ],
            [
                'key' => 'google_redirect_url',
                'value' => '',
                'type' => 'text',
                'group' => 'login',
                'label' => 'Google Redirect URL',
                'description' => 'Leave empty to use default: {APP_URL}/login/google/callback',
                'order' => 12,
            ],
            [
                'key' => 'facebook_client_id',
                'value' => '',
                'type' => 'text',
                'group' => 'login',
                'label' => 'Facebook App ID',
                'description' => 'Facebook App ID from Facebook Developers',
                'order' => 13,
            ],
            [
                'key' => 'facebook_client_secret',
                'value' => '',
                'type' => 'text',
                'group' => 'login',
                'label' => 'Facebook App Secret',
                'description' => 'Facebook App Secret from Facebook Developers',
                'order' => 14,
            ],
            [
                'key' => 'facebook_redirect_url',
                'value' => '',
                'type' => 'text',
                'group' => 'login',
                'label' => 'Facebook Redirect URL',
                'description' => 'Leave empty to use default: {APP_URL}/login/facebook/callback',
                'order' => 15,
            ],
            [
                'key' => 'apple_client_id',
                'value' => '',
                'type' => 'text',
                'group' => 'login',
                'label' => 'Apple Service ID',
                'description' => 'Apple Service ID from Apple Developer',
                'order' => 16,
            ],
            [
                'key' => 'apple_client_secret',
                'value' => '',
                'type' => 'text',
                'group' => 'login',
                'label' => 'Apple Client Secret',
                'description' => 'Apple Client Secret (generated from private key)',
                'order' => 17,
            ],
            [
                'key' => 'apple_redirect_url',
                'value' => '',
                'type' => 'text',
                'group' => 'login',
                'label' => 'Apple Redirect URL',
                'description' => 'Leave empty to use default: {APP_URL}/login/apple/callback',
                'order' => 18,
            ],
        ];

        foreach ($settings as $setting) {
            $this->upsertSetting($setting);
        }

        // Search Engine Verification Settings
        $verificationSettings = [
            [
                'key' => 'google_verification',
                'value' => '',
                'type' => 'text',
                'group' => 'seo',
                'label' => 'Google Search Console Verification',
                'description' => 'Google verification meta tag content (e.g., google-site-verification code)',
                'order' => 100,
            ],
            [
                'key' => 'bing_verification',
                'value' => '',
                'type' => 'text',
                'group' => 'seo',
                'label' => 'Bing Webmaster Verification',
                'description' => 'Bing verification meta tag content',
                'order' => 101,
            ],
            [
                'key' => 'yandex_verification',
                'value' => '',
                'type' => 'text',
                'group' => 'seo',
                'label' => 'Yandex Webmaster Verification',
                'description' => 'Yandex verification meta tag content',
                'order' => 102,
            ],
            [
                'key' => 'pinterest_verification',
                'value' => '',
                'type' => 'text',
                'group' => 'seo',
                'label' => 'Pinterest Site Verification',
                'description' => 'Pinterest verification meta tag content',
                'order' => 103,
            ],
            [
                'key' => 'robots_txt_custom',
                'value' => '',
                'type' => 'textarea',
                'group' => 'seo',
                'label' => 'Custom Robots.txt Rules',
                'description' => 'Additional custom rules for robots.txt (optional)',
                'order' => 104,
            ],
            [
                'key' => 'sitemap_enabled',
                'value' => '1',
                'type' => 'select',
                'group' => 'seo',
                'label' => 'Enable Sitemap',
                'description' => 'Enable automatic sitemap generation',
                'options' => json_encode(['1' => 'Enabled', '0' => 'Disabled']),
                'order' => 105,
            ],
        ];

        foreach ($verificationSettings as $setting) {
            $this->upsertSetting($setting);
        }

        // Product Features Settings
        $featureSettings = [
            [
                'key' => 'enable_product_reviews',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'product_features',
                'label' => 'Enable Product Reviews',
                'description' => 'Allow customers to submit and view product reviews on the site',
                'order' => 1,
            ],
            [
                'key' => 'enable_product_qna',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'product_features',
                'label' => 'Enable Product Q&A',
                'description' => 'Allow customers to ask questions and view answers on product pages',
                'order' => 2,
            ],
            [
                'key' => 'enable_product_specifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'product_features',
                'label' => 'Enable Product Specifications',
                'description' => 'Display product specifications (category, product code, shipping weight, dimensions) on product detail pages',
                'order' => 3,
            ],
        ];

        foreach ($featureSettings as $setting) {
            $this->upsertSetting($setting);
        }

        // Appointment Settings
        $appointmentSettings = [
            [
                'key' => 'appointment_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'appointment',
                'label' => 'Enable Appointments',
                'description' => 'Enable/disable the appointment booking system',
                'order' => 1,
            ],
            [
                'key' => 'appointment_heading',
                'value' => 'অ্যাপয়েন্টমেন্ট বুক করুন',
                'type' => 'text',
                'group' => 'appointment',
                'label' => 'Appointment Form Heading',
                'description' => 'Heading text for the appointment form',
                'order' => 2,
            ],
            [
                'key' => 'appointment_alert_message',
                'value' => 'দয়া করে নোট করুন: আপনার অ্যাপয়েন্টমেন্ট রিকোয়েস্ট গ্রহণ করার পর আমরা শীঘ্রই ফোন করে আপনাকে কনফার্মেশন জানাব।',
                'type' => 'textarea',
                'group' => 'appointment',
                'label' => 'Alert Message',
                'description' => 'Alert message shown before the appointment form',
                'order' => 3,
            ],
            [
                'key' => 'appointment_success_message',
                'value' => 'আপনার অ্যাপয়েন্টমেন্ট রিকোয়েস্ট সফলভাবে জমা হয়েছে। আমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।',
                'type' => 'textarea',
                'group' => 'appointment',
                'label' => 'Success Message',
                'description' => 'Message shown after successful appointment submission',
                'order' => 4,
            ],
            [
                'key' => 'appointment_default_chamber',
                'value' => '1',
                'type' => 'number',
                'group' => 'appointment',
                'label' => 'Default Chamber ID',
                'description' => 'Default chamber to pre-select in appointment form',
                'order' => 5,
            ],
        ];

        foreach ($appointmentSettings as $setting) {
            $this->upsertSetting($setting);
        }

        // YouTube Feedback Import Settings
        $youtubeFeedbackSettings = [
            [
                'key' => 'youtube_api_key',
                'value' => '',
                'type' => 'password',
                'group' => 'feedback_sites',
                'label' => 'YouTube API Key',
                'description' => 'YouTube Data API v3 key from Google Cloud Console. Required for importing comments.',
                'order' => 1,
            ],
            [
                'key' => 'youtube_channel_id',
                'value' => '',
                'type' => 'text',
                'group' => 'feedback_sites',
                'label' => 'YouTube Channel ID',
                'description' => 'Your YouTube channel ID (found in YouTube Studio > Customization > Basic Info)',
                'order' => 2,
            ],
            [
                'key' => 'youtube_import_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'feedback_sites',
                'label' => 'Enable YouTube Import',
                'description' => 'Enable automatic daily import of YouTube comments as feedback',
                'order' => 3,
            ],
            [
                'key' => 'youtube_auto_approve',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'feedback_sites',
                'label' => 'Auto-Approve YouTube Comments',
                'description' => 'Automatically approve imported YouTube comments (if disabled, comments remain pending for manual review)',
                'order' => 4,
            ],
            [
                'key' => 'youtube_default_rating',
                'value' => '5',
                'type' => 'number',
                'group' => 'feedback_sites',
                'label' => 'Default Rating for YouTube Comments',
                'description' => 'Star rating to assign to imported YouTube comments (1-5)',
                'order' => 5,
            ],
            [
                'key' => 'youtube_max_results',
                'value' => '100',
                'type' => 'number',
                'group' => 'feedback_sites',
                'label' => 'Max Comments Per Import',
                'description' => 'Maximum number of comments to import per video',
                'order' => 6,
            ],
            [
                'key' => 'youtube_sentiment_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'feedback_sites',
                'label' => 'Enable Sentiment Filtering',
                'description' => 'Filter comments based on sentiment analysis',
                'order' => 7,
            ],
            [
                'key' => 'youtube_sentiment_method',
                'value' => 'keyword',
                'type' => 'select',
                'group' => 'feedback_sites',
                'label' => 'Sentiment Analysis Method',
                'description' => 'Choose between keyword-based or ML-based analysis',
                'options' => json_encode(['keyword' => 'Keyword-Based (Fast)', 'ml' => 'ML-Based (Accurate)']),
                'order' => 8,
            ],
            [
                'key' => 'youtube_sentiment_threshold',
                'value' => '0.6',
                'type' => 'number',
                'group' => 'feedback_sites',
                'label' => 'Positive Sentiment Threshold',
                'description' => 'Minimum score (0-1) to consider comment as positive',
                'order' => 9,
            ],
            [
                'key' => 'youtube_import_positive_only',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'feedback_sites',
                'label' => 'Import Positive Comments Only',
                'description' => 'Only import comments with positive sentiment',
                'order' => 10,
            ],
            [
                'key' => 'sentiment_custom_positive_bangla',
                'value' => '',
                'type' => 'textarea',
                'group' => 'feedback_sites',
                'label' => 'Custom Positive Keywords (Bangla)',
                'description' => 'Comma-separated list of custom Bangla positive keywords',
                'order' => 11,
            ],
            [
                'key' => 'sentiment_custom_positive_english',
                'value' => '',
                'type' => 'textarea',
                'group' => 'feedback_sites',
                'label' => 'Custom Positive Keywords (English)',
                'description' => 'Comma-separated list of custom English positive keywords',
                'order' => 12,
            ],
            [
                'key' => 'sentiment_custom_negative_bangla',
                'value' => '',
                'type' => 'textarea',
                'group' => 'feedback_sites',
                'label' => 'Custom Negative Keywords (Bangla)',
                'description' => 'Comma-separated list of custom Bangla negative keywords',
                'order' => 13,
            ],
            [
                'key' => 'sentiment_custom_negative_english',
                'value' => '',
                'type' => 'textarea',
                'group' => 'feedback_sites',
                'label' => 'Custom Negative Keywords (English)',
                'description' => 'Comma-separated list of custom English negative keywords',
                'order' => 14,
            ],
            [
                'key' => 'youtube_last_import',
                'value' => '',
                'type' => 'readonly',
                'group' => 'feedback_sites',
                'label' => 'Last Import',
                'description' => 'Timestamp of the last successful YouTube comment import',
                'order' => 7,
            ],
            [
                'key' => 'youtube_last_import_count',
                'value' => '0',
                'type' => 'readonly',
                'group' => 'feedback_sites',
                'label' => 'Last Import Count',
                'description' => 'Number of comments imported in the last run',
                'order' => 8,
            ],
        ];

        foreach ($youtubeFeedbackSettings as $setting) {
            $this->upsertSetting($setting);
        }
    }

    /**
     * Smart upsert: Only create or update if metadata differs (excludes value, timestamps)
     */
    private function upsertSetting(array $settingData): void
    {
        $existing = SiteSetting::where('key', $settingData['key'])->first();

        if (!$existing) {
            // Setting doesn't exist, create it
            SiteSetting::create($settingData);
            $this->command->info("Created setting: {$settingData['key']}");
        } else {
            // Setting exists, check if metadata differs (exclude value and timestamps)
            $excludeFields = ['key', 'value', 'created_at', 'updated_at'];
            $needsUpdate = false;
            $updates = [];

            foreach ($settingData as $field => $newValue) {
                if (in_array($field, $excludeFields)) continue;

                $oldValue = $existing->{$field};

                // Compare metadata fields only
                if ($oldValue != $newValue) {
                    $needsUpdate = true;
                    $updates[$field] = $newValue;
                }
            }

            if ($needsUpdate) {
                $existing->update($updates);
                $this->command->info("Updated setting metadata: {$settingData['key']}");
            }
        }
    }
}
