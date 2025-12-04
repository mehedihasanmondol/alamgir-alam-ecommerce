<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Homepage Types
    |--------------------------------------------------------------------------
    |
    | Define available homepage types. Each type should have:
    | - key: Unique identifier
    | - label: Display name in admin panel
    | - description: Help text for admins
    | - view: View file to render (optional, can be handled in controller)
    | - requires: Additional data required (e.g., author_id)
    |
    */

    'types' => [
        'default' => [
            'key' => 'default',
            'label' => 'Default Homepage',
            'description' => 'Display the standard homepage with featured products, categories, and promotional content.',
            'icon' => 'home',
            'requires' => [],
        ],
        
        'author_profile' => [
            'key' => 'author_profile',
            'label' => 'Author Profile',
            'description' => 'Display a specific author\'s profile page as the homepage, including their bio and articles.',
            'icon' => 'user',
            'requires' => ['author_id'],
        ],
        
        // Future extensible types:
        // 'category_page' => [
        //     'key' => 'category_page',
        //     'label' => 'Category Page',
        //     'description' => 'Display a specific product category as the homepage.',
        //     'icon' => 'folder',
        //     'requires' => ['category_id'],
        // ],
        
        // 'custom_page' => [
        //     'key' => 'custom_page',
        //     'label' => 'Custom Page',
        //     'description' => 'Display a custom static page as the homepage.',
        //     'icon' => 'file',
        //     'requires' => ['page_id'],
        // ],
        
        // 'blog_index' => [
        //     'key' => 'blog_index',
        //     'label' => 'Blog Index',
        //     'description' => 'Display the blog listing page as the homepage.',
        //     'icon' => 'newspaper',
        //     'requires' => [],
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Homepage Type
    |--------------------------------------------------------------------------
    |
    | This value will be used if no homepage setting is found in the database.
    |
    */

    'default_type' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configure caching for homepage settings to improve performance.
    |
    */

    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour
        'key' => 'homepage_settings',
    ],
];
