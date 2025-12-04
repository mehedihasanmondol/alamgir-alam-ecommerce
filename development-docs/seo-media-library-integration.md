# SEO Media Library Integration

## Overview
Updated all SEO-enabled pages to prioritize media library images over legacy image fields for Open Graph and Twitter Card images.

---

## Pages Updated

### 1. Blog Post Page (`BlogController::show()`)
**Status**: ✅ Already using media library
- **Priority**: `post->media->large_url` → `post->featured_image` → `blog_image` setting → default
- **Model**: `Post` has `media()` relationship
- **Eager Loading**: `$post->load('media')`

### 2. Blog Category Page (`BlogController::category()`)
**Status**: ✅ Updated
- **Priority**: `category->media->large_url` → `category->image_path` → `blog_image` setting → `site_logo` → default
- **Model**: `BlogCategory` has `media()` relationship
- **Eager Loading**: `$category->load('media')`

### 3. Author Profile Page (`BlogController::author()`)
**Status**: ✅ Updated
- **Priority**: `authorProfile->media->large_url` → `authorProfile->avatar` → default avatar
- **Model**: `AuthorProfile` has `media()` relationship
- **Eager Loading**: `$authorProfile->load('media')`

### 4. Brand Page (`BrandController::show()`)
**Status**: ✅ Updated
- **Priority**: `brand->media->large_url` → `brand->og_image` → `brand->logo` → `site_logo` → default
- **Model**: `Brand` has `media()` relationship
- **Eager Loading**: `$brand->load('media')`

### 5. Category Page (Livewire `ProductList::getSeoDataProperty()`)
**Status**: ✅ Updated
- **Priority**: `category->media->large_url` → `category->og_image` → `category->image` → `site_logo` → default
- **Model**: `Category` has `media()` relationship
- **Eager Loading**: Added `'media'` to `with()` in mount method

### 6. Brand Page (Livewire `ProductList::getSeoDataProperty()`)
**Status**: ✅ Updated
- **Priority**: `brand->media->large_url` → `brand->og_image` → `brand->logo` → `site_logo` → default
- **Model**: `Brand` has `media()` relationship
- **Eager Loading**: Added `'media'` to `with()` in mount method

### 7. Product Detail Page (`ProductController::show()`)
**Status**: ✅ Added SEO with media library support
- **Priority**: `primaryImage->media->large_url` → `primaryImage->image_path` → `site_logo` → default
- **Model**: `ProductImage` has `media()` relationship
- **Eager Loading**: `'images.media'` in product query
- **Additional**: Added product-specific SEO fields (price, currency, availability)

### 8. Homepage Author Profile (`HomeController::author()`)
**Status**: ✅ Updated
- **Priority**: `authorProfile->media->large_url` → `authorProfile->avatar` → default avatar
- **Model**: `AuthorProfile` has `media()` relationship
- **Eager Loading**: `$authorProfile->load('media')`

---

## Image Priority Logic

### Standard Priority (Settings Pages)
```
1. Media Library Image (media->large_url)
2. Legacy og_image field
3. Legacy image/logo field
4. Site logo setting
5. Default fallback image
```

### Blog/Author Priority
```
1. Media Library Image (media->large_url)
2. Legacy avatar/featured_image field
3. Blog image setting
4. Default fallback image
```

### Product Priority
```
1. Primary Product Image Media (primaryImage->media->large_url)
2. Primary Product Image Path (primaryImage->image_path)
3. Site logo setting
4. Default fallback image
```

---

## Models with Media Library Support

| Model | Relationship | Field | Usage |
|-------|-------------|-------|-------|
| `Post` | `media()` | `media_id` | Blog post featured image |
| `BlogCategory` | `media()` | `media_id` | Blog category image |
| `AuthorProfile` | `media()` | `media_id` | Author avatar |
| `Brand` | `media()` | `media_id` | Brand logo |
| `Category` | `media()` | `media_id` | Product category image |
| `ProductImage` | `media()` | `media_id` | Product images |
| `ProductVariant` | `media()` | `media_id` | Variant-specific images |
| `HeroSlider` | `media()` | `media_id` | Homepage slider images |
| `User` | `media()` | `media_id` | User avatar |

---

## Benefits

### 1. Centralized Image Management
- All images managed through universal image uploader
- Consistent image optimization (WebP, compression)
- Multiple size variants (large, medium, small)

### 2. Better Performance
- Optimized images with WebP format
- Automatic size variants for different use cases
- CDN-ready URLs

### 3. SEO Improvements
- High-quality images for social media sharing
- Proper image dimensions (1200x630px recommended)
- Alt text and metadata support

### 4. Backward Compatibility
- Legacy image fields still work as fallback
- Gradual migration path
- No breaking changes

---

## Usage Examples

### Blog Post SEO Image
```php
'og_image' => ($post->media && $post->media->large_url)
    ? $post->media->large_url
    : ($post->featured_image 
        ? asset('storage/' . $post->featured_image) 
        : (\App\Models\SiteSetting::get('blog_image') 
            ? asset('storage/' . \App\Models\SiteSetting::get('blog_image'))
            : asset('images/og-default.jpg')))
```

### Product SEO Image
```php
$primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();

'og_image' => ($primaryImage && $primaryImage->media && $primaryImage->media->large_url)
    ? $primaryImage->media->large_url
    : ($primaryImage && $primaryImage->image_path
        ? asset('storage/' . $primaryImage->image_path)
        : (\App\Models\SiteSetting::get('site_logo')
            ? asset('storage/' . \App\Models\SiteSetting::get('site_logo'))
            : asset('images/og-default.jpg')))
```

### Category/Brand SEO Image
```php
'og_image' => ($category->media && $category->media->large_url)
    ? $category->media->large_url
    : (!empty($category->og_image)
        ? asset('storage/' . $category->og_image)
        : ($category->image 
            ? asset('storage/' . $category->image) 
            : (\App\Models\SiteSetting::get('site_logo')
                ? asset('storage/' . \App\Models\SiteSetting::get('site_logo'))
                : asset('images/og-default.jpg'))))
```

---

## Files Modified

### Controllers
1. `app/Modules/Blog/Controllers/Frontend/BlogController.php`
   - Updated `category()` method - added media eager loading
   - Updated `author()` method - added media eager loading and image priority

2. `app/Http/Controllers/BrandController.php`
   - Updated `show()` method - added media eager loading and image priority

3. `app/Http/Controllers/ProductController.php`
   - Updated `show()` method - added complete SEO data with media library support
   - Added product-specific meta tags (price, currency, availability)

4. `app/Http/Controllers/HomeController.php`
   - Updated `author()` method - added media eager loading and image priority

### Livewire Components
5. `app/Livewire/Shop/ProductList.php`
   - Updated `mount()` method - added media to eager loading
   - Updated `getSeoDataProperty()` - added media library image priority for both category and brand

---

## Testing Checklist

- [ ] Blog post page shows media library image in social share
- [ ] Blog category page shows media library image in social share
- [ ] Author profile page shows media library image in social share
- [ ] Brand page shows media library image in social share
- [ ] Product category page shows media library image in social share
- [ ] Product detail page shows media library image in social share
- [ ] Homepage (author profile) shows media library image in social share
- [ ] Fallback images work when media library image not set
- [ ] Legacy image fields still work as fallback
- [ ] Social media preview tools show correct images (Facebook, Twitter, LinkedIn)

---

## Social Media Preview

### Test URLs
- **Facebook Debugger**: https://developers.facebook.com/tools/debug/
- **Twitter Card Validator**: https://cards-dev.twitter.com/validator
- **LinkedIn Post Inspector**: https://www.linkedin.com/post-inspector/

### Recommended Image Sizes
- **Open Graph**: 1200x630px (1.91:1 ratio)
- **Twitter Card**: 1200x675px (16:9 ratio) or 1200x1200px (1:1 ratio)
- **Minimum**: 600x315px

---

## Migration Guide

### For Existing Content

1. **Upload images to media library** using Universal Image Uploader
2. **Assign media_id** to respective models (posts, categories, brands, etc.)
3. **Test SEO** using social media preview tools
4. **Keep legacy images** as fallback (no need to delete)

### For New Content

1. **Use Universal Image Uploader** for all new images
2. **Media library images** will be used automatically for SEO
3. **Legacy fields** can be left empty

---

## Future Enhancements

1. **Automatic Migration Tool**: Script to migrate legacy images to media library
2. **Admin Interface**: Bulk assign media library images to existing content
3. **Image Validation**: Ensure images meet social media requirements
4. **Preview Tool**: Admin can preview how content appears on social media
5. **Analytics**: Track which images perform best on social media

---

## Summary

All SEO-enabled pages now prioritize media library images for social media sharing while maintaining backward compatibility with legacy image fields. This provides:

- ✅ Better image quality and optimization
- ✅ Centralized image management
- ✅ Multiple size variants
- ✅ WebP format support
- ✅ Backward compatibility
- ✅ No breaking changes
