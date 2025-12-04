# Homepage Settings Management Guide

## Overview
A complete backend system for managing homepage content including hero sliders and general settings.

## Features Implemented

### ✅ Hero Slider Management
- **Add/Edit/Delete** sliders with images
- **Drag & Drop** reordering (using SortableJS)
- **Active/Inactive** toggle for each slider
- **Custom links** and button text
- **Image upload** with preview
- **Database-driven** content

### ✅ General Settings
- Site title and tagline
- Featured products configuration
- New arrivals settings
- Promotional banner settings
- Settings grouped by category

### ✅ Frontend Integration
- Hero slider component pulls from database
- Autoplay/pause functionality
- Navigation overlay (50%+ on slider)
- Smooth transitions

## File Structure

```
app/
├── Models/
│   ├── HomepageSetting.php      # Settings model with caching
│   └── HeroSlider.php            # Slider model
├── Http/Controllers/Admin/
│   └── HomepageSettingController.php  # Admin controller

database/
├── migrations/
│   └── 2025_11_06_100000_create_homepage_settings_table.php
└── seeders/
    └── HomepageSettingSeeder.php  # Default data

resources/views/
├── admin/homepage-settings/
│   ├── index.blade.php           # Main admin page
│   └── partials/
│       └── slider-form.blade.php # Slider form
└── components/frontend/
    └── hero-slider.blade.php     # Frontend slider

routes/
└── web.php                        # Routes added
```

## Usage

### Access Admin Panel
1. Navigate to: `/admin/homepage-settings`
2. Menu location: **Content > Homepage Settings**

### Managing Hero Sliders

#### Add New Slider
1. Click "Add Slider" button
2. Fill in the form:
   - **Title** (required): Main heading
   - **Subtitle** (optional): Secondary text
   - **Image** (required): Upload banner image (1920x400px recommended)
   - **Link URL** (optional): Where the slider links to
   - **Button Text** (optional): CTA button text
   - **Display Order**: Lower numbers appear first
   - **Active**: Toggle to show/hide on homepage
3. Click "Add Slider"

#### Edit Slider
1. Click the edit icon (pencil) on any slider
2. Modify fields as needed
3. Upload new image to replace (optional)
4. Click "Update Slider"

#### Reorder Sliders
- Simply **drag and drop** sliders using the handle icon (≡)
- Order saves automatically

#### Delete Slider
- Click the delete icon (trash)
- Confirm deletion
- Image is automatically removed from storage

### Managing General Settings

1. Click the "General Settings" tab
2. Modify settings grouped by category:
   - **General**: Site title, tagline
   - **Featured**: Featured products settings
   - **Banner**: Promotional banner content
3. Click "Save Settings"

## Database Tables

### `homepage_settings`
```sql
- id
- key (unique)
- value
- type (text, textarea, image, json, boolean)
- group (general, featured, banner)
- order
- description
- timestamps
```

### `hero_sliders`
```sql
- id
- title
- subtitle
- image (stored in storage/app/public/sliders)
- link
- button_text
- order
- is_active
- timestamps
```

## API Methods

### HomepageSetting Model
```php
// Get a setting value
HomepageSetting::get('site_title', 'Default');

// Set a setting value
HomepageSetting::set('site_title', 'My Store', 'text', 'general');

// Clear cache
HomepageSetting::clearCache();
```

### HeroSlider Model
```php
// Get active sliders
$sliders = HeroSlider::getActive();

// Get image URL
$slider->image_url;
```

## Frontend Usage

The hero slider component automatically displays active sliders:

```blade
<x-frontend.hero-slider />
```

## Image Storage

- Sliders: `storage/app/public/sliders/`
- Settings: `storage/app/public/homepage/`
- Public URL: `/storage/sliders/` and `/storage/homepage/`

## Caching

Settings are cached for 1 hour (3600 seconds) to improve performance.
Cache is automatically cleared when settings are updated.

## Customization

### Add New Setting
```php
HomepageSetting::create([
    'key' => 'my_custom_setting',
    'value' => 'Default value',
    'type' => 'text',
    'group' => 'general',
    'order' => 10,
    'description' => 'My custom setting description',
]);
```

### Modify Slider Autoplay Speed
Edit `resources/views/components/frontend/hero-slider.blade.php`:
```javascript
startAutoplay() {
    this.autoplayInterval = setInterval(() => {
        this.nextSlide();
    }, 5000); // Change 5000 to desired milliseconds
}
```

## Troubleshooting

### Images not showing
1. Ensure storage link exists: `php artisan storage:link`
2. Check file permissions on `storage/app/public`
3. Verify image was uploaded successfully

### Drag & Drop not working
1. Check browser console for errors
2. Ensure SortableJS is loaded
3. Clear browser cache

### Settings not saving
1. Check form validation errors
2. Verify CSRF token is present
3. Check file upload size limits in `php.ini`

## Security Notes

- All routes protected by `auth` middleware
- CSRF protection on all forms
- File upload validation (image types, max 2MB)
- SQL injection protection via Eloquent ORM

## Performance

- Settings cached for 1 hour
- Images optimized for web
- Lazy loading on frontend
- Minimal database queries

## Future Enhancements

Potential features to add:
- [ ] Image cropping/resizing tool
- [ ] Slider scheduling (start/end dates)
- [ ] A/B testing for sliders
- [ ] Analytics integration
- [ ] Multi-language support
- [ ] Video slider support
- [ ] Mobile-specific images

---

**Created:** November 6, 2025  
**Version:** 1.0  
**Status:** Production Ready ✅
