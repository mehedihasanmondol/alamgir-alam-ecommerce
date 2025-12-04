# Mega Menu Dynamic Trending Brands Feature

**Created:** 2025-11-19  
**Status:** ✅ Completed

## Overview

Implemented a comprehensive system for displaying trending brands dynamically in the mega menu based on actual product sales data. Each category now shows its own top-performing brands calculated from order history, with intelligent fallbacks and full admin control.

---

## Key Features

### 1. **Category-Specific Trending Brands**
- Each category in the mega menu displays brands based on sales within that category
- Automatically includes sales from all descendant categories (children and grandchildren)
- Shows top 6 brands by default (configurable via settings)

### 2. **Global Trending Brands**
- "Brands A-Z" menu shows overall trending brands across all categories
- Calculated from all product sales regardless of category

### 3. **Sales-Based Calculation**
- Trending brands are determined by analyzing order items
- Only counts successful orders (excludes cancelled/failed orders)
- Uses configurable time window (default: last 30 days)
- Ranks brands by total quantity sold

### 4. **Intelligent Fallbacks**
- If no sales data available, automatically falls back to featured brands
- Ensures trending brands section always shows relevant content
- Uses brand sort_order for fallback ranking

### 5. **Full Admin Control**
- Enable/disable entire trending brands feature
- Toggle between dynamic (sales-based) and static (featured brands)
- Configure number of brands to display
- Set time window for sales calculation

---

## Settings Added

Added to `database/seeders/HomepageSettingSeeder.php`:

| Setting Key | Type | Default | Description |
|------------|------|---------|-------------|
| `mega_menu_trending_brands_enabled` | boolean | `1` | Show/hide trending brands in mega menu |
| `mega_menu_trending_brands_dynamic` | boolean | `1` | Use dynamic (sales-based) vs static (featured brands) |
| `mega_menu_trending_brands_limit` | text | `6` | Number of brands to display per category |
| `mega_menu_trending_brands_days` | text | `30` | Calculate sales from last X days |

### Accessing Settings in Admin

Settings appear in: **Admin Panel → Settings → Homepage Settings → Mega Menu**

---

## Technical Implementation

### Service Layer: `MegaMenuService`

**File:** `app/Services/MegaMenuService.php`

#### Key Methods:

1. **`getTrendingBrandsByCategory(int $categoryId, ?int $limit, ?int $days)`**
   - Calculates trending brands for specific category
   - Includes all descendant categories in calculation
   - Returns Collection of Brand models with sales data
   - Cached for 1 hour per category

2. **`getGlobalTrendingBrands(?int $limit, ?int $days)`**
   - Calculates trending brands across all categories
   - Used for "Brands A-Z" section
   - Cached for 1 hour

3. **`getCategoryWithDescendants(int $categoryId)`**
   - Recursively gets all child and grandchild categories
   - Ensures sales from nested categories are included

4. **`getFallbackTrendingBrands(?int $limit)`**
   - Returns featured brands when no sales data available
   - Sorted by brand sort_order

5. **`clearTrendingBrandsCache()`**
   - Clears all trending brands cache
   - Should be called after orders are created/updated

#### Query Details:

```php
// Sales-based calculation query
Brand::select('brands.*', DB::raw('SUM(order_items.quantity) as total_sales'))
    ->join('products', 'products.brand_id', '=', 'brands.id')
    ->join('order_items', 'order_items.product_id', '=', 'products.id')
    ->join('orders', 'orders.id', '=', 'order_items.order_id')
    ->whereIn('products.category_id', $categoryIds)
    ->where('brands.is_active', true)
    ->where('orders.status', '!=', 'cancelled')
    ->where('orders.status', '!=', 'failed')
    ->where('orders.created_at', '>=', now()->subDays($days))
    ->groupBy('brands.id')
    ->orderByDesc('total_sales')
    ->limit($limit)
    ->get();
```

---

### View Composer: `CategoryComposer`

**File:** `app/Http/View/Composers/CategoryComposer.php`

#### Updates:

1. **Dependency Injection**
   - Injected `MegaMenuService` via constructor
   
2. **New Method: `getCategoryTrendingBrands($categories)`**
   - Loops through all mega menu categories
   - Calls service to get trending brands for each
   - Returns array keyed by category ID

3. **Data Provided to Views:**
   - `$megaMenuCategories` - Category tree structure
   - `$categoryTrendingBrands` - Array of trending brands per category
   - `$globalTrendingBrands` - Overall trending brands

---

### Frontend Components

#### 1. **Mega Menu Component**

**File:** `resources/views/components/frontend/mega-menu.blade.php`

**Props Updated:**
```php
@props([
    'megaMenuCategories' => collect(), 
    'categoryTrendingBrands' => [], 
    'globalTrendingBrands' => collect()
])
```

**Category Menu Changes:**
- Uses `$categoryTrendingBrands[$category->id]` for each category dropdown
- Shows different trending brands for each category
- Falls back to empty collection if no brands available

**Brands A-Z Menu Changes:**
- Uses `$globalTrendingBrands` instead of category-specific brands
- Shows overall top-selling brands across all categories

#### 2. **Header Component**

**File:** `resources/views/components/frontend/header.blade.php`

**Updated Props Passed to Mega Menu:**
```blade
<x-frontend.mega-menu 
    :megaMenuCategories="$megaMenuCategories ?? collect()" 
    :categoryTrendingBrands="$categoryTrendingBrands ?? []" 
    :globalTrendingBrands="$globalTrendingBrands ?? collect()" 
/>
```

---

## Performance Optimization

### Caching Strategy

1. **Category Trending Brands**
   - Cache key: `trending_brands_category_{$categoryId}_{$limit}_{$days}`
   - TTL: 3600 seconds (1 hour)
   - Individual cache per category configuration

2. **Global Trending Brands**
   - Cache key: `trending_brands_global_{$limit}_{$days}`
   - TTL: 3600 seconds (1 hour)

3. **Mega Menu Categories**
   - Cache key: `mega_menu_categories`
   - TTL: 3600 seconds (1 hour)
   - Already existed, unchanged

### Cache Clearing

Cache should be cleared when:
- New orders are created
- Order status changes
- Product categories are modified
- Brand activation status changes

**Manual Clear Method:**
```php
app(\App\Services\MegaMenuService::class)->clearTrendingBrandsCache();
```

---

## Database Tables Used

### Primary Tables:
- `brands` - Brand information and logos
- `products` - Product-brand-category relationships
- `orders` - Order status and timestamps
- `order_items` - Product quantities sold
- `categories` - Category hierarchy

### Key Relationships:
```
orders (1) → (N) order_items
order_items (N) → (1) products
products (N) → (1) brands
products (N) → (1) categories
categories (1) → (N) categories (parent-child)
```

---

## Admin Usage Guide

### Enable/Disable Feature

1. Go to **Admin Panel → Settings → Homepage Settings**
2. Find **Mega Menu** section
3. Toggle settings:
   - **Trending Brands Enabled**: Master on/off switch
   - **Dynamic Trending Brands**: Use sales data vs featured brands
   - **Brands Limit**: How many brands to show (1-10)
   - **Days for Calculation**: Time window for sales (7-90 days)

### Best Practices

#### Recommended Settings:

- **New Store (< 3 months old):**
  - Enable: `Yes`
  - Dynamic: `No` (use featured brands)
  - Limit: `6`
  - Days: `30`

- **Established Store (> 3 months old):**
  - Enable: `Yes`
  - Dynamic: `Yes` (use sales data)
  - Limit: `6`
  - Days: `30-60`

- **High Traffic Store:**
  - Enable: `Yes`
  - Dynamic: `Yes`
  - Limit: `6-8`
  - Days: `14-30` (shorter window for faster trends)

---

## Files Modified/Created

### Created Files:
1. `app/Services/MegaMenuService.php` - Core service logic

### Modified Files:
1. `database/seeders/HomepageSettingSeeder.php` - Added settings
2. `app/Http/View/Composers/CategoryComposer.php` - Updated data provider
3. `resources/views/components/frontend/mega-menu.blade.php` - Updated component
4. `resources/views/components/frontend/header.blade.php` - Updated props

---

## Testing Checklist

### Frontend Tests:

- [ ] Mega menu displays correctly for each category
- [ ] Each category shows different trending brands
- [ ] "Brands A-Z" shows global trending brands
- [ ] Brand logos display correctly
- [ ] Brands without logos show name text
- [ ] Links to brand pages work correctly
- [ ] Hover effects work on brand cards

### Admin Tests:

- [ ] Settings appear in Homepage Settings admin
- [ ] Toggle "Trending Brands Enabled" hides/shows brands
- [ ] Toggle "Dynamic Trending Brands" switches between sales/featured
- [ ] Changing "Brands Limit" adjusts number shown
- [ ] Changing "Days" affects calculation window
- [ ] Settings save successfully

### Performance Tests:

- [ ] Page loads within acceptable time (< 500ms)
- [ ] Cache is working (check query count)
- [ ] No N+1 query issues
- [ ] Memory usage is acceptable

### Edge Case Tests:

- [ ] Category with no sales shows featured brands
- [ ] Category with < 6 brands shows available brands
- [ ] New store with no orders shows featured brands
- [ ] Brands with no logo display correctly

---

## Migration Required

### Database Migration:
**None required** - Uses existing tables and relationships

### Data Seeding:
Run the following command to add new settings:
```bash
php artisan db:seed --class=HomepageSettingSeeder
```

Or run full seeder:
```bash
php artisan db:seed
```

---

## Future Enhancements

### Potential Improvements:

1. **Admin Dashboard Widget**
   - Show trending brands chart
   - Display sales trends over time

2. **Brand Performance Metrics**
   - Add "Hot" or "Trending Up" badges
   - Show percentage growth

3. **A/B Testing**
   - Test sales-based vs featured brands
   - Measure click-through rates

4. **Personalization**
   - Show trending brands based on user's browsing history
   - Category preference learning

5. **Real-time Updates**
   - WebSocket integration for live trending updates
   - Refresh cache on order completion

6. **Advanced Analytics**
   - Track which trending brands generate most clicks
   - Measure conversion rates per brand

---

## Bug Fixes

### Fixed: MySQL GROUP BY Strict Mode Error (2025-11-19)

**Error**: `SQLSTATE[42000]: Syntax error or access violation: 1055 'brands.name' isn't in GROUP BY`

**Root Cause**: When using `SELECT brands.*` with `GROUP BY brands.id`, MySQL strict mode requires all non-aggregated columns to be in the GROUP BY clause.

**Solution**: Refactored query to use subquery approach:
1. First query gets brand IDs with sales totals (only aggregates `brand_id`)
2. Second query fetches full brand records using the IDs
3. Sort results to maintain sales order

**Files Modified**:
- `app/Services/MegaMenuService.php` - Both `getTrendingBrandsByCategory()` and `getGlobalTrendingBrands()` methods

**Benefits**:
- ✅ Works with MySQL strict mode
- ✅ Cleaner separation of concerns
- ✅ Better performance (only aggregates necessary columns)
- ✅ Maintains sort order correctly

---

## Troubleshooting

### Issue: No Brands Showing

**Check:**
1. Is `mega_menu_trending_brands_enabled` set to `1`?
2. Are there any active brands in database?
3. Are brands marked as `is_active = 1`?
4. If using featured brands, are any marked `is_featured = 1`?

**Solution:**
```bash
# Check settings
php artisan tinker
\App\Models\HomepageSetting::where('key', 'like', 'mega_menu%')->get();

# Check brands
\App\Modules\Ecommerce\Brand\Models\Brand::where('is_active', 1)->count();
```

### Issue: Same Brands for All Categories

**Check:**
1. Is `mega_menu_trending_brands_dynamic` set to `1`?
2. Are there orders in the last X days?
3. Is cache stuck?

**Solution:**
```bash
# Clear cache
php artisan cache:clear

# Or programmatically
app(\App\Services\MegaMenuService::class)->clearTrendingBrandsCache();
```

### Issue: Slow Page Load

**Check:**
1. Is caching working?
2. Are there too many database queries?

**Solution:**
```bash
# Check queries with Laravel Debugbar
# Ensure cache is enabled in config/cache.php
# Consider increasing cache TTL to 2 hours

# Or reduce days calculation
UPDATE homepage_settings 
SET value = '14' 
WHERE key = 'mega_menu_trending_brands_days';
```

---

## API Documentation

### MegaMenuService Methods

#### `getTrendingBrandsByCategory(int $categoryId, ?int $limit = null, ?int $days = null)`

**Parameters:**
- `$categoryId` (int) - Category ID to get trending brands for
- `$limit` (int|null) - Number of brands to return (default: from settings)
- `$days` (int|null) - Days to look back for sales (default: from settings)

**Returns:** `Collection` - Collection of Brand models

**Example:**
```php
$service = app(\App\Services\MegaMenuService::class);
$brands = $service->getTrendingBrandsByCategory(5, 10, 60);
```

#### `getGlobalTrendingBrands(?int $limit = null, ?int $days = null)`

**Parameters:**
- `$limit` (int|null) - Number of brands to return
- `$days` (int|null) - Days to look back for sales

**Returns:** `Collection` - Collection of Brand models

**Example:**
```php
$service = app(\App\Services\MegaMenuService::class);
$brands = $service->getGlobalTrendingBrands(8, 30);
```

#### `clearTrendingBrandsCache()`

**Parameters:** None

**Returns:** `void`

**Example:**
```php
$service = app(\App\Services\MegaMenuService::class);
$service->clearTrendingBrandsCache();
```

---

## Conclusion

The dynamic trending brands feature provides a data-driven approach to showcase the most popular brands in each category. This helps customers discover trending products and brands while giving store owners valuable insights into brand performance.

**Benefits:**
- ✅ Increases brand visibility based on actual sales
- ✅ Improves customer discovery
- ✅ Provides category-specific relevance
- ✅ Fully configurable via admin settings
- ✅ Intelligent fallbacks ensure always-on display
- ✅ Performance optimized with caching
- ✅ No manual brand selection needed

**Next Steps:**
1. Run database seeder to add settings
2. Configure settings in admin panel
3. Test across different categories
4. Monitor performance and adjust cache TTL if needed
5. Consider implementing cache clearing on order events
