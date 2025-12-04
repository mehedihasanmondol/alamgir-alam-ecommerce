# Blog SEO Migration Guide

## Quick Setup Guide

Follow these steps to enable blog page SEO with the new `blog_image` setting.

---

## Step 1: Run Database Seeder

Add the new blog_image setting to your database:

```bash
php artisan db:seed --class=SiteSettingSeeder
```

**What this does**:
- Adds `blog_image` setting to site_settings table
- Configures as image upload type
- Sets appropriate order for admin display

---

## Step 2: Clear All Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
```

---

## Step 3: Upload Blog SEO Image (Admin Panel)

1. Login to admin panel
2. Navigate to **Settings** → **Site Settings**
3. Click on **Blog** tab
4. Find **Blog SEO Image** field
5. Upload an image (recommended: 1200x630px)
6. Click **Save Settings**

**Image Recommendations**:
- Size: 1200 x 630 pixels
- Format: JPG or PNG
- File size: Under 2MB
- Include: Blog branding, title, or key visual

---

## Step 4: Verify Settings

Check that these blog settings are configured:

```
✓ Blog Title: "Health & Wellness Blog"
✓ Blog Tagline: "Your source for health tips..."
✓ Blog Description: "Discover the latest health and wellness tips..."
✓ Blog Keywords: "health blog, wellness tips, nutrition advice..."
✓ Blog Image: [Uploaded]
```

---

## Step 5: Test SEO Implementation

### Check Page Title
1. Visit `/blog` page
2. Check browser tab title
3. Should show: `{Blog Title} | {Blog Tagline}`

### Check Meta Tags
1. Open browser developer tools (F12)
2. Go to Elements/Inspector tab
3. Look for `<head>` section
4. Verify these meta tags exist:
   - `<title>`
   - `<meta name="description">`
   - `<meta name="keywords">`
   - `<meta property="og:title">`
   - `<meta property="og:description">`
   - `<meta property="og:image">`
   - `<meta name="twitter:card">`

### Test Social Media Sharing

#### Facebook Debugger
1. Go to: https://developers.facebook.com/tools/debug/
2. Enter your blog URL: `https://yoursite.com/blog`
3. Click **Debug**
4. Verify:
   - Title shows with tagline
   - Description appears
   - Image displays correctly

#### Twitter Card Validator
1. Go to: https://cards-dev.twitter.com/validator
2. Enter your blog URL
3. Click **Preview Card**
4. Verify card displays correctly

---

## Expected Results

### Browser Tab
```
Health & Wellness Blog | Your source for health tips
```

### Google Search Result
```
Health & Wellness Blog | Your source for health tips
Discover the latest health and wellness tips, product reviews, and expert advice...
https://yoursite.com/blog
```

### Facebook Share
```
[Blog Image - 1200x630px]
Health & Wellness Blog | Your source for health tips
Discover the latest health and wellness tips, product reviews, and expert advice...
yoursite.com/blog
```

### Twitter Card
```
[Blog Image - 1200x630px]
Health & Wellness Blog | Your source for health tips
Discover the latest health and wellness tips...
yoursite.com/blog
```

---

## Troubleshooting

### Image not showing in social media
**Solution**: 
1. Ensure image is publicly accessible
2. Check file permissions in `storage` directory
3. Run: `php artisan storage:link`
4. Clear social media cache (Facebook Debugger)

### Title doesn't show tagline
**Solution**: 
1. Verify `blog_tagline` has a value in database
2. Clear all caches
3. Refresh browser

### Old meta tags still appearing
**Solution**:
```bash
php artisan cache:clear
php artisan view:clear
composer dump-autoload
```

---

## Rollback (If Needed)

If you need to revert changes:

1. **Remove setting from database**:
```sql
DELETE FROM site_settings WHERE `key` = 'blog_image';
```

2. **Revert code changes**:
```bash
git checkout HEAD -- app/Modules/Blog/Controllers/Frontend/BlogController.php
git checkout HEAD -- resources/views/frontend/blog/index.blade.php
```

3. **Clear caches**:
```bash
php artisan optimize:clear
```

---

## Summary of Changes

### New Database Setting
```php
[
    'key' => 'blog_image',
    'value' => null,
    'type' => 'image',
    'group' => 'blog',
    'label' => 'Blog SEO Image',
    'description' => 'Default image for blog page social media sharing',
    'order' => 5,
]
```

### SEO Title Format
- **Old**: `Blog Title - Site Name`
- **New**: `Blog Title | Blog Tagline`

### New Meta Tags Added
- Open Graph tags (og:title, og:description, og:image, og:type, og:url)
- Twitter Card tags (twitter:card, twitter:title, twitter:description, twitter:image)
- Proper canonical URL
- Complete fallback support

---

## Post-Migration Checklist

- [ ] Database seeder run successfully
- [ ] All caches cleared
- [ ] Blog image uploaded in admin panel
- [ ] Blog title and tagline configured
- [ ] Page title displays correctly in browser
- [ ] Meta tags present in page source
- [ ] Facebook preview shows correctly
- [ ] Twitter card displays correctly
- [ ] Image appears in social media shares
- [ ] Google search preview looks good
- [ ] Mobile view tested

---

## Support

For issues or questions:
1. Check `development-docs/blog-page-seo-implementation.md` for detailed documentation
2. Review browser console for JavaScript errors
3. Check Laravel logs: `storage/logs/laravel.log`
4. Clear all caches and try again

---

## Related Documentation
- `development-docs/blog-page-seo-implementation.md` - Full implementation guide
- `development-docs/homepage-dynamic-seo-implementation.md` - Homepage SEO
- `database/seeders/SiteSettingSeeder.php` - Settings configuration
