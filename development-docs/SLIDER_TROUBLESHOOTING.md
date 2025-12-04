# Product Slider Troubleshooting Guide

## Issue: Slider Not Showing After Hero

### Quick Checks

1. **View Page Source** (Right-click → View Page Source)
   - Look for the HTML comment: `<!-- DEBUG: Featured Products: X, New Arrivals: Y, Recommended: Z -->`
   - This will tell you if products are being loaded

2. **Check Browser Console** (F12 → Console tab)
   - Look for JavaScript errors
   - Look for Alpine.js errors

3. **Check Network Tab** (F12 → Network tab)
   - Refresh page
   - Look for failed requests (red items)

---

## Possible Causes & Solutions

### 1. No Products in Database

**Symptom**: Debug comment shows `0` products

**Solution**: Create some products first
```bash
# Check if you have products
php artisan tinker
>>> App\Modules\Ecommerce\Product\Models\Product::count();
```

If 0, you need to create products via admin panel:
- Go to `/admin/products`
- Create at least 3-5 products
- Mark some as "Featured"

### 2. Products Not Featured

**Symptom**: Featured Products: 0, but New Arrivals > 0

**Solution**: Mark products as featured
- Go to `/admin/products`
- Edit products
- Check "Featured" checkbox
- Save

### 3. Products Not Active

**Symptom**: Products exist but don't show

**Solution**: Activate products
- Go to `/admin/products`
- Check product status
- Change status to "Active"

### 4. Missing Images

**Symptom**: Slider shows but no images

**Solution**: 
```bash
# Create storage link
php artisan storage:link

# Upload images via admin panel
# Go to /admin/products/{id}/images
```

### 5. Alpine.js Not Loaded

**Symptom**: Arrows don't work, no scrolling

**Solution**: Check if Alpine.js is loaded
```html
<!-- In browser console -->
window.Alpine
// Should return an object, not undefined
```

If undefined:
```bash
# Rebuild assets
npm run build
```

### 6. View Cache Issue

**Symptom**: Changes not reflecting

**Solution**:
```bash
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

Then hard refresh browser: `Ctrl + Shift + R`

### 7. Component Not Found

**Symptom**: Error about component not found

**Solution**: Check file exists
```
resources/views/components/frontend/recommended-slider.blade.php
```

If missing, the file was not created properly.

---

## Debug Steps

### Step 1: Check Page Source
```
1. Open homepage
2. Right-click → View Page Source
3. Search for "DEBUG:"
4. Note the numbers
```

### Step 2: Check Products
```bash
php artisan tinker

# Count total products
>>> App\Modules\Ecommerce\Product\Models\Product::count();

# Count active products
>>> App\Modules\Ecommerce\Product\Models\Product::where('status', 'active')->count();

# Count featured products
>>> App\Modules\Ecommerce\Product\Models\Product::where('is_featured', true)->count();

# Get first product with details
>>> App\Modules\Ecommerce\Product\Models\Product::with(['variants', 'images'])->first();
```

### Step 3: Check Component
```
1. Open: resources/views/components/frontend/recommended-slider.blade.php
2. Verify file exists and has content
3. Check for syntax errors
```

### Step 4: Check Homepage View
```
1. Open: resources/views/frontend/home/index.blade.php
2. Look for: <x-frontend.recommended-slider :products="$recommendedProducts" />
3. Verify it's after <x-frontend.hero-slider />
```

---

## Quick Fix: Force Show Slider

If you want to see the slider even with no products (for testing):

**Edit**: `resources/views/components/frontend/recommended-slider.blade.php`

Change line 21 from:
```blade
@if($hasProducts)
```

To:
```blade
@if(true) {{-- Force show for testing --}}
```

This will show the slider structure even if empty.

---

## Create Test Products

If you have no products, create some quickly:

```bash
php artisan tinker
```

```php
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductVariant;

// Create 5 test products
for ($i = 1; $i <= 5; $i++) {
    $product = Product::create([
        'name' => "Test Product $i",
        'slug' => "test-product-$i",
        'description' => "This is test product $i",
        'product_type' => 'simple',
        'status' => 'active',
        'is_featured' => true,
    ]);
    
    // Create default variant
    ProductVariant::create([
        'product_id' => $product->id,
        'sku' => "TEST-$i",
        'variant_name' => 'Default',
        'is_default' => true,
        'price' => rand(10, 100),
        'sale_price' => rand(5, 50),
        'stock_quantity' => 100,
    ]);
}

echo "Created 5 test products!\n";
```

---

## Expected HTML Output

When working, you should see this in page source:

```html
<!-- DEBUG: Featured Products: 5, New Arrivals: 8, Recommended: 5 -->

<section class="py-8 bg-white border-b border-gray-200">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Recommended for you</h2>
        </div>
        <!-- Products here -->
    </div>
</section>
```

---

## Still Not Working?

1. **Check Laravel logs**:
   ```
   storage/logs/laravel.log
   ```

2. **Check web server logs**:
   - Apache: error.log
   - Nginx: error.log

3. **Enable debug mode**:
   ```
   .env
   APP_DEBUG=true
   ```

4. **Clear everything**:
   ```bash
   php artisan cache:clear
   php artisan view:clear
   php artisan config:clear
   php artisan route:clear
   composer dump-autoload
   npm run build
   ```

---

## Contact Points

If still stuck, check:
1. Browser console for errors
2. Network tab for failed requests
3. Laravel logs for PHP errors
4. Page source for DEBUG comment

The DEBUG comment will tell you exactly where the problem is!
