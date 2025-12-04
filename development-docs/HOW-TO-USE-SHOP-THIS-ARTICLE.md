# 📝 How to Use "Shop This Article" - Quick Guide

## 🎯 **Quick Start**

Add products to your blog posts to create a seamless shopping experience!

---

## ⚡ **Method 1: Laravel Tinker (Fastest)**

### Step 1: Open Tinker
```bash
php artisan tinker
```

### Step 2: Find Your Blog Post
```php
$post = App\Modules\Blog\Models\Post::find(1);
// Or by slug
$post = App\Modules\Blog\Models\Post::where('slug', 'your-post-slug')->first();
```

### Step 3: Attach Products
```php
// Attach single product
$post->products()->attach(5);

// Attach multiple products
$post->products()->attach([5, 8, 12, 15]);

// Attach with sort order
$post->products()->attach([
    5 => ['sort_order' => 1],
    8 => ['sort_order' => 2],
    12 => ['sort_order' => 3]
]);
```

### Step 4: Verify
```php
$post->products()->count(); // Should return number of attached products
```

---

## 🗄️ **Method 2: Direct SQL**

### Attach Products
```sql
-- Add single product to post
INSERT INTO blog_post_product (blog_post_id, product_id, sort_order, created_at, updated_at)
VALUES (1, 5, 1, NOW(), NOW());

-- Add multiple products
INSERT INTO blog_post_product (blog_post_id, product_id, sort_order, created_at, updated_at)
VALUES 
  (1, 5, 1, NOW(), NOW()),
  (1, 8, 2, NOW(), NOW()),
  (1, 12, 3, NOW(), NOW());
```

### View Attached Products
```sql
SELECT p.id, p.name, bpp.sort_order
FROM blog_post_product bpp
JOIN products p ON bpp.product_id = p.id
WHERE bpp.blog_post_id = 1
ORDER BY bpp.sort_order;
```

---

## 🎨 **Method 3: PHP Code (In Controller/Service)**

### Example in a Seeder or Controller:
```php
use App\Modules\Blog\Models\Post;

// Find post
$post = Post::findOrFail(1);

// Attach products
$post->products()->sync([
    5 => ['sort_order' => 1],
    8 => ['sort_order' => 2],
    12 => ['sort_order' => 3],
    15 => ['sort_order' => 4]
]);
```

### In a Custom Command:
```php
namespace App\Console\Commands;

use App\Modules\Blog\Models\Post;
use Illuminate\Console\Command;

class AttachProductsToPost extends Command
{
    protected $signature = 'blog:attach-products {post_id} {product_ids*}';
    
    public function handle()
    {
        $postId = $this->argument('post_id');
        $productIds = $this->argument('product_ids');
        
        $post = Post::findOrFail($postId);
        $post->products()->attach($productIds);
        
        $this->info("Attached {count($productIds)} products to post #{$postId}");
    }
}
```

Usage:
```bash
php artisan blog:attach-products 1 5 8 12 15
```

---

## 🔄 **Common Operations**

### Remove Product from Post
```php
$post->products()->detach(5);
```

### Remove All Products
```php
$post->products()->detach();
```

### Update Sort Order
```php
$post->products()->updateExistingPivot(5, ['sort_order' => 10]);
```

### Replace All Products (Sync)
```php
// This will remove old products and add new ones
$post->products()->sync([1, 2, 3, 4, 5]);
```

### Check if Product is Attached
```php
$isAttached = $post->products()->where('product_id', 5)->exists();
```

### Get Product Count
```php
$count = $post->products()->count();
```

---

## 📊 **Example Scenarios**

### Scenario 1: Health Blog Post with Supplements
```php
// Post: "10 Best Vitamins for Immune Health"
$post = Post::where('slug', '10-best-vitamins-immune-health')->first();

// Attach vitamin products
$post->products()->attach([
    12 => ['sort_order' => 1],  // Vitamin C
    15 => ['sort_order' => 2],  // Zinc
    18 => ['sort_order' => 3],  // Vitamin D
    21 => ['sort_order' => 4],  // Multivitamin
]);
```

### Scenario 2: Fitness Article with Equipment
```php
// Post: "Home Workout Essentials"
$post = Post::where('slug', 'home-workout-essentials')->first();

// Attach fitness products
$post->products()->attach([
    45 => ['sort_order' => 1],  // Yoga Mat
    48 => ['sort_order' => 2],  // Dumbbells
    51 => ['sort_order' => 3],  // Resistance Bands
    54 => ['sort_order' => 4],  // Foam Roller
]);
```

### Scenario 3: Beauty Tutorial with Products
```php
// Post: "Natural Skincare Routine"
$post = Post::where('slug', 'natural-skincare-routine')->first();

// Attach beauty products in routine order
$post->products()->attach([
    67 => ['sort_order' => 1],  // Face Wash (Step 1)
    69 => ['sort_order' => 2],  // Toner (Step 2)
    72 => ['sort_order' => 3],  // Serum (Step 3)
    75 => ['sort_order' => 4],  // Moisturizer (Step 4)
    78 => ['sort_order' => 5],  // Sunscreen (Step 5)
]);
```

---

## 🎓 **Tips & Best Practices**

### ✅ DO:
- **Relevant Products** - Only attach products mentioned in the article
- **Logical Order** - Use sort_order to show products in article order
- **3-8 Products** - Sweet spot for not overwhelming readers
- **In Stock** - Feature products that are available
- **Good Images** - Ensure products have quality images

### ❌ DON'T:
- **Random Products** - Don't attach unrelated products
- **Too Many** - Avoid 20+ products (overwhelming)
- **Out of Stock** - Don't feature unavailable products
- **Duplicate** - Can't attach same product twice (unique constraint)
- **Deleted Products** - Will cause errors

---

## 🔍 **Finding Product IDs**

### Method 1: Database Query
```sql
SELECT id, name, slug FROM products WHERE status = 'published' LIMIT 20;
```

### Method 2: Tinker
```php
// Search by name
$products = App\Modules\Ecommerce\Product\Models\Product::where('name', 'LIKE', '%vitamin%')->get();

// Get all published products
$products = App\Modules\Ecommerce\Product\Models\Product::where('status', 'published')->get(['id', 'name']);

// Get products by category
$products = App\Modules\Ecommerce\Product\Models\Product::where('category_id', 5)->get(['id', 'name']);
```

### Method 3: Admin Panel
1. Go to Products page
2. Note the product ID in the list or URL
3. Use those IDs when attaching

---

## 📝 **Seeder Example**

Create a seeder to attach products to specific blog posts:

```php
<?php

namespace Database\Seeders;

use App\Modules\Blog\Models\Post;
use Illuminate\Database\Seeder;

class BlogProductSeeder extends Seeder
{
    public function run()
    {
        // Health & Wellness Post
        $healthPost = Post::where('slug', 'boost-immune-system')->first();
        if ($healthPost) {
            $healthPost->products()->sync([
                1 => ['sort_order' => 1],
                3 => ['sort_order' => 2],
                5 => ['sort_order' => 3],
                7 => ['sort_order' => 4],
            ]);
        }

        // Fitness Post
        $fitnessPost = Post::where('slug', 'home-workout-guide')->first();
        if ($fitnessPost) {
            $fitnessPost->products()->sync([
                10 => ['sort_order' => 1],
                12 => ['sort_order' => 2],
                14 => ['sort_order' => 3],
            ]);
        }

        // Beauty Post
        $beautyPost = Post::where('slug', 'skincare-routine')->first();
        if ($beautyPost) {
            $beautyPost->products()->sync([
                20 => ['sort_order' => 1],
                22 => ['sort_order' => 2],
                24 => ['sort_order' => 3],
                26 => ['sort_order' => 4],
                28 => ['sort_order' => 5],
            ]);
        }
    }
}
```

Run seeder:
```bash
php artisan db:seed --class=BlogProductSeeder
```

---

## 🎯 **Testing Your Setup**

### Step 1: Attach Products
```php
php artisan tinker
$post = App\Modules\Blog\Models\Post::find(1);
$post->products()->attach([1, 2, 3]);
```

### Step 2: Visit Blog Post
Navigate to: `yoursite.com/blog/your-post-slug`

### Step 3: Verify Features
- ✅ Shop button appears next to author info
- ✅ Button shows correct count: "Shop (3)"
- ✅ Dropdown shows 3 products
- ✅ "Shop This Article" section at bottom
- ✅ 3 product cards display
- ✅ Add to cart works

---

## 🐛 **Troubleshooting**

### Shop button doesn't appear
**Check**: Post has products attached
```php
$post->products()->count(); // Should be > 0
```

### Products show but no images
**Check**: Products have images
```php
$post->products->first()->images; // Should not be empty
```

### Can't attach product
**Possible reasons**:
- Product doesn't exist
- Already attached (unique constraint)
- Blog post doesn't exist

**Solution**:
```php
// Check if product exists
$product = App\Modules\Ecommerce\Product\Models\Product::find(5);

// Check if already attached
$post->products()->where('product_id', 5)->exists();
```

---

## 📚 **Additional Resources**

- **Full Documentation**: `SHOP-THIS-ARTICLE-FEATURE.md`
- **Database Schema**: Migration file in `database/migrations/`
- **Model Code**: `app/Modules/Blog/Models/Post.php`
- **View Code**: `resources/views/frontend/blog/show.blade.php`

---

## ✅ **Quick Reference Commands**

```php
// === In Tinker ===

// Find post
$post = App\Modules\Blog\Models\Post::find(1);

// Attach products
$post->products()->attach([1, 2, 3]);

// Attach with order
$post->products()->attach([1 => ['sort_order' => 1]]);

// Detach product
$post->products()->detach(1);

// Sync (replace all)
$post->products()->sync([1, 2, 3]);

// Count products
$post->products()->count();

// Get all products
$post->products;

// Check if attached
$post->products()->where('product_id', 1)->exists();
```

---

**Status**: ✅ **Ready to Use**

Start attaching products to your blog posts and watch your conversions grow! 🛍️📈
