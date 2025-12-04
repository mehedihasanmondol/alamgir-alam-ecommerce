# Homepage Dynamic SEO Implementation

## Overview
Implemented dynamic SEO metadata for the homepage that automatically switches between site-wide settings and author profile settings based on the configured homepage type.

## Implementation Date
November 20, 2025

---

## Features

### 1. Default Homepage SEO (Ecommerce)
When homepage is set to "default" or "/ecommerce", SEO content is sourced from:

- **Title**: `{Site Name} | {Site Tagline}` (e.g., "iHerb | Your Health & Wellness Store")
  - If no tagline: Just `{Site Name}`
- **Description**: `meta_description` from site settings
- **Keywords**: `meta_keywords` from site settings
- **OG Image**: `site_logo` from site settings (frontend logo logic)
- **OG Type**: `website`
- **Canonical URL**: Homepage URL (`/`)

### 2. Author Profile Homepage SEO
When homepage is set to "author_profile", SEO content is sourced from:

- **Title**: `{Author Name} | {Job Title}` (e.g., "John Doe | Health & Wellness Expert")
  - Uses pipe separator (|) for consistency
- **Description**: First 160 characters of author's bio (or fallback message)
- **Keywords**: `{Author Name}, author, blog, articles, writer, {Job Title}`
- **OG Image**: Author's avatar (author image logic)
- **OG Type**: `profile`
- **Canonical URL**: Homepage URL (`/`)
- **Author Meta**: Author's name

---

## Technical Implementation

### Files Modified

#### 1. HomeController.php
**Location**: `app/Http/Controllers/HomeController.php`

**Changes**:
- Added SEO data preparation in `showDefaultHomepage()` method
- Added SEO data preparation in `showAuthorHomepage()` method
- Both methods now pass `$seoData` array to views

**SEO Data Structure**:
```php
$seoData = [
    'title' => string,           // Page title
    'description' => string,     // Meta description
    'keywords' => string,        // Meta keywords
    'og_image' => string,        // Open Graph image URL
    'og_type' => string,         // Open Graph type (website/profile)
    'canonical' => string,       // Canonical URL
    'author_name' => string,     // Optional: Author name for profile pages
];
```

#### 2. frontend/home/index.blade.php
**Location**: `resources/views/frontend/home/index.blade.php`

**Changes**:
- Updated to use dynamic `$seoData` array instead of hardcoded settings
- Added Twitter Card meta tags
- Added conditional author meta tag
- All SEO sections now have fallbacks to site settings

**Meta Tags Added**:
- `@section('title')` - Dynamic title
- `@section('description')` - Dynamic description
- `@section('keywords')` - Dynamic keywords
- `@section('og_type')` - Dynamic OG type
- `@section('og_title')` - Dynamic OG title
- `@section('og_description')` - Dynamic OG description
- `@section('og_image')` - Dynamic OG image
- `@section('canonical')` - Dynamic canonical URL
- `@section('twitter_card')` - Twitter card type
- `@section('twitter_title')` - Twitter title
- `@section('twitter_description')` - Twitter description
- `@section('twitter_image')` - Twitter image
- `@section('author')` - Conditional author name

#### 3. frontend/blog/author.blade.php
**Location**: `resources/views/frontend/blog/author.blade.php`

**Changes**:
- Updated to use dynamic `$seoData` array with fallbacks
- Added Twitter Card meta tags
- Added conditional author meta tag
- Works both as standalone author page and as homepage

#### 4. BlogController.php
**Location**: `app/Modules/Blog/Controllers/Frontend/BlogController.php`

**Changes**:
- Added SEO data preparation in `author()` method
- Ensures consistent SEO metadata for author profile pages
- Uses author profile slug for canonical URL

---

## SEO Metadata Coverage

### Google
- ✅ Title tag
- ✅ Meta description
- ✅ Meta keywords
- ✅ Canonical URL
- ✅ Author meta (for author profiles)

### Facebook (Open Graph)
- ✅ og:type
- ✅ og:url (canonical)
- ✅ og:title
- ✅ og:description
- ✅ og:image
- ✅ og:site_name

### Twitter
- ✅ twitter:card
- ✅ twitter:url
- ✅ twitter:title
- ✅ twitter:description
- ✅ twitter:image

### Other Platforms
- ✅ LinkedIn (uses Open Graph)
- ✅ WhatsApp (uses Open Graph)
- ✅ Telegram (uses Open Graph)
- ✅ Pinterest (uses Open Graph)

---

## Usage Examples

### Example 1: Default Homepage
```php
// Site Settings
site_name = "iHerb"
site_tagline = "Your Health & Wellness Store"
meta_description = "Shop premium health and wellness products"
meta_keywords = "health, wellness, supplements, vitamins"
site_logo = "logos/iherb-logo.png"

// Generated SEO
Title: "iHerb | Your Health & Wellness Store"
Description: "Shop premium health and wellness products"
Keywords: "health, wellness, supplements, vitamins"
OG Image: "https://example.com/storage/logos/iherb-logo.png"
OG Type: "website"
```

### Example 2: Author Profile Homepage
```php
// Author Profile
name = "Dr. Sarah Johnson"
job_title = "Nutritionist & Wellness Coach"
bio = "Helping people achieve optimal health through nutrition..."
avatar = "avatars/sarah-johnson.jpg"

// Generated SEO
Title: "Dr. Sarah Johnson | Nutritionist & Wellness Coach"
Description: "Helping people achieve optimal health through nutrition..."
Keywords: "Dr. Sarah Johnson, author, blog, articles, writer, Nutritionist & Wellness Coach"
OG Image: "https://example.com/storage/avatars/sarah-johnson.jpg"
OG Type: "profile"
Author: "Dr. Sarah Johnson"
```

---

## Benefits

### 1. SEO Optimization
- **Accurate Metadata**: Each homepage type has appropriate SEO metadata
- **Rich Snippets**: Proper Open Graph tags for social media sharing
- **Search Engine Friendly**: Correct canonical URLs and meta tags

### 2. Social Media Sharing
- **Facebook**: Displays correct title, description, and image
- **Twitter**: Shows proper Twitter Card with image
- **LinkedIn**: Uses Open Graph data for rich previews
- **WhatsApp**: Displays preview with image and description

### 3. Flexibility
- **Dynamic Content**: SEO changes automatically based on homepage type
- **Fallback Support**: Always has default values if data is missing
- **Consistent Structure**: Same SEO data structure across all pages

### 4. Maintainability
- **Centralized Logic**: SEO data preparation in controller
- **Reusable Views**: Views work for multiple contexts
- **Easy Updates**: Change SEO strategy in one place

---

## Testing Checklist

### Default Homepage
- [ ] Title shows site name
- [ ] Description shows site meta description
- [ ] Keywords show site meta keywords
- [ ] OG image shows site logo
- [ ] OG type is "website"
- [ ] Canonical URL is homepage URL
- [ ] Twitter card displays correctly
- [ ] Facebook preview shows correct data

### Author Profile Homepage
- [ ] Title shows author name and job title
- [ ] Description shows author bio (truncated)
- [ ] Keywords include author name and job title
- [ ] OG image shows author avatar
- [ ] OG type is "profile"
- [ ] Author meta tag is present
- [ ] Canonical URL is homepage URL
- [ ] Twitter card displays correctly
- [ ] Facebook preview shows author info

### Social Media Sharing
- [ ] Facebook shows correct preview
- [ ] Twitter shows correct card
- [ ] LinkedIn shows correct preview
- [ ] WhatsApp shows correct preview
- [ ] Telegram shows correct preview

---

## Future Enhancements

### Potential Improvements
1. **Schema.org Markup**: Add structured data for better search results
2. **Multiple Languages**: Support for multilingual SEO metadata
3. **A/B Testing**: Test different SEO strategies
4. **Analytics Integration**: Track SEO performance
5. **Dynamic OG Images**: Generate custom OG images for each page type
6. **Video Support**: Add video meta tags for author profiles with video content
7. **Article Schema**: Add article schema for blog posts on author profile

### Additional Homepage Types
- Category page as homepage (with category SEO)
- Custom page as homepage (with page SEO)
- Blog index as homepage (with blog SEO)

---

## Related Files
- `app/Http/Controllers/HomeController.php`
- `app/Modules/Blog/Controllers/Frontend/BlogController.php`
- `resources/views/frontend/home/index.blade.php`
- `resources/views/frontend/blog/author.blade.php`
- `resources/views/layouts/app.blade.php`
- `app/Models/SiteSetting.php`
- `app/Models/AuthorProfile.php`

---

## Notes
- SEO data is prepared in the controller and passed to views
- Views use `$seoData` array with fallbacks to site settings
- All meta tags support Google, Facebook, Twitter, and other platforms
- Implementation follows Laravel best practices
- Code is maintainable and extensible
