# Site Settings Dropdown Fixes & Admin Profile Page

**Date**: November 27, 2025  
**Developer**: Windsurf AI  
**Status**: ✅ Completed

---

## Overview

Fixed critical issues with site settings dropdowns and added admin profile page functionality:

1. **Select Dropdown Options Not Showing** - Fixed author page and sitemap settings dropdowns
2. **Admin Profile Page** - Added profile view accessible from header dropdown

---

## Feature 1: Site Settings Dropdown Fixes

### Problem
Select-type settings in admin site settings were showing only "Select an option..." with no actual options displayed. Affected settings:
- **Author Page Settings**: Appointment Form Width, Feedback Section Width  
- **SEO Settings**: Enable Sitemap

### Root Cause
The generic select handler in `setting-section.blade.php` wasn't parsing the `options` JSON field from the database.

### Solution

#### 1. Updated Livewire View Component
**File**: `resources/views/livewire/admin/setting-section.blade.php`

Added JSON parsing logic for generic select fields:

```php
@else
    <!-- Generic Select -->
    @php
        $options = [];
        if ($setting->options) {
            $options = is_string($setting->options) ? json_decode($setting->options, true) : $setting->options;
        }
    @endphp
    <select 
        wire:model="settings.{{ $setting->key }}"
        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white">
        <option value="">Select an option...</option>
        @if(is_array($options))
            @foreach($options as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        @endif
    </select>
@endif
```

#### 2. Fixed Sitemap Options Format
**File**: `database/seeders/SiteSettingSeeder.php`

Changed from invalid format to proper JSON:
```php
// Before
'options' => '1:Enabled,0:Disabled',

// After
'options' => json_encode(['1' => 'Enabled', '0' => 'Disabled']),
```

#### 3. Added Select Type Badge
Added visual indicator for dropdown fields in settings UI:
```php
@elseif($setting->type === 'select')
    <span class="text-xs font-normal px-2 py-1 bg-teal-100 text-teal-700 rounded">Dropdown</span>
```

#### 4. Added Author Page Icon
Added to both mobile and desktop sidebars:
```php
'author_page' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
```

### Settings Now Working

#### Author Page Settings (`author_page` group)
- ✅ **Appointment Form Width**: Full Width / Half Width / Quarter Width
- ✅ **Feedback Section Width**: Full Width / Half Width / Quarter Width

#### SEO Settings (`seo` group)
- ✅ **Enable Sitemap**: Enabled / Disabled

### Testing
1. Navigate to **Admin → Site Settings → Author Page**
2. Verify both dropdowns show all 3 options (full/half/quarter)
3. Navigate to **Admin → Site Settings → SEO**
4. Verify sitemap dropdown shows Enabled/Disabled options
5. Save and verify values persist correctly

---

## Feature 2: Admin Profile Page

### Problem
No way for admin users to view their own profile from the admin panel. The header dropdown had a "Profile" link but it wasn't implemented.

### Solution
Created a dedicated admin profile route that displays the logged-in admin's information using the existing user show template.

### Implementation

#### 1. Added Profile Route
**File**: `routes/web.php`

```php
// Admin Profile
Route::get('/profile', function () {
    if (auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized access');
    }
    $user = auth()->user();
    return view('admin.users.show', compact('user'));
})->name('profile');
```

#### 2. Route Details
- **URL**: `/admin/profile`
- **Route Name**: `admin.profile`
- **Middleware**: `auth`
- **Access**: Admin role only
- **View**: Reuses `admin.users.show` template

### Features
The admin profile page displays:
- **Profile Photo**: Avatar with media library support or initials fallback
- **Basic Info**: Name, ID, Role badge, Status badge
- **Contact Information**: Email, Mobile phone
- **Address**: Full address details if available
- **Account Info**: Join date, last login, last updated
- **Activity Stats**: Post counts (for authors), profile completeness
- **Quick Actions**: Edit profile button

### Header Integration
The existing header dropdown "Profile" link now navigates to:
```php
route('admin.profile')
// Results in: /admin/profile
```

### Security
- ✅ Authentication required
- ✅ Role verification (admin only)
- ✅ 403 error for non-admin users
- ✅ Uses same permissions as other admin routes

### User Experience
1. Admin clicks their name/avatar in header
2. Dropdown appears with "Profile" option
3. Clicks "Profile" → Navigates to `/admin/profile`
4. Sees their own user information
5. Can click "Edit User" to modify profile

---

## Database Migration Required

Run the seeder to update sitemap settings:
```bash
php artisan db:seed --class=SiteSettingSeeder
```

**Note**: The seeder uses smart upsert logic, so it won't overwrite existing custom values.

---

## Files Modified

### Select Dropdown Fixes
1. `resources/views/livewire/admin/setting-section.blade.php`
   - Added JSON parsing for generic selects
   - Added select type badge
   - Added author_page icon

2. `database/seeders/SiteSettingSeeder.php`
   - Fixed sitemap_enabled options format

3. `resources/views/admin/site-settings/index.blade.php`
   - Added author_page icon to mobile and desktop sidebars

### Admin Profile
4. `routes/web.php`
   - Added admin profile route

### Reused (No Changes)
- `resources/views/admin/users/show.blade.php` (existing template)

---

## Testing Checklist

### Site Settings Dropdowns
- [ ] Navigate to Admin → Site Settings → Author Page
- [ ] Verify "Appointment Form Width" shows: Full Width, Half Width, Quarter Width
- [ ] Verify "Feedback Section Width" shows all 3 width options
- [ ] Select different values and save - verify persistence
- [ ] Navigate to Admin → Site Settings → SEO
- [ ] Verify "Enable Sitemap" shows: Enabled, Disabled
- [ ] Test toggling and saving

### Admin Profile
- [ ] Login as admin user
- [ ] Click profile dropdown in header
- [ ] Click "Profile" link
- [ ] Verify redirects to `/admin/profile`
- [ ] Verify displays correct user information
- [ ] Verify "Edit User" button works
- [ ] Try accessing as non-admin (should get 403)
- [ ] Verify page layout matches other user detail pages

---

## Technical Notes

### JSON Options Format
All select-type settings must use this format in seeder:
```php
'options' => json_encode([
    'value1' => 'Label 1',
    'value2' => 'Label 2',
    'value3' => 'Label 3'
])
```

### Generic Select Handler
The blade component automatically:
1. Checks if `options` exists
2. Parses JSON string or uses array directly
3. Generates `<option>` tags from key-value pairs
4. Handles empty options gracefully

### Profile Route Pattern
Similar pattern can be used for other role-specific profile pages:
```php
Route::get('/my-profile', function () {
    $user = auth()->user();
    // custom logic per role
    return view('role.profile', compact('user'));
})->name('role.profile');
```

---

## Known Limitations

### Select Dropdowns
- Options are static from seeder (not editable via admin UI)
- To add new options, must update seeder and re-run
- No multi-select support currently

### Admin Profile
- Read-only view (edit goes to user edit page)
- No separate "my profile" edit form
- Uses generic user show template
- Cannot change own role (security feature)

---

## Future Enhancements

### Select Dropdowns
- [ ] Admin UI to manage select options
- [ ] Multi-select support for certain settings
- [ ] Conditional options based on other settings
- [ ] Option groups/categories

### Admin Profile
- [ ] Dedicated profile edit page (inline editing)
- [ ] Password change from profile
- [ ] Two-factor authentication setup
- [ ] Activity log on profile page
- [ ] Customizable dashboard preferences

---

## Troubleshooting

### Dropdowns Still Empty
1. Run seeder: `php artisan db:seed --class=SiteSettingSeeder`
2. Clear cache: `php artisan cache:clear`
3. Check database: Options column should contain valid JSON
4. Verify Livewire is working: `php artisan livewire:publish --config`

### Profile Page Not Loading
1. Check route: `php artisan route:list | grep profile`
2. Verify authentication: User must be logged in
3. Check role: User must have 'admin' role
4. Clear route cache: `php artisan route:clear`

### 403 Error on Profile
- Verify user role is 'admin' (not 'customer' or other)
- Check middleware: Should have 'auth' middleware
- Review role assignment in database

---

## Related Documentation

- [Author Page Layout System](./secondary-menu-author-page-sitemap-enhancements.md)
- [Site Settings Architecture](./site-settings-architecture.md)
- [User Management System](./user-management-system.md)
- [Admin Role Permissions](./role-permissions.md)

---

## Support

For issues:
- Check Laravel logs: `storage/logs/laravel.log`
- Test in tinker: `php artisan tinker` → `\App\Models\SiteSetting::where('type', 'select')->get()`
- Verify JSON: `json_decode($setting->options, true)`
- Check user role: `auth()->user()->role`
