<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Blog\Controllers\Admin\PostController;
use App\Modules\Blog\Controllers\Admin\BlogCategoryController;
use App\Modules\Blog\Controllers\Admin\TagController;
use App\Modules\Blog\Controllers\Admin\CommentController;
use App\Modules\Blog\Controllers\Frontend\BlogController;

/**
 * Blog Management Routes
 * 
 * Admin Routes: /admin/blog/*
 * Frontend Routes: /blog/* and /{slug}
 * 
 * Following .windsurfrules:
 * - Blog posts accessible at: domain.com/{slug} (NO /blog prefix)
 * - Category archives: domain.com/blog/category/{slug}
 * - Tag archives: domain.com/blog/tag/{slug}
 */

// ============================================
// ADMIN BLOG ROUTES
// ============================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {
    
    Route::prefix('blog')->name('blog.')->group(function () {
        
        // Posts Management (Livewire) - Requires posts.view permission
        Route::middleware(['permission:posts.view'])->group(function () {
            Route::get('posts', function() {
                return view('admin.blog.posts.index-livewire');
            })->name('posts.index');
        });
        
        // Create Posts - Requires posts.create permission (MUST BE BEFORE posts/{post})
        Route::middleware(['permission:posts.create'])->group(function () {
            Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
            Route::post('posts', [PostController::class, 'store'])->name('posts.store');
        });
        
        // Edit Posts - Requires posts.edit permission (MUST BE BEFORE posts/{post})
        Route::middleware(['permission:posts.edit'])->group(function () {
            Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
            Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
        });
        
        // Show Post - MUST BE AFTER create/edit routes
        Route::middleware(['permission:posts.view'])->group(function () {
            Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
        });
        
        // Delete Posts - Requires posts.delete permission
        Route::middleware(['permission:posts.delete'])->group(function () {
            Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
            Route::post('posts/bulk-delete', [PostController::class, 'bulkDelete'])->name('posts.bulk-delete');
        });
        
        // Publish Posts - Requires posts.publish permission
        Route::middleware(['permission:posts.publish'])->group(function () {
            Route::post('posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
        });
        
        // Toggle Featured - Requires posts.edit permission
        Route::middleware(['permission:posts.edit'])->group(function () {
            Route::post('posts/{post}/toggle-featured', [PostController::class, 'toggleFeatured'])->name('posts.toggle-featured');
        });
        
        // TinyMCE Image Upload - Requires posts.upload-image permission
        Route::middleware(['permission:posts.upload-image'])->group(function () {
            Route::post('upload-image', [PostController::class, 'uploadImage'])->name('upload-image');
        });
        
        // Tick Mark Management - Requires posts.tick-marks permission
        Route::middleware(['permission:posts.tick-marks'])->group(function () {
            Route::get('tick-marks/stats', [PostController::class, 'tickMarkStats'])->name('tick-marks.stats');
            Route::post('posts/{post}/toggle-verification', [PostController::class, 'toggleVerification'])->name('posts.toggle-verification');
            Route::post('posts/{post}/toggle-editor-choice', [PostController::class, 'toggleEditorChoice'])->name('posts.toggle-editor-choice');
            Route::post('posts/{post}/toggle-trending', [PostController::class, 'toggleTrending'])->name('posts.toggle-trending');
            Route::post('posts/{post}/toggle-premium', [PostController::class, 'togglePremium'])->name('posts.toggle-premium');
            Route::post('posts/bulk-update-tick-marks', [PostController::class, 'bulkUpdateTickMarks'])->name('posts.bulk-update-tick-marks');
        });
        
        // Categories Management - View only for authors, full access for editors
        Route::middleware(['permission:blog-categories.view'])->group(function () {
            Route::get('categories', [BlogCategoryController::class, 'index'])->name('categories.index');
        });
        
        Route::middleware(['permission:blog-categories.create'])->group(function () {
            Route::get('categories/create', [BlogCategoryController::class, 'create'])->name('categories.create');
            Route::post('categories', [BlogCategoryController::class, 'store'])->name('categories.store');
        });
        
        Route::middleware(['permission:blog-categories.edit'])->group(function () {
            Route::get('categories/{category}/edit', [BlogCategoryController::class, 'edit'])->name('categories.edit');
            Route::put('categories/{category}', [BlogCategoryController::class, 'update'])->name('categories.update');
        });
        
        Route::middleware(['permission:blog-categories.delete'])->group(function () {
            Route::delete('categories/{category}', [BlogCategoryController::class, 'destroy'])->name('categories.destroy');
        });
        
        // Tags Management - View only for authors, full access for editors
        Route::middleware(['permission:blog-tags.view'])->group(function () {
            Route::get('tags', [TagController::class, 'index'])->name('tags.index');
        });
        
        Route::middleware(['permission:blog-tags.create'])->group(function () {
            Route::get('tags/create', [TagController::class, 'create'])->name('tags.create');
            Route::post('tags', [TagController::class, 'store'])->name('tags.store');
        });
        
        Route::middleware(['permission:blog-tags.edit'])->group(function () {
            Route::get('tags/{tag}/edit', [TagController::class, 'edit'])->name('tags.edit');
            Route::put('tags/{tag}', [TagController::class, 'update'])->name('tags.update');
        });
        
        Route::middleware(['permission:blog-tags.delete'])->group(function () {
            Route::delete('tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
        });
        
        // Comments Moderation (Livewire) - Requires blog-comments.view permission
        Route::middleware(['permission:blog-comments.view'])->group(function () {
            Route::get('comments', function() {
                return view('admin.blog.comments.index-livewire');
            })->name('comments.index');
        });
    });
});

// ============================================
// FRONTEND BLOG ROUTES
// ============================================

// Blog Index (listing page)
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');

// Category Archive
Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');

// Tag Archive
Route::get('/blog/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');

// Search Results
Route::get('/blog/search', [BlogController::class, 'search'])->name('blog.search');

// Comment Submission
Route::post('/blog/{post}/comments', [BlogController::class, 'storeComment'])->name('blog.comments.store');

// Author Profile (slug-based)
Route::get('/author/{slug}', [BlogController::class, 'author'])->name('blog.author');

// ============================================
// SINGLE POST ROUTE (NO /blog PREFIX)
// ============================================
// NOTE: This route should be added to web.php at the END
// to avoid conflicts with other routes
// Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');
