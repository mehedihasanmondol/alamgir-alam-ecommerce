# Blog Post Featured Image - Universal Image Uploader Integration

## Overview
Migrated blog post featured image upload system from direct file upload to the Universal Image Uploader system, matching the implementation pattern used in product categories.

## Implementation Date
November 22, 2024

## Benefits
- ✅ **Unified Media Management**: All blog post images go through centralized media library
- ✅ **Image Library Access**: Select from previously uploaded images for blog posts
- ✅ **Automatic Optimization**: Compression, resizing, and WebP conversion via ImageService
- ✅ **Better Organization**: All media centrally managed in media_library table
- ✅ **Consistent UX**: Same upload experience as categories
- ✅ **Backward Compatibility**: Legacy featured_image field still supported

## Database Changes

### Migration Created
**File**: `database/migrations/2025_11_22_000001_add_media_id_to_blog_posts_table.php`

**Changes**:
- Added `media_id` column (foreign key to `media_library` table)
- Kept `featured_image` field for backward compatibility
- Set `onDelete('set null')` for safe deletion

## Model Updates

### Post Model (`app/Modules/Blog/Models/Post.php`)

**Changes**:
1. Added `use App\Models\Media;` import
2. Added `media_id` to `$fillable` array
3. Added `media()` relationship method:
```php
public function media(): BelongsTo
{
    return $this->belongsTo(Media::class, 'media_id');
}
```

## Service Layer Updates

### PostService (`app/Modules/Blog/Services/PostService.php`)

**Changes**:
1. **createPost()**: 
   - Updated to prefer `media_id` over `featured_image`
   - Loads `media` relationship when returning post
   
2. **updatePost()**:
   - Updated to prefer `media_id` over `featured_image`
   - Loads `media` relationship when returning post
   - Legacy file upload support maintained

## Livewire Component

### PostImageHandler Component

**File**: `app/Livewire/Admin/Blog/PostImageHandler.php`

**Purpose**: Handles image selection/upload events from Universal Image Uploader

**Features**:
- Listens for `imageSelected` and `imageUploaded` events
- Filters events by `targetField` = `post_featured_image`
- Stores `media_id` in component state
- Displays image preview with remove option
- Opens Universal Image Uploader modal on button click

**View**: `resources/views/livewire/admin/blog/post-image-handler.blade.php`

**Key Methods**:
- `mount($post)`: Loads existing media if post has `media_id`
- `handleImageSelected()`: Handles selection from library
- `handleImageUploaded()`: Handles new image upload
- `removeImage()`: Clears selected image

## Admin View Updates

### Create Post (`resources/views/admin/blog/posts/create.blade.php`)

**Changes**:
1. Replaced file input with `@livewire('admin.blog.post-image-handler')`
2. Added Universal Image Uploader component instance:
```blade
@livewire('universal-image-uploader', [
    'multiple' => false,
    'disk' => 'public',
    'maxFileSize' => 5,
    'libraryScope' => 'global',
    'targetField' => 'post_featured_image'
])
```
3. Kept Alt Text field for SEO

### Edit Post (`resources/views/admin/blog/posts/edit.blade.php`)

**Changes**:
1. Replaced file input and preview logic with `@livewire('admin.blog.post-image-handler', ['post' => $post])`
2. Added Universal Image Uploader component instance (same configuration)
3. Removed checkbox for "Remove current image" (handled by component)

## Frontend View Updates

### Post Card Component (`resources/views/components/blog/post-card.blade.php`)

**Changes**:
Updated to support both new and legacy image sources:
1. Check for `$post->media` first (new system)
2. Fall back to `$post->featured_image` (legacy)
3. Prioritize YouTube thumbnail if available

**Image Display Logic**:
```blade
@if($post->youtube_url || $post->media || $post->featured_image)
    @if($post->youtube_url)
        <!-- YouTube thumbnail -->
    @elseif($post->media)
        <img src="{{ $post->media->large_url }}" alt="...">
    @elseif($post->featured_image)
        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="...">
    @endif
@endif
```

## Data Flow

### Creating/Editing Blog Post with Featured Image:

1. **User clicks "Select Featured Image"**
2. **PostImageHandler** dispatches `openMediaLibrary` event
3. **Universal Image Uploader** modal opens showing media library
4. **User selects/uploads** image
5. **UniversalImageUploader** component dispatches `imageSelected`/`imageUploaded`
6. **PostImageHandler** catches event, updates `media_id`
7. **Hidden input** `<input name="media_id">` submitted with form
8. **PostService** saves post with `media_id`
9. **Post model** establishes relationship with Media

### Displaying Featured Image on Frontend:

1. **Post loaded** with eager loading: `$post->load('media')`
2. **Blade template** checks `$post->media` first
3. **If media exists**: Use `$post->media->large_url`
4. **If no media**: Fall back to legacy `$post->featured_image`

## Backward Compatibility

**Legacy Support**:
- Old posts with `featured_image` path still display correctly
- `PostService` maintains file upload logic if `media_id` not provided
- Frontend templates check both `media` and `featured_image`
- Gradual migration possible: admin can update images over time

**Migration Strategy**:
- No data migration required immediately
- Old images continue working via `featured_image` field
- New/updated posts use `media_id` system
- Optional: Create command to migrate old images to media library

## Files Created

1. `database/migrations/2025_11_22_000001_add_media_id_to_blog_posts_table.php`
2. `app/Livewire/Admin/Blog/PostImageHandler.php`
3. `resources/views/livewire/admin/blog/post-image-handler.blade.php`
4. `development-docs/blog-post-universal-image-uploader-integration.md`

## Files Modified

1. `app/Modules/Blog/Models/Post.php` - Added media relationship
2. `app/Modules/Blog/Services/PostService.php` - Updated create/update methods
3. `resources/views/admin/blog/posts/create.blade.php` - Replaced file input
4. `resources/views/admin/blog/posts/edit.blade.php` - Replaced file input
5. `resources/views/components/blog/post-card.blade.php` - Support media relationship
6. `pending-deployment.md` - Added migration command

## Deployment Steps

```bash
# Run migration
php artisan migrate --path=database/migrations/2025_11_22_000001_add_media_id_to_blog_posts_table.php

# Clear caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

## Testing Checklist

### Admin Panel:
- [ ] Create new blog post with featured image
- [ ] Edit existing post and change featured image
- [ ] Select image from library
- [ ] Upload new image
- [ ] Remove featured image
- [ ] Verify media_id is saved in database
- [ ] Check that alt text still works

### Frontend:
- [ ] Display posts with new media system images
- [ ] Display posts with legacy featured_image
- [ ] Verify image optimization (WebP, sizes)
- [ ] Check responsive images
- [ ] Test on blog index page
- [ ] Test on category pages
- [ ] Test on single post view
- [ ] Test on search results

### Edge Cases:
- [ ] Post without any image
- [ ] Post with YouTube URL (image should not show)
- [ ] Post with media deleted from library (should handle gracefully)
- [ ] Mix of old and new image posts on same page

## Database Schema

### Before:
```sql
blog_posts
├── id
├── featured_image (nullable)
└── featured_image_alt (nullable)
```

### After:
```sql
blog_posts
├── id
├── media_id (nullable, foreign key)
├── featured_image (nullable, legacy)
└── featured_image_alt (nullable)
```

## Performance Considerations

- **Eager Loading**: Use `->with('media')` when loading posts
- **Image Sizes**: Media library provides optimized sizes (small, medium, large)
- **Caching**: Media URLs are consistent and cacheable
- **Lazy Loading**: Frontend can use native lazy loading for images

## Future Enhancements

1. **Data Migration Command**: Create artisan command to migrate old featured images to media library
2. **Bulk Update**: Admin interface to bulk update post images
3. **Image Cropping**: Add cropping capabilities during selection
4. **Multiple Images**: Support image galleries for posts
5. **Video Thumbnails**: Auto-generate thumbnails for YouTube videos

## Related Documentation

- [Universal Image Uploader Documentation](./universal-image-uploader.md)
- [CKEditor Integration](./ckeditor-universal-uploader-integration.md)
- [Media Library System](./media-library-system.md)
- [Image Optimization](./image-optimization.md)

## Support

For issues or questions:
- Check console logs for JavaScript errors
- Verify Livewire is working properly
- Ensure media library has images
- Check database for media_id values
- Verify foreign key constraints

## Notes

- Featured image upload now consistent across all admin features
- Image library promotes image reuse across blog posts
- Automatic optimization improves site performance
- Centralized media management simplifies administration
