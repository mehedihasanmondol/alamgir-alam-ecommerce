# User & Author Profile Media Library Integration

## Date: November 22, 2024

## Overview
Integrated media library for user avatars and author profile images, replacing traditional file uploads with the universal media library system. This provides optimized images, centralized management, and consistent UX across the platform.

---

## Changes Made

### 1. **Database Migrations** ✅

#### Users Table:
**File**: `database/migrations/2025_11_22_000002_add_media_id_to_users_table.php`
- Added `media_id` column (nullable, unsignedBigInteger)
- Foreign key to `media_library` table
- Index on `media_id`

#### Author Profiles Table:
**File**: `database/migrations/2025_11_22_000003_add_media_id_to_author_profiles_table.php`
- Added `media_id` column (nullable, unsignedBigInteger)
- Foreign key to `media_library` table
- Index on `media_id`

**Run Migrations**:
```bash
php artisan migrate --path=database/migrations/2025_11_22_000002_add_media_id_to_users_table.php
php artisan migrate --path=database/migrations/2025_11_22_000003_add_media_id_to_author_profiles_table.php
```

---

### 2. **Model Updates** ✅

#### User Model:
**File**: `app/Models/User.php`
- Added `media_id` to `$fillable` array
- Added `media()` relationship (BelongsTo Media)

```php
public function media(): BelongsTo
{
    return $this->belongsTo(\App\Models\Media::class, 'media_id');
}
```

#### AuthorProfile Model:
**File**: `app/Models/AuthorProfile.php`
- Added `media_id` to `$fillable` array
- Added `media()` relationship (BelongsTo Media)

```php
public function media(): BelongsTo
{
    return $this->belongsTo(\App\Models\Media::class, 'media_id');
}
```

---

### 3. **Livewire Components** ✅

#### User Avatar Handler:
**Files**:
- `app/Livewire/Admin/User/UserAvatarHandler.php`
- `resources/views/livewire/admin/user/user-avatar-handler.blade.php`

**Features**:
- Alpine.js entangle for `media_id`
- Two buttons: "Select from Library" & "Upload New"
- Image preview with thumbnail
- Remove button
- Auto-syncs with form

#### Author Avatar Handler:
**Files**:
- `app/Livewire/Admin/User/AuthorAvatarHandler.php`
- `resources/views/livewire/admin/user/author-avatar-handler.blade.php`

**Features**:
- Same as User Avatar Handler
- Different field name: `author_media_id`
- Orange color scheme (vs blue for user)

---

### 4. **Views Requiring Updates**

#### Admin Views:
1. ✅ **User Edit Page**: `resources/views/admin/users/edit.blade.php`
   - Replace user avatar file upload with `<livewire:admin.user.user-avatar-handler :user="$user" />`
   - Replace author avatar file upload with `<livewire:admin.user.author-avatar-handler :authorProfile="$user->authorProfile" />`
   
2. ⏳ **User Create Page**: `resources/views/admin/users/create.blade.php`
   - Add Livewire components for avatars
   
3. ⏳ **User Show Page**: `resources/views/admin/users/show.blade.php`
   - Update avatar display to check `media` first, then `avatar`
   
4. ⏳ **User Index/List**: `resources/views/admin/users/index.blade.php` or `resources/views/livewire/admin/user/user-list.blade.php`
   - Update avatar display

#### Frontend Views:
5. ⏳ **Blog Post Show**: `resources/views/frontend/blog/show.blade.php`
   - Update author avatar display
   
6. ⏳ **Blog Author Page**: `resources/views/frontend/blog/author.blade.php`
   - Update author avatar display
   
7. ⏳ **Customer Profile**: `resources/views/customer/profile/index.blade.php`
   - Update user avatar display
   
8. ⏳ **Blog Comment Section**: `resources/views/livewire/blog/comment-section.blade.php`
   - Update commenter avatar display
   
9. ⏳ **Header/Navigation**: `resources/views/components/frontend/header.blade.php` & `resources/views/layouts/customer.blade.php`
   - Update logged-in user avatar display
   
10. ⏳ **Admin Layout**: `resources/views/layouts/admin.blade.php`
    - Update admin user avatar in sidebar/header

---

### 5. **Controller Updates Needed**

#### UserController:
**File**: `app/Http/Controllers/Admin/UserController.php` (or appropriate location)

**store() method**:
```php
$user = User::create([
    // ... other fields
    'media_id' => $request->media_id,
    // Keep avatar for backward compatibility
]);

// Handle author profile
if ($request->has('author_bio')) {
    $user->authorProfile()->create([
        // ... other fields
        'media_id' => $request->author_media_id,
        'avatar' => null, // Legacy field
    ]);
}
```

**update() method**:
```php
$user->update([
    // ... other fields
    'media_id' => $request->media_id,
]);

// Update author profile
if ($request->has('author_bio')) {
    $user->authorProfile()->updateOrCreate(
        ['user_id' => $user->id],
        [
            // ... other fields
            'media_id' => $request->author_media_id,
        ]
    );
}
```

---

### 6. **Display Logic Pattern**

For all views displaying avatars, use this pattern:

#### User Avatar:
```blade
@if($user->media)
    <img src="{{ $user->media->small_url }}" alt="{{ $user->name }}">
@elseif($user->avatar)
    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
@else
    <!-- Default avatar placeholder -->
    <div class="rounded-full bg-gray-200">
        <svg>...</svg>
    </div>
@endif
```

#### Author Profile Avatar:
```blade
@if($authorProfile->media)
    <img src="{{ $authorProfile->media->small_url }}" alt="{{ $authorProfile->user->name }}">
@elseif($authorProfile->avatar)
    <img src="{{ Storage::url($authorProfile->avatar) }}" alt="{{ $authorProfile->user->name }}">
@elseif($authorProfile->user->media)
    <img src="{{ $authorProfile->user->media->small_url }}" alt="{{ $authorProfile->user->name }}">
@elseif($authorProfile->user->avatar)
    <img src="{{ Storage::url($authorProfile->user->avatar) }}" alt="{{ $authorProfile->user->name }}">
@else
    <!-- Default avatar placeholder -->
@endif
```

---

## Benefits

1. **Optimized Images**: Automatic image optimization and multiple sizes
2. **Centralized Management**: All images in one media library
3. **Consistent UX**: Same upload experience as categories and blog posts
4. **Better Performance**: Serve optimized image sizes
5. **Backward Compatible**: Legacy `avatar` fields still work
6. **Easy Cropping**: Built-in image cropping tool

---

## Migration Steps

### Step 1: Run Migrations
```bash
php artisan migrate --path=database/migrations/2025_11_22_000002_add_media_id_to_users_table.php
php artisan migrate --path=database/migrations/2025_11_22_000003_add_media_id_to_author_profiles_table.php
```

### Step 2: Update Controllers
- Add `media_id` and `author_media_id` handling in UserController
- Validation rules should allow nullable `media_id`

### Step 3: Update Views
- Replace all avatar file uploads with Livewire components
- Update all avatar displays to check `media` first

### Step 4: Add Universal Image Uploader
Make sure each form includes:
```blade
<!-- Universal Image Uploader -->
<livewire:universal-image-uploader />
```

### Step 5: Test
- Create new user with avatar from media library
- Edit existing user and change avatar
- Verify avatars display correctly in all locations
- Test author profile images separately

---

## Files Status

### Created:
1. ✅ `database/migrations/2025_11_22_000002_add_media_id_to_users_table.php`
2. ✅ `database/migrations/2025_11_22_000003_add_media_id_to_author_profiles_table.php`
3. ✅ `app/Livewire/Admin/User/UserAvatarHandler.php`
4. ✅ `app/Livewire/Admin/User/AuthorAvatarHandler.php`
5. ✅ `resources/views/livewire/admin/user/user-avatar-handler.blade.php`
6. ✅ `resources/views/livewire/admin/user/author-avatar-handler.blade.php`

### Modified:
7. ✅ `app/Models/User.php` - Added media_id and media() relationship
8. ✅ `app/Models/AuthorProfile.php` - Added media_id and media() relationship
9. ✅ `resources/views/admin/users/edit.blade.php` - Replaced file uploads with Livewire components
10. ✅ `app/Modules/User/Requests/UpdateUserRequest.php` - Added media_id and author_media_id validation
11. ⏳ `resources/views/admin/users/create.blade.php` - Need to add Livewire components
12. ⏳ Multiple frontend views - Need to update avatar display logic (26 files)

---

## Search & Replace Pattern

To find all places where avatars are displayed:

### Search for:
- `Storage::url($user->avatar)`
- `$user->avatar`
- `$authorProfile->avatar`
- `asset('storage/' . $user->avatar)`
- `{{ $user->avatar }}`

### Files to Update (26 files found with "avatar"):
1. `resources/views/admin/users/edit.blade.php` (30 matches)
2. `resources/views/admin/users/create.blade.php` (24 matches)
3. `resources/views/customer/profile/index.blade.php` (12 matches)
4. `resources/views/frontend/blog/show.blade.php` (6 matches)
5. `resources/views/layouts/customer.blade.php` (6 matches)
6. `resources/views/frontend/blog/author.blade.php` (5 matches)
7. `resources/views/admin/dashboard/index.blade.php` (4 matches)
8. `resources/views/admin/blog/posts/index.blade.php` (2 matches)
9. `resources/views/admin/users/show.blade.php` (2 matches)
10. `resources/views/components/frontend/header.blade.php` (2 matches)
11. `resources/views/layouts/admin.blade.php` (2 matches)
12. `resources/views/livewire/admin/blog/post-list.blade.php` (2 matches)
13. `resources/views/livewire/admin/global-user-search.blade.php` (2 matches)
14. `resources/views/livewire/admin/user/user-list.blade.php` (2 matches)
15. `resources/views/livewire/blog/comment-section.blade.php` (2 matches)
16. `resources/views/livewire/mobile-menu.blade.php` (2 matches)
17. `resources/views/livewire/user/user-search.blade.php` (2 matches)

---

## Testing Checklist

### User Avatar:
- [ ] Create user with media library avatar
- [ ] Edit user and change avatar
- [ ] Remove avatar
- [ ] Upload new avatar
- [ ] Avatar displays in admin user list
- [ ] Avatar displays in admin dashboard
- [ ] Avatar displays in frontend header (logged in)
- [ ] Avatar displays in customer profile
- [ ] Avatar displays in order history
- [ ] Avatar displays in comments

### Author Profile Avatar:
- [ ] Create author profile with media library avatar
- [ ] Edit author profile and change avatar
- [ ] Remove author avatar
- [ ] Avatar displays on blog post view
- [ ] Avatar displays on author archive page
- [ ] Avatar displays in featured authors section
- [ ] Falls back to user avatar if author avatar not set
- [ ] Falls back to placeholder if no avatars

### Backward Compatibility:
- [ ] Existing users with old avatar field still display correctly
- [ ] Can update from old avatar to media library avatar
- [ ] Legacy avatar field not broken

---

## Notes

- **Backward Compatible**: Keep both `avatar` and `media_id` columns
- **Priority**: Always check `media` relationship first, then fall back to `avatar` field
- **Image Sizes**: Use `small_url` for thumbnails, `medium_url` for cards, `large_url` for full display
- **No Build Required**: User is on dev mode with `npm run dev` running

---

## Related Documentation

- `blog-post-universal-image-uploader-integration.md`
- `product-ckeditor-integration.md`
- `blog-post-image-upload-fixes.md`
