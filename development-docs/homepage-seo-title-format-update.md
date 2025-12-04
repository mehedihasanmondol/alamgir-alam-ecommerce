# Homepage SEO Title Format Update

## Update Date
November 20, 2025 (4:59 PM)

---

## Changes Made

### Title Format Updates

#### 1. Default Homepage (Ecommerce)
**Previous Format**: `{Site Name}` only  
**New Format**: `{Site Name} | {Site Tagline}`

**Example**:
- Old: `iHerb`
- New: `iHerb | Your Health & Wellness Store`

**Fallback**: If no tagline is set, displays site name only

**Implementation**:
```php
$siteName = SiteSetting::get('site_name', config('app.name'));
$siteTagline = SiteSetting::get('site_tagline', '');

$seoData = [
    'title' => $siteTagline ? $siteName . ' | ' . $siteTagline : $siteName,
    // ... other fields
];
```

#### 2. Author Profile Homepage
**Previous Format**: `{Author Name} - {Job Title}` (dash separator)  
**New Format**: `{Author Name} | {Job Title}` (pipe separator)

**Example**:
- Old: `Dr. Sarah Johnson - Nutritionist & Wellness Coach`
- New: `Dr. Sarah Johnson | Nutritionist & Wellness Coach`

**Reason**: Consistency with default homepage title format

**Implementation**:
```php
$jobTitle = $authorProfile->job_title ?? 'Author Profile';

$seoData = [
    'title' => $author->name . ' | ' . $jobTitle,
    // ... other fields
];
```

---

## Image Logic Clarification

### Default Homepage
- **Image Source**: `site_logo` setting (frontend logo)
- **Path**: `storage/{site_logo}`
- **Fallback**: `images/og-default.jpg`

### Author Profile Homepage
- **Image Source**: Author's `avatar` field (author image)
- **Path**: `storage/{avatar}`
- **Fallback**: `images/default-avatar.jpg`

---

## Files Modified

1. **`app/Http/Controllers/HomeController.php`**
   - Updated `showDefaultHomepage()` method
   - Updated `showAuthorHomepage()` method
   - Added tagline logic for default homepage
   - Changed separator from dash to pipe for author profile

2. **`app/Modules/Blog/Controllers/Frontend/BlogController.php`**
   - Updated `author()` method
   - Changed separator from dash to pipe

3. **`development-docs/homepage-dynamic-seo-implementation.md`**
   - Updated documentation with new title formats
   - Updated examples with tagline
   - Clarified image logic

4. **`editor-task-management.md`**
   - Updated latest completion section with new formats

---

## SEO Benefits

### 1. Better Branding
- Default homepage now includes tagline for better brand messaging
- Example: "iHerb | Your Health & Wellness Store" is more descriptive than just "iHerb"

### 2. Consistency
- Both homepage types now use pipe separator (|)
- Uniform title format across all pages

### 3. SEO Optimization
- More descriptive titles improve click-through rates
- Tagline provides additional context for search engines
- Better social media sharing previews

---

## Testing Checklist

### Default Homepage
- [ ] Title shows: `{Site Name} | {Site Tagline}`
- [ ] If no tagline, shows: `{Site Name}` only
- [ ] OG image uses `site_logo`
- [ ] Facebook preview shows correct title
- [ ] Twitter card shows correct title
- [ ] Google search result shows correct title

### Author Profile Homepage
- [ ] Title shows: `{Author Name} | {Job Title}`
- [ ] Uses pipe separator (|) not dash (-)
- [ ] OG image uses author avatar
- [ ] Facebook preview shows author info
- [ ] Twitter card shows author info
- [ ] Google search result shows correct title

---

## Configuration

### Site Settings Required
```php
// For Default Homepage
'site_name' => 'iHerb',
'site_tagline' => 'Your Health & Wellness Store',
'site_logo' => 'logos/iherb-logo.png',
'meta_description' => 'Shop premium health products...',
'meta_keywords' => 'health, wellness, supplements',
```

### Author Profile Required
```php
// For Author Profile Homepage
'name' => 'Dr. Sarah Johnson',
'job_title' => 'Nutritionist & Wellness Coach',
'avatar' => 'avatars/sarah-johnson.jpg',
'bio' => 'Helping people achieve optimal health...',
```

---

## Browser Tab Examples

### Default Homepage
```
Tab Title: iHerb | Your Health & Wellness Store
```

### Author Profile Homepage
```
Tab Title: Dr. Sarah Johnson | Nutritionist & Wellness Coach
```

---

## Social Media Preview Examples

### Facebook Share - Default Homepage
```
Title: iHerb | Your Health & Wellness Store
Description: Shop premium health and wellness products
Image: [iHerb Logo]
```

### Facebook Share - Author Profile Homepage
```
Title: Dr. Sarah Johnson | Nutritionist & Wellness Coach
Description: Helping people achieve optimal health through nutrition...
Image: [Author Avatar]
```

---

## Notes

1. **Backward Compatibility**: If `site_tagline` is empty, the system falls back to showing only `site_name`
2. **Separator Choice**: Pipe (|) is preferred over dash (-) for better visual separation in browser tabs
3. **Image Logic**: Each homepage type uses its appropriate image source (logo vs avatar)
4. **Consistency**: All controllers now use the same title format pattern

---

## Related Documentation
- `development-docs/homepage-dynamic-seo-implementation.md` - Full implementation guide
- `editor-task-management.md` - Task tracking and completion status
