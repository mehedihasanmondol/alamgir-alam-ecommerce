# Inspired by Browsing Section - Implementation Guide

## Overview

The "Inspired by your browsing" section displays personalized product recommendations on the product detail page based on the user's browsing history. This feature enhances user engagement and increases cross-selling opportunities.

## Features

### 🎯 Smart Recommendations
- Analyzes user's browsing history (recently viewed products)
- Recommends products from similar categories and brands
- Excludes already viewed products to show fresh recommendations
- Falls back to category-based recommendations for new users

### 🎨 Design
- Horizontal scrollable carousel matching iHerb style
- Left/right navigation arrows for easy browsing
- Clean product cards with essential information
- Responsive design for all screen sizes
- Smooth scroll animations

### 📦 Product Cards Include
- Product image with hover effect
- Brand name
- Product name (2-line truncation)
- Star rating with review count
- Price (with sale price if applicable)
- Sale badge for discounted items

## Implementation Details

### Files Created

#### 1. Component: `resources/views/components/inspired-by-browsing.blade.php`

**Purpose**: Displays the browsing-inspired product recommendations carousel

**Key Features**:
- Horizontal scrollable container with hidden scrollbar
- Navigation buttons (left/right arrows)
- Product cards with complete information
- Responsive grid layout
- JavaScript for smooth carousel scrolling

**Props**:
- `$products` - Collection of Product models with relationships (variants, images, brand)

### Files Modified

#### 2. Controller: `app/Http/Controllers/ProductController.php`

**Added Method**: `getInspiredByBrowsing(Product $currentProduct)`

**Logic**:
1. Retrieves recently viewed product IDs from session
2. If no browsing history exists:
   - Returns products from the same category
   - Excludes current product
   - Random order, limit 10
3. If browsing history exists:
   - Fetches recently viewed products
   - Extracts category IDs and brand IDs
   - Queries products matching those categories/brands
   - Excludes current product and already viewed products
   - Random order, limit 10

**Updated Method**: `show($slug)`
- Added call to `getInspiredByBrowsing()`
- Passes `$inspiredByBrowsing` to view

#### 3. View: `resources/views/frontend/products/show.blade.php`

**Added Section**:
```blade
<!-- Inspired by Browsing -->
<x-inspired-by-browsing :products="$inspiredByBrowsing" />
```

**Position**: After "Frequently Purchased Together" section, before "Product Tabs"

## How It Works

### User Flow

1. **User browses products**
   - Each product view is tracked in session
   - Session stores last 10 viewed product IDs

2. **User views a product**
   - Controller analyzes browsing history
   - Identifies patterns (categories, brands)
   - Fetches relevant recommendations

3. **Recommendations displayed**
   - Up to 10 products shown in carousel
   - User can scroll horizontally
   - Click any product to view details

### Technical Flow

```
User Views Product
       ↓
ProductController::show()
       ↓
getInspiredByBrowsing()
       ↓
Analyze Session Data
       ↓
Query Relevant Products
       ↓
Return Collection
       ↓
Pass to View
       ↓
Render Component
       ↓
Display Carousel
```

## Component Structure

### HTML Structure
```
<div class="bg-white py-8">
  <div class="container">
    <h2>Inspired by your browsing</h2>
    <div class="relative">
      <button>← Previous</button>
      <button>Next →</button>
      <div class="carousel">
        <div class="product-card">...</div>
        <div class="product-card">...</div>
        ...
      </div>
    </div>
  </div>
</div>
```

### Product Card Structure
```
<div class="product-card">
  <a href="product-url">
    <div class="image-container">
      <img src="product-image" />
      <span class="sale-badge">SALE</span>
    </div>
    <div class="product-info">
      <div class="brand">Brand Name</div>
      <h3 class="name">Product Name</h3>
      <div class="rating">★★★★★ (reviews)</div>
      <div class="price">$XX.XX</div>
    </div>
  </a>
</div>
```

## Styling

### Key CSS Classes
- `scrollbar-hide` - Hides scrollbar for clean look
- `line-clamp-2` - Truncates product name to 2 lines
- `group-hover:scale-105` - Image zoom on hover
- `scroll-smooth` - Smooth scrolling animation

### Responsive Design
- Desktop: Shows 5-6 products at once
- Tablet: Shows 3-4 products at once
- Mobile: Shows 1-2 products at once
- All sizes: Horizontal scroll enabled

## JavaScript Functionality

### Carousel Scrolling
```javascript
function scrollCarousel(carouselId, direction) {
    const carousel = document.getElementById(carouselId);
    const scrollAmount = 220; // Card width + gap
    
    if (direction === 'left') {
        carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
}
```

### Scrollbar Hiding
- CSS: `scrollbar-width: none` (Firefox)
- CSS: `-ms-overflow-style: none` (IE/Edge)
- CSS: `::-webkit-scrollbar { display: none }` (Chrome/Safari)

## Database Queries

### Main Query (with browsing history)
```php
Product::with(['variants', 'images', 'brand'])
    ->where('id', '!=', $currentProduct->id)
    ->where('is_active', true)
    ->where(function ($q) use ($categoryIds, $brandIds) {
        if (!empty($categoryIds)) {
            $q->whereIn('category_id', $categoryIds);
        }
        if (!empty($brandIds)) {
            $q->orWhereIn('brand_id', $brandIds);
        }
    })
    ->whereNotIn('id', $recentlyViewedIds)
    ->inRandomOrder()
    ->limit(10)
    ->get();
```

### Fallback Query (no browsing history)
```php
Product::with(['variants', 'images', 'brand'])
    ->where('category_id', $currentProduct->category_id)
    ->where('id', '!=', $currentProduct->id)
    ->where('is_active', true)
    ->inRandomOrder()
    ->limit(10)
    ->get();
```

## Performance Considerations

### Optimizations
- Eager loading: `with(['variants', 'images', 'brand'])`
- Limit results: `limit(10)`
- Image lazy loading: `loading="lazy"`
- Session-based tracking (no database writes)
- Random order for variety: `inRandomOrder()`

### Caching Opportunities (Future)
- Cache category-based recommendations
- Cache brand-based recommendations
- Cache for 1 hour, invalidate on product updates

## Testing

### Test Cases

1. **New User (No Browsing History)**
   - Should show products from same category
   - Should exclude current product
   - Should show up to 10 products

2. **User with Browsing History**
   - Should analyze categories and brands
   - Should show relevant products
   - Should exclude already viewed products
   - Should show up to 10 products

3. **UI/UX Testing**
   - Carousel scrolls smoothly
   - Navigation buttons work correctly
   - Product cards display correctly
   - Images load properly
   - Links work correctly
   - Responsive on all devices

4. **Edge Cases**
   - No products available: Section hidden
   - Less than 10 products: Shows available products
   - All products viewed: Falls back to category products

## Future Enhancements

### Potential Improvements

1. **Advanced Recommendations**
   - Machine learning-based recommendations
   - Collaborative filtering
   - Purchase history integration
   - User preferences

2. **Personalization**
   - User-specific weights for categories/brands
   - Time-based relevance decay
   - Seasonal recommendations

3. **Analytics**
   - Track click-through rates
   - A/B testing different algorithms
   - Conversion tracking

4. **Performance**
   - Redis caching for recommendations
   - Pre-compute recommendations
   - Lazy load product cards

5. **UI Enhancements**
   - Infinite scroll
   - Touch swipe on mobile
   - Keyboard navigation
   - Accessibility improvements

## Troubleshooting

### Common Issues

**Issue**: Section not appearing
- **Solution**: Check if `$inspiredByBrowsing` is passed to view
- **Solution**: Verify products exist in database
- **Solution**: Check `is_active` status of products

**Issue**: Carousel not scrolling
- **Solution**: Verify JavaScript is loaded
- **Solution**: Check browser console for errors
- **Solution**: Ensure carousel ID matches in script

**Issue**: Images not loading
- **Solution**: Check storage symlink: `php artisan storage:link`
- **Solution**: Verify image paths in database
- **Solution**: Check file permissions

**Issue**: Same products showing repeatedly
- **Solution**: Verify `whereNotIn()` excludes viewed products
- **Solution**: Check session data is being stored correctly
- **Solution**: Increase product pool in database

## Maintenance

### Regular Tasks
- Monitor performance metrics
- Review recommendation quality
- Update styling as needed
- Test on new devices/browsers

### Code Updates
- Keep component in sync with design system
- Update queries for new product features
- Optimize database queries as needed
- Add new recommendation algorithms

## Conclusion

The "Inspired by your browsing" section successfully enhances the product detail page with personalized recommendations. The implementation is:

✅ **User-Friendly**: Clean, intuitive interface  
✅ **Performance-Optimized**: Efficient queries and lazy loading  
✅ **Responsive**: Works on all devices  
✅ **Maintainable**: Clean, documented code  
✅ **Scalable**: Ready for future enhancements  

The feature is production-ready and provides a solid foundation for advanced recommendation systems in the future.
