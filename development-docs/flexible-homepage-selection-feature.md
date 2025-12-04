# Flexible Homepage Selection Feature

**Implementation Date:** 2025-11-16  
**Module:** Site Settings - Homepage Management  
**Status:** ✅ Completed

---

## Overview

Implemented a flexible and extensible homepage selection system that allows administrators to choose different content types as the website homepage. The system is designed with extensibility in mind, making it easy to add new homepage types in the future.

---

## Features

### 1. **Homepage Type Selection**
Admins can choose from multiple homepage types:
- ✅ **Default Homepage**: Standard e-commerce homepage with products, categories, and featured sections
- ✅ **Author Profile Homepage**: Display a specific author's profile page as the homepage
- 🔮 **Future Extensible Types** (commented in code, ready to implement):
  - Category Page Homepage
  - Blog Index Homepage
  - Custom Page Homepage
  - Any other custom type

### 2. **Dynamic Author Selection**
When "Author Profile" is selected as the homepage type:
- Dropdown menu shows all available authors
- Displays author name and job title for easy identification
- Only enabled when Author Profile homepage type is selected
- Falls back to default homepage if no author is selected or author not found

### 3. **Admin Interface**
- Clean, intuitive interface in Site Settings > Homepage section
- Real-time preview of selected homepage type
- Contextual help text explaining each option
- Dropdown automatically enables/disables based on homepage type
- Individual save button for homepage settings

---

## Technical Implementation

### File Structure

```
config/
└── homepage.php                              # Homepage types configuration

database/
└── seeders/
    └── SiteSettingSeeder.php                 # Database settings

app/
├── Http/
│   └── Controllers/
│       ├── Admin/
│       │   └── SiteSettingController.php     # Admin controller
│       └── HomeController.php                 # Frontend homepage controller
└── Livewire/
    └── Admin/
        └── SettingSection.php                 # Livewire component

resources/
└── views/
    ├── admin/
    │   └── site-settings/
    │       └── index.blade.php                # Admin settings page
    └── livewire/
        └── admin/
            └── setting-section.blade.php      # Settings form component
```

---

## Configuration File

### `config/homepage.php`

```php
<?php

return [
    'types' => [
        'default' => [
            'key' => 'default',
            'label' => 'Default Homepage',
            'description' => 'Display the standard homepage with featured products.',
            'icon' => 'home',
            'requires' => [],
        ],
        
        'author_profile' => [
            'key' => 'author_profile',
            'label' => 'Author Profile',
            'description' => 'Display a specific author\'s profile page as the homepage.',
            'icon' => 'user',
            'requires' => ['author_id'],
        ],
        
        // Future types can be easily added here
    ],
    
    'default_type' => 'default',
    
    'cache' => [
        'enabled' => true,
        'ttl' => 3600,
        'key' => 'homepage_settings',
    ],
];
```

**Benefits:**
- ✅ Centralized configuration
- ✅ Easy to add new homepage types
- ✅ Self-documenting structure
- ✅ Built-in caching support

---

## Database Settings

### Added to `SiteSettingSeeder.php`

```php
// Homepage Settings
[
    'key' => 'homepage_type',
    'value' => 'default',
    'type' => 'select',
    'group' => 'homepage',
    'label' => 'Homepage Type',
    'description' => 'Select what content to display on the homepage',
    'order' => 1,
],
[
    'key' => 'homepage_author_id',
    'value' => null,
    'type' => 'select',
    'group' => 'homepage',
    'label' => 'Featured Author',
    'description' => 'Select an author to display (only for Author Profile homepage type)',
    'order' => 2,
],
```

---

## Controller Logic

### `HomeController.php` - Homepage Routing

```php
public function index()
{
    // Check homepage type setting
    $homepageType = SiteSetting::get('homepage_type', config('homepage.default_type', 'default'));
    
    // Handle different homepage types
    switch ($homepageType) {
        case 'author_profile':
            return $this->showAuthorHomepage();
        case 'default':
        default:
            return $this->showDefaultHomepage();
    }
}

protected function showAuthorHomepage()
{
    $authorId = SiteSetting::get('homepage_author_id');
    
    if (!$authorId) {
        return $this->showDefaultHomepage(); // Fallback
    }
    
    $author = User::with('authorProfile')->find($authorId);
    
    if (!$author || !$author->authorProfile) {
        return $this->showDefaultHomepage(); // Fallback
    }
    
    $categories = Category::whereNull('parent_id')
        ->where('is_active', true)
        ->orderBy('name')
        ->get();
    
    return view('frontend.blog.author', compact('author', 'categories'));
}

protected function showDefaultHomepage()
{
    // ... existing default homepage logic
}
```

**Key Points:**
- ✅ Clean switch-case structure (easy to extend)
- ✅ Graceful fallbacks if data is missing
- ✅ Reuses existing author profile view
- ✅ Caches settings for performance

---

## Admin Interface

### Livewire Component Updates

#### `SettingSection.php`

```php
public function render()
{
    $data = [];
    
    // Pass additional data for homepage settings
    if ($this->group === 'homepage') {
        $data['authors'] = User::where('role', 'author')
            ->orWhereHas('authorProfile')
            ->with('authorProfile')
            ->orderBy('name')
            ->get();
        $data['homepageTypes'] = config('homepage.types', []);
    }
    
    return view('livewire.admin.setting-section', $data);
}
```

#### `setting-section.blade.php`

```blade
@elseif($setting->type === 'select')
    @if($setting->key === 'homepage_type' && isset($homepageTypes))
        <!-- Homepage Type Select -->
        <select wire:model="settings.{{ $setting->key }}" class="...">
            <option value="">Select homepage type...</option>
            @foreach($homepageTypes as $type)
                <option value="{{ $type['key'] }}">
                    {{ $type['label'] }} - {{ $type['description'] }}
                </option>
            @endforeach
        </select>
        
        <!-- Show description for selected type -->
        @if(isset($settings['homepage_type']) && $settings['homepage_type'])
            <div class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm font-medium text-blue-900">{{ $selectedType['label'] }}</p>
                <p class="text-xs text-blue-700 mt-1">{{ $selectedType['description'] }}</p>
            </div>
        @endif
        
    @elseif($setting->key === 'homepage_author_id' && isset($authors))
        <!-- Author Select -->
        <select 
            wire:model="settings.{{ $setting->key }}"
            @if(!isset($settings['homepage_type']) || $settings['homepage_type'] !== 'author_profile') disabled @endif>
            <option value="">Select an author...</option>
            @foreach($authors as $author)
                <option value="{{ $author->id }}">
                    {{ $author->name }}
                    @if($author->authorProfile && $author->authorProfile->job_title)
                        - {{ $author->authorProfile->job_title }}
                    @endif
                </option>
            @endforeach
        </select>
        
        @if(!isset($settings['homepage_type']) || $settings['homepage_type'] !== 'author_profile')
            <p class="mt-2 text-xs text-gray-500">
                Select "Author Profile" as homepage type to enable this option
            </p>
        @endif
    @endif
@endif
```

**UI Features:**
- ✅ Contextual descriptions
- ✅ Conditional dropdown enabling
- ✅ Visual feedback with color coding
- ✅ Help text for users
- ✅ Real-time updates with Livewire

---

## Usage Guide

### For Administrators

#### Step 1: Access Homepage Settings
1. Login to admin panel
2. Navigate to **Site Settings**
3. Click on **Homepage** section in the sidebar

#### Step 2: Select Homepage Type
1. In the "Homepage Type" dropdown, choose:
   - **Default Homepage** - Standard e-commerce layout
   - **Author Profile** - Feature a specific author

#### Step 3: Configure Author (if applicable)
1. If you selected "Author Profile":
   - The "Featured Author" dropdown will become enabled
   - Select an author from the list
   - Author name and job title are shown for easy identification

#### Step 4: Save Settings
1. Click **"Save Homepage Settings"** button
2. Wait for success confirmation
3. Visit your website homepage to see the changes

### For Developers

#### Adding a New Homepage Type

**Step 1: Add to Configuration**

```php
// config/homepage.php
'types' => [
    // ... existing types ...
    
    'blog_index' => [
        'key' => 'blog_index',
        'label' => 'Blog Index',
        'description' => 'Display the blog listing page as the homepage.',
        'icon' => 'newspaper',
        'requires' => [],
    ],
],
```

**Step 2: Add Controller Method**

```php
// app/Http/Controllers/HomeController.php
public function index()
{
    $homepageType = SiteSetting::get('homepage_type', 'default');
    
    switch ($homepageType) {
        // ... existing cases ...
        
        case 'blog_index':
            return redirect()->route('blog.index');
        
        default:
            return $this->showDefaultHomepage();
    }
}
```

**Step 3: Add Database Setting (if needed)**

```php
// database/seeders/SiteSettingSeeder.php
[
    'key' => 'homepage_blog_category_id',
    'value' => null,
    'type' => 'select',
    'group' => 'homepage',
    'label' => 'Featured Blog Category',
    'description' => 'Select a blog category to display',
    'order' => 3,
],
```

**Step 4: Update View Logic (if needed)**

```blade
{{-- resources/views/livewire/admin/setting-section.blade.php --}}
@elseif($setting->key === 'homepage_blog_category_id' && isset($blogCategories))
    <select wire:model="settings.{{ $setting->key }}"
            @if($settings['homepage_type'] !== 'blog_index') disabled @endif>
        <option value="">Select a category...</option>
        @foreach($blogCategories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
@endif
```

---

## Extensibility

### Design Principles

1. **Configuration-Driven**
   - All homepage types defined in `config/homepage.php`
   - Easy to add new types without touching core code

2. **Graceful Degradation**
   - Falls back to default homepage if:
     - Selected type is invalid
     - Required data is missing
     - Author/resource not found

3. **Reusable Components**
   - Leverages existing views (author profile, blog pages, etc.)
   - No duplicate template code

4. **Modular Architecture**
   - Each homepage type has its own method
   - Easy to test and maintain separately

### Future Homepage Types (Ready to Implement)

#### 1. **Category Page Homepage**
```php
'category_page' => [
    'key' => 'category_page',
    'label' => 'Category Page',
    'description' => 'Display a specific product category as the homepage.',
    'icon' => 'folder',
    'requires' => ['category_id'],
],
```

#### 2. **Custom Page Homepage**
```php
'custom_page' => [
    'key' => 'custom_page',
    'label' => 'Custom Page',
    'description' => 'Display a custom static page as the homepage.',
    'icon' => 'file',
    'requires' => ['page_id'],
],
```

#### 3. **Blog Index Homepage**
```php
'blog_index' => [
    'key' => 'blog_index',
    'label' => 'Blog Index',
    'description' => 'Display the blog listing page as the homepage.',
    'icon' => 'newspaper',
    'requires' => [],
],
```

---

## Performance Considerations

### Caching Strategy
- Homepage type setting is cached via `SiteSetting` model
- Cache key: `homepage_settings`
- TTL: 3600 seconds (1 hour)
- Automatically cleared when settings are updated

### Database Queries
- Author query uses eager loading: `with('authorProfile')`
- Categories loaded only when needed
- Minimal overhead on default homepage (no extra queries)

### Best Practices
- Settings are loaded once per request
- Falls back quickly if resources not found
- No redirect loops (direct rendering)

---

## Testing Checklist

- [x] Default homepage displays correctly
- [x] Author profile homepage works with valid author
- [x] Fallback to default when author not found
- [x] Fallback to default when no author selected
- [x] Admin dropdown enables/disables correctly
- [x] Settings save successfully
- [x] Cache clears after save
- [x] Homepage updates immediately after save
- [x] Author dropdown shows correct information
- [x] Help text displays appropriately
- [x] Livewire updates work smoothly
- [x] Icons display in sidebar navigation

---

## Troubleshooting

### Issue: Homepage not changing after save
**Solution**: Clear cache manually
```bash
php artisan cache:clear
php artisan config:clear
```

### Issue: Author dropdown is disabled
**Solution**: Select "Author Profile" as homepage type first

### Issue: Homepage shows 404 or error
**Solution**: Check that:
- Selected author exists in database
- Author has an author_profile record
- Author profile view file exists
- Settings are saved correctly

### Issue: Settings not saving
**Solution**: Check Livewire component is working
```bash
php artisan livewire:publish --assets
```

---

## Security Considerations

- ✅ Only admins can access homepage settings
- ✅ Author validation before rendering
- ✅ Graceful error handling (no data exposure)
- ✅ Input sanitization via Livewire
- ✅ No direct database manipulation from frontend

---

## Migration Guide

### For Fresh Installations
1. Run migrations: `php artisan migrate`
2. Run seeders: `php artisan db:seed --class=SiteSettingSeeder`
3. Access admin panel > Site Settings > Homepage
4. Configure your desired homepage type

### For Existing Installations
1. Pull latest code changes
2. Run seeder to add new settings:
   ```bash
   php artisan db:seed --class=SiteSettingSeeder
   ```
3. Clear cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```
4. Configure in admin panel

### No Breaking Changes
- Default homepage remains unchanged
- Existing homepage URLs still work
- Backward compatible with all previous versions

---

## API Reference

### Configuration

```php
// Get homepage types
$types = config('homepage.types');

// Get default homepage type
$default = config('homepage.default_type');
```

### Settings

```php
// Get current homepage type
$type = SiteSetting::get('homepage_type', 'default');

// Get featured author ID
$authorId = SiteSetting::get('homepage_author_id');

// Set homepage type
SiteSetting::set('homepage_type', 'author_profile');

// Set featured author
SiteSetting::set('homepage_author_id', 1);
```

---

## Related Documentation

- [Author Role Management](./author-role-management-implementation.md)
- [Author Page Livewire Enhancement](./author-page-livewire-enhancement.md)
- [Site Settings System](./site-settings-system.md)

---

## Changelog

### Version 1.0 (2025-11-16)
- ✅ Created flexible homepage selection system
- ✅ Implemented homepage type configuration file
- ✅ Added database settings for homepage management
- ✅ Updated HomeController with dynamic routing
- ✅ Enhanced admin interface with Livewire components
- ✅ Added author profile homepage type
- ✅ Implemented graceful fallback mechanisms
- ✅ Prepared architecture for future homepage types
- ✅ Added comprehensive documentation

---

**Last Updated:** 2025-11-16  
**Version:** 1.0  
**Maintained By:** Development Team
