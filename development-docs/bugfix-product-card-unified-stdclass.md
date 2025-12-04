# Bug Fix: Product Card Unified - stdClass Error

## Date: November 18, 2025
## Status: ✅ Fixed

---

## Error

```
Call to undefined method stdClass::canAddToCart()
File: resources\views\components\product-card-unified.blade.php:19
```

---

## Problem

The `product-card-unified` component was calling `canAddToCart()` method on variant objects, but in some cases (like the cart page), variants were created as `stdClass` objects instead of `ProductVariant` model instances.

### Root Cause:

In `resources/views/frontend/cart/index.blade.php`, mock variants were created using:
```php
$variant = (object) [
    'id' => $product['variant_id'],
    'price' => $product['original_price'] ?? $product['price'],
    'sale_price' => $product['price'],
    'stock_quantity' => 10
];
```

This creates a `stdClass` object, not a `ProductVariant` model, so it doesn't have methods like:
- `canAddToCart()`
- `shouldShowStock()`
- `getStockDisplayText()`

---

## Solution

Updated `resources/views/components/product-card-unified.blade.php` to:

1. **Check variant type** before using model methods
2. **Convert stdClass to ProductVariant model** if needed
3. **Use method_exists()** as safety check
4. **Graceful fallback** if methods don't exist

### Code Changes:

**Before:**
```php
$variant = $product->variants->first();
$canAddToCart = $variant ? $variant->canAddToCart() : false;
$showStockInfo = $variant ? $variant->shouldShowStock() : false;
```

**After:**
```php
// Ensure we get a proper ProductVariant model instance
$variant = null;
if ($product->variants && $product->variants->count() > 0) {
    $firstVariant = $product->variants->first();
    // If it's a stdClass, get the proper model by ID
    if ($firstVariant instanceof stdClass && isset($firstVariant->id)) {
        $variant = \App\Modules\Ecommerce\Product\Models\ProductVariant::find($firstVariant->id);
    } elseif ($firstVariant instanceof \App\Modules\Ecommerce\Product\Models\ProductVariant) {
        $variant = $firstVariant;
    }
}

// Use model methods only if we have a proper model
$canAddToCart = ($variant && method_exists($variant, 'canAddToCart')) ? $variant->canAddToCart() : false;
$showStockInfo = ($variant && method_exists($variant, 'shouldShowStock')) ? $variant->shouldShowStock() : false;
$stockText = ($variant && method_exists($variant, 'getStockDisplayText')) ? $variant->getStockDisplayText() : null;
```

---

## How It Works

### 1. **Type Detection**
```php
if ($firstVariant instanceof stdClass && isset($firstVariant->id)) {
    // It's a plain object with an ID - fetch the real model
    $variant = \App\Modules\Ecommerce\Product\Models\ProductVariant::find($firstVariant->id);
}
```

### 2. **Model Verification**
```php
elseif ($firstVariant instanceof \App\Modules\Ecommerce\Product\Models\ProductVariant) {
    // Already a proper model - use it directly
    $variant = $firstVariant;
}
```

### 3. **Safe Method Calling**
```php
$canAddToCart = ($variant && method_exists($variant, 'canAddToCart')) 
    ? $variant->canAddToCart() 
    : false;
```

---

## Impact

### Pages Affected & Fixed:
- ✅ Cart page (`/cart`)
- ✅ Related products section
- ✅ Inspired by browsing section
- ✅ Best sellers slider
- ✅ Recommended slider
- ✅ Sale offers slider
- ✅ New arrivals slider
- ✅ Trending products slider

### Benefits:
- ✅ No more `stdClass` errors
- ✅ Graceful handling of both object types
- ✅ Stock restriction logic works correctly
- ✅ Cart functionality restored

---

## Testing

### Test Scenarios:
1. ✅ Visit `/cart` page - No errors
2. ✅ View product recommendations - Works
3. ✅ Check homepage sliders - Display correctly
4. ✅ Add to cart from recommendations - Functions properly

---

## Files Modified

1. ✅ `resources/views/components/product-card-unified.blade.php`
   - Added type checking for variants
   - Added model conversion for stdClass
   - Added method_exists() checks
   - Improved error handling

---

## Prevention

To prevent similar issues in the future:

### Best Practice:
When passing products to `product-card-unified`, ensure variants are proper model instances:

**Good:**
```php
$product = Product::with(['variants', 'images'])->find($id);
<x-product-card-unified :product="$product" />
```

**Avoid:**
```php
$variant = (object) ['id' => 1, 'price' => 29.99];
$product->variants = collect([$variant]);
```

### Alternative Solution:
If you need to create mock objects, use proper models:
```php
use App\Modules\Ecommerce\Product\Models\ProductVariant;

$variant = new ProductVariant([
    'id' => $product['variant_id'],
    'price' => $product['price'],
    'sale_price' => $product['sale_price'],
]);
```

---

## Related

This fix complements:
- Stock restriction logic implementation
- Cart sidebar fixes
- Currency system updates

---

## Summary

**Problem**: Component tried to call model methods on stdClass objects  
**Solution**: Added type checking and model conversion  
**Result**: ✅ All product cards work with both model and plain objects  
**Status**: ✅ **FIXED & TESTED**

---

**Fixed By**: Windsurf AI  
**Date**: November 18, 2025  
**Severity**: Medium (500 error on cart page)  
**Resolution Time**: Immediate
