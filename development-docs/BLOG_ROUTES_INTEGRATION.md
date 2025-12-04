# Blog Routes Integration Guide

## Overview
This guide explains how to integrate the blog routes into your Laravel application.

## Files Created
- `routes/blog.php` - All blog routes (admin + frontend)

## Integration Steps

### Step 1: Register Blog Routes in bootstrap/app.php

Add the blog routes to your application's route configuration:

```php
// File: bootstrap/app.php

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Register blog routes
            Route::middleware('web')
                ->group(base_path('routes/blog.php'));
        }
    )
    // ... rest of configuration
```

### Step 2: Add Single Post Route to web.php

Add this route **AT THE END** of your `routes/web.php` file to avoid conflicts:

```php
// File: routes/web.php

// ... all other routes above ...

// Single Blog Post Route (must be last to avoid conflicts)
Route::get('/{slug}', [App\Modules\Blog\Controllers\Frontend\BlogController::class, 'show'])
    ->name('blog.show')
    ->where('slug', '[a-z0-9-]+'); // Only match lowercase slugs with hyphens
```

**IMPORTANT**: This route MUST be at the end of web.php because it's a catch-all route.

### Step 3: Update Content Resolution Logic

Since products and blog posts share the same URL pattern (`domain.com/{slug}`), you need to update the route handler to check both:

**Option A: Create a ContentController (Recommended)**

```php
// File: app/Http/Controllers/ContentController.php

<?php

namespace App\Http\Controllers;

use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Blog\Models\Post;

class ContentController extends Controller
{
    public function show(string $slug)
    {
        // Try product first
        $product = Product::where('slug', $slug)->first();
        if ($product) {
            return app(\App\Modules\Ecommerce\Product\Controllers\ProductController::class)->show($slug);
        }
        
        // Then try blog post
        $post = Post::where('slug', $slug)->published()->first();
        if ($post) {
            return app(\App\Modules\Blog\Controllers\Frontend\BlogController::class)->show($slug);
        }
        
        // Neither found
        abort(404);
    }
}
```

Then update web.php:

```php
Route::get('/{slug}', [App\Http\Controllers\ContentController::class, 'show'])
    ->name('content.show');
```

**Option B: Update BlogController (Simpler)**

Update the `show` method in `BlogController`:

```php
// File: app/Modules/Blog/Controllers/Frontend/BlogController.php

public function show($slug)
{
    // Check if it's a product first
    $product = \App\Modules\Ecommerce\Product\Models\Product::where('slug', $slug)->first();
    if ($product) {
        abort(404); // Let product route handle it
    }
    
    $post = $this->postService->getPostBySlug($slug);
    // ... rest of method
}
```

## Route List

### Admin Routes (Protected by auth + role:admin)

| Method | URI | Name | Controller@Method |
|--------|-----|------|-------------------|
| GET | /admin/blog/posts | admin.blog.posts.index | PostController@index |
| GET | /admin/blog/posts/create | admin.blog.posts.create | PostController@create |
| POST | /admin/blog/posts | admin.blog.posts.store | PostController@store |
| GET | /admin/blog/posts/{post} | admin.blog.posts.show | PostController@show |
| GET | /admin/blog/posts/{post}/edit | admin.blog.posts.edit | PostController@edit |
| PUT/PATCH | /admin/blog/posts/{post} | admin.blog.posts.update | PostController@update |
| DELETE | /admin/blog/posts/{post} | admin.blog.posts.destroy | PostController@destroy |
| POST | /admin/blog/posts/{post}/publish | admin.blog.posts.publish | PostController@publish |
| GET | /admin/blog/categories | admin.blog.categories.index | BlogCategoryController@index |
| GET | /admin/blog/categories/create | admin.blog.categories.create | BlogCategoryController@create |
| POST | /admin/blog/categories | admin.blog.categories.store | BlogCategoryController@store |
| GET | /admin/blog/categories/{category}/edit | admin.blog.categories.edit | BlogCategoryController@edit |
| PUT/PATCH | /admin/blog/categories/{category} | admin.blog.categories.update | BlogCategoryController@update |
| DELETE | /admin/blog/categories/{category} | admin.blog.categories.destroy | BlogCategoryController@destroy |
| GET | /admin/blog/tags | admin.blog.tags.index | TagController@index |
| GET | /admin/blog/tags/create | admin.blog.tags.create | TagController@create |
| POST | /admin/blog/tags | admin.blog.tags.store | TagController@store |
| GET | /admin/blog/tags/{tag}/edit | admin.blog.tags.edit | TagController@edit |
| PUT/PATCH | /admin/blog/tags/{tag} | admin.blog.tags.update | TagController@update |
| DELETE | /admin/blog/tags/{tag} | admin.blog.tags.destroy | TagController@destroy |
| GET | /admin/blog/comments | admin.blog.comments.index | CommentController@index |
| POST | /admin/blog/comments/{comment}/approve | admin.blog.comments.approve | CommentController@approve |
| POST | /admin/blog/comments/{comment}/spam | admin.blog.comments.spam | CommentController@spam |
| POST | /admin/blog/comments/{comment}/trash | admin.blog.comments.trash | CommentController@trash |
| DELETE | /admin/blog/comments/{comment} | admin.blog.comments.destroy | CommentController@destroy |

### Frontend Routes (Public)

| Method | URI | Name | Controller@Method |
|--------|-----|------|-------------------|
| GET | /blog | blog.index | BlogController@index |
| GET | /blog/category/{slug} | blog.category | BlogController@category |
| GET | /blog/tag/{slug} | blog.tag | BlogController@tag |
| GET | /blog/search | blog.search | BlogController@search |
| POST | /blog/{post}/comments | blog.comments.store | BlogController@storeComment |
| GET | /{slug} | blog.show | BlogController@show |

## Testing Routes

After integration, test the routes:

```bash
# View all routes
php artisan route:list

# Filter blog routes only
php artisan route:list --name=blog

# Filter admin blog routes
php artisan route:list --name=admin.blog

# Clear route cache
php artisan route:clear
php artisan route:cache
```

## URL Examples

### Admin URLs
- Posts List: `https://yourdomain.com/admin/blog/posts`
- Create Post: `https://yourdomain.com/admin/blog/posts/create`
- Edit Post: `https://yourdomain.com/admin/blog/posts/1/edit`
- Categories: `https://yourdomain.com/admin/blog/categories`
- Tags: `https://yourdomain.com/admin/blog/tags`
- Comments: `https://yourdomain.com/admin/blog/comments`

### Frontend URLs
- Blog Index: `https://yourdomain.com/blog`
- Single Post: `https://yourdomain.com/my-blog-post-slug`
- Category: `https://yourdomain.com/blog/category/technology`
- Tag: `https://yourdomain.com/blog/tag/laravel`
- Search: `https://yourdomain.com/blog/search?q=keyword`

## Navigation Integration

### Admin Panel Navigation

Add to your admin layout navigation:

```blade
<!-- File: resources/views/layouts/admin.blade.php -->

<nav>
    <!-- Existing menu items -->
    
    <!-- Blog Menu -->
    <div class="menu-section">
        <h3 class="menu-title">Blog</h3>
        <ul>
            <li>
                <a href="{{ route('admin.blog.posts.index') }}" 
                   class="{{ request()->routeIs('admin.blog.posts.*') ? 'active' : '' }}">
                    <i class="icon-file-text"></i>
                    <span>Posts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.blog.categories.index') }}" 
                   class="{{ request()->routeIs('admin.blog.categories.*') ? 'active' : '' }}">
                    <i class="icon-folder"></i>
                    <span>Categories</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.blog.tags.index') }}" 
                   class="{{ request()->routeIs('admin.blog.tags.*') ? 'active' : '' }}">
                    <i class="icon-tag"></i>
                    <span>Tags</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.blog.comments.index') }}" 
                   class="{{ request()->routeIs('admin.blog.comments.*') ? 'active' : '' }}">
                    <i class="icon-message-square"></i>
                    <span>Comments</span>
                    @if($pendingCommentsCount > 0)
                        <span class="badge">{{ $pendingCommentsCount }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>
</nav>
```

### Frontend Header Navigation

Add to your frontend header:

```blade
<!-- File: resources/views/components/header.blade.php -->

<nav>
    <a href="{{ route('home') }}">Home</a>
    <a href="{{ route('shop') }}">Shop</a>
    <a href="{{ route('blog.index') }}">Blog</a>
    <a href="{{ route('about') }}">About</a>
    <a href="{{ route('contact') }}">Contact</a>
</nav>
```

## Middleware Configuration

Ensure these middleware are registered:

```php
// File: bootstrap/app.php or app/Http/Kernel.php

protected $middlewareAliases = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'role' => \App\Http\Middleware\CheckRole::class,
    // ... other middleware
];
```

## Permissions Setup

Ensure these permissions exist in your database:

```php
// Run this in tinker or create a seeder
php artisan tinker

$permissions = [
    'blog.view',
    'blog.create',
    'blog.edit',
    'blog.delete',
    'blog.publish',
    'comments.moderate',
];

foreach ($permissions as $permission) {
    \Spatie\Permission\Models\Permission::create(['name' => $permission]);
}

// Assign to admin role
$adminRole = \Spatie\Permission\Models\Role::findByName('admin');
$adminRole->givePermissionTo($permissions);
```

## Troubleshooting

### Route Not Found
```bash
php artisan route:clear
php artisan route:cache
php artisan optimize:clear
```

### 404 on Single Post
- Ensure the route is at the END of web.php
- Check slug format (lowercase, hyphens only)
- Verify post is published

### Admin Routes Not Working
- Check authentication middleware
- Verify role middleware is registered
- Ensure user has admin role

### Slug Conflicts
- Products take priority over blog posts
- Use unique slugs across both tables
- System auto-appends numbers if conflict detected

## Next Steps

1. ✅ Routes file created
2. ⏳ Register routes in bootstrap/app.php
3. ⏳ Add single post route to web.php
4. ⏳ Update navigation menus
5. ⏳ Test all routes
6. ⏳ Create views for each route

---

**Status**: Routes created and documented  
**Last Updated**: 2025-11-07  
**Next**: Register routes and create views
