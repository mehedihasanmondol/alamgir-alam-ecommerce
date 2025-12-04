# Site Logo Management System - Complete Guide

## Overview
A comprehensive site settings management system has been implemented, allowing administrators to manage site logos, branding, and other site-wide settings through the admin panel.

## Features Implemented

### 1. **Site Settings Model & Database**
- **Model**: `App\Models\SiteSetting`
- **Table**: `site_settings`
- **Caching**: Settings are cached for 24 hours for optimal performance
- **Auto-cache clearing**: Cache automatically clears when settings are updated

### 2. **Admin Panel Interface**
- **Route**: `/admin/site-settings`
- **Access**: Admin role required
- **Features**:
  - Upload site logo (frontend)
  - Upload admin panel logo
  - Upload favicon
  - Manage site name and tagline
  - Configure contact information
  - Set social media URLs
  - Configure SEO meta tags
  - Set primary brand color

### 3. **Dynamic Logo Display**
- **Frontend Header**: Automatically displays uploaded logo or falls back to site name
- **Admin Header**: Shows admin-specific logo or default admin branding
- **Responsive**: Logos are properly sized and responsive

## File Structure

```
app/
├── Models/
│   └── SiteSetting.php                    # Model with caching
├── Http/Controllers/Admin/
│   └── SiteSettingController.php          # Admin controller
database/
├── migrations/
│   └── 2025_11_11_132700_create_site_settings_table.php
└── seeders/
    └── SiteSettingSeeder.php              # Default settings
resources/
└── views/
    ├── admin/site-settings/
    │   └── index.blade.php                # Admin settings page
    ├── layouts/
    │   ├── app.blade.php                  # Frontend layout (updated)
    │   └── admin.blade.php                # Admin layout (updated)
    └── components/frontend/
        └── header.blade.php               # Frontend header (updated)
routes/
└── admin.php                              # Routes added
```

## Available Settings

### General Settings
- **site_name**: Site name (default: "iHerb")
- **site_tagline**: Site tagline/description
- **site_email**: Contact email address
- **site_phone**: Contact phone number

### Appearance Settings
- **site_logo**: Main site logo (frontend)
- **admin_logo**: Admin panel logo
- **site_favicon**: Site favicon
- **primary_color**: Primary brand color (hex code)

### Social Media
- **facebook_url**: Facebook page URL
- **twitter_url**: Twitter profile URL
- **instagram_url**: Instagram profile URL
- **youtube_url**: YouTube channel URL

### SEO Settings
- **meta_description**: Default meta description
- **meta_keywords**: Default meta keywords

## Usage Guide

### For Administrators

#### Accessing Site Settings
1. Log in to admin panel
2. Navigate to **System → Site Settings** in the sidebar
3. Or visit: `http://yourdomain.com/admin/site-settings`

#### Uploading a Logo
1. Go to Site Settings page
2. Find the "Appearance Settings" section
3. Click "Choose File" under "Site Logo"
4. Select your logo image (PNG, JPG, WEBP - Max 2MB)
5. Preview will appear immediately
6. Click "Save Settings" at the bottom

#### Removing a Logo
1. Hover over the current logo image
2. Click the red X button in the top-right corner
3. Confirm the removal
4. Logo will be removed immediately

#### Recommended Logo Sizes
- **Site Logo**: 200x60px (or similar aspect ratio)
- **Admin Logo**: 180x50px (or similar aspect ratio)
- **Favicon**: 32x32px or 64x64px

### For Developers

#### Getting a Setting Value
```php
use App\Models\SiteSetting;

// Get a single setting
$siteName = SiteSetting::get('site_name', 'Default Name');

// Get all settings (cached)
$allSettings = SiteSetting::getAllCached();

// Get settings grouped by category
$groupedSettings = SiteSetting::getAllGrouped();
```

#### Setting a Value Programmatically
```php
use App\Models\SiteSetting;

// Update a setting
SiteSetting::set('site_name', 'New Site Name');

// Clear cache manually if needed
SiteSetting::clearCache();
```

#### Using Settings in Blade Views
```blade
@php
    $siteLogo = \App\Models\SiteSetting::get('site_logo');
    $siteName = \App\Models\SiteSetting::get('site_name', 'Default');
@endphp

@if($siteLogo)
    <img src="{{ asset('storage/' . $siteLogo) }}" alt="{{ $siteName }}">
@else
    <h1>{{ $siteName }}</h1>
@endif
```

#### Adding New Settings
1. Add to seeder (`database/seeders/SiteSettingSeeder.php`):
```php
[
    'key' => 'new_setting',
    'value' => 'default value',
    'type' => 'text', // text, image, textarea, boolean
    'group' => 'general',
    'label' => 'New Setting',
    'description' => 'Description of the setting',
    'order' => 5,
]
```

2. Run seeder:
```bash
php artisan db:seed --class=SiteSettingSeeder
```

## API Endpoints

### Admin Routes
- **GET** `/admin/site-settings` - View settings page
- **PUT** `/admin/site-settings` - Update settings
- **POST** `/admin/site-settings/remove-logo` - Remove logo image

## Performance

### Caching Strategy
- All settings are cached for 24 hours (86400 seconds)
- Cache key: `site_settings_all`
- Cache automatically clears when:
  - A setting is updated
  - A setting is deleted
  - Manual clear via `SiteSetting::clearCache()`

### Optimization Tips
1. Settings are loaded once per request and cached
2. Use `SiteSetting::get()` instead of querying database directly
3. Avoid calling settings in loops
4. Consider using view composers for frequently accessed settings

## Security

### File Upload Security
- Only image files allowed (jpeg, png, jpg, gif, webp)
- Maximum file size: 2MB
- Files stored in `storage/app/public/site-settings/`
- Old images automatically deleted when replaced

### Access Control
- Only users with 'admin' role can access settings
- Protected by Laravel's authentication and role middleware
- CSRF protection on all forms

## Troubleshooting

### Logo Not Displaying
1. Check if storage link exists:
   ```bash
   php artisan storage:link
   ```
2. Verify file permissions on `storage/app/public/`
3. Clear cache:
   ```bash
   php artisan cache:clear
   ```

### Settings Not Updating
1. Clear application cache:
   ```bash
   php artisan cache:clear
   ```
2. Clear config cache:
   ```bash
   php artisan config:clear
   ```
3. Check database connection

### Image Upload Fails
1. Check `upload_max_filesize` in php.ini
2. Check `post_max_size` in php.ini
3. Verify storage directory permissions
4. Check available disk space

## Migration Commands

```bash
# Run migration
php artisan migrate --path=database/migrations/2025_11_11_132700_create_site_settings_table.php

# Rollback migration
php artisan migrate:rollback --step=1

# Seed default settings
php artisan db:seed --class=SiteSettingSeeder

# Fresh migration with seed
php artisan migrate:fresh --seed
```

## Testing

### Manual Testing Checklist
- [ ] Upload site logo
- [ ] Upload admin logo
- [ ] Upload favicon
- [ ] Update site name
- [ ] Update contact information
- [ ] Remove logo image
- [ ] Verify logo displays on frontend
- [ ] Verify logo displays in admin panel
- [ ] Test with different image formats
- [ ] Test with large images
- [ ] Verify cache clearing works

### Browser Testing
- [ ] Chrome/Edge
- [ ] Firefox
- [ ] Safari
- [ ] Mobile browsers

## Future Enhancements

### Potential Features
1. **Logo Variants**: Light/dark mode logos
2. **Image Cropping**: Built-in image cropper
3. **Multiple Languages**: Multilingual settings
4. **Theme Settings**: Color scheme customization
5. **Email Templates**: Customizable email settings
6. **Maintenance Mode**: Site maintenance settings
7. **Analytics**: Google Analytics integration settings
8. **SMTP Settings**: Email configuration
9. **Payment Gateway**: Payment settings management
10. **Backup/Restore**: Settings backup functionality

## Support

### Common Issues
1. **Permission Denied**: Run `chmod -R 775 storage/`
2. **Symlink Missing**: Run `php artisan storage:link`
3. **Cache Issues**: Run `php artisan cache:clear`

### Getting Help
- Check Laravel logs: `storage/logs/laravel.log`
- Enable debug mode in `.env`: `APP_DEBUG=true`
- Check browser console for JavaScript errors

## Changelog

### Version 1.0.0 (2025-11-11)
- ✅ Initial implementation
- ✅ Site logo management
- ✅ Admin logo management
- ✅ Favicon support
- ✅ General settings (name, tagline, contact)
- ✅ Social media URLs
- ✅ SEO meta tags
- ✅ Caching system
- ✅ Image upload/removal
- ✅ Dynamic logo display
- ✅ Admin panel integration

---

**Last Updated**: November 11, 2025
**Status**: ✅ Production Ready
