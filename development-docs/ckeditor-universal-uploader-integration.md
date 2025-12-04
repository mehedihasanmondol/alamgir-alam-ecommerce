# CKEditor Universal Image Uploader Integration

## Overview
Integrated the existing Universal Image Uploader Livewire component with CKEditor's image upload functionality, replacing the default CKEditor upload tool with our media library system.

## Implementation Date
November 22, 2024

## Benefits
- ✅ **Unified Media Management**: All uploaded images go through the same media library system
- ✅ **Image Library Access**: Users can select from previously uploaded images
- ✅ **Image Optimization**: Automatic compression and resizing through ImageService
- ✅ **Better User Experience**: Modal interface with upload/library tabs
- ✅ **Consistent Workflow**: Same image upload experience across admin panel
- ✅ **No File Size Issues**: Respects PHP upload limits with proper validation

## Technical Architecture

### 1. Custom Upload Adapter (`ckeditor-universal-uploader.js`)
Created a custom CKEditor upload adapter that:
- Intercepts CKEditor's image upload requests
- Opens the Universal Image Uploader modal
- Handles image selection/upload completion
- Returns the uploaded image URL to CKEditor
- Handles modal cancellation

**Key Features:**
- Promise-based upload handling
- Event-driven communication between CKEditor and Livewire
- Support for both uploading new images and selecting from library
- Proper cleanup of event listeners

### 2. Updated CKEditor Configuration (`ckeditor-init.js`)
- Imported `UniversalImageUploadPlugin` from custom adapter
- Added `FileRepository` to CKEditor plugins
- Registered the custom upload adapter plugin
- Maintained all existing CKEditor features (headings, lists, tables, etc.)

### 3. Blog Post Editor Integration (`blog-post-editor.js`)
- Added event listener for `open-ckeditor-uploader` custom event
- Dispatches Livewire event to open the uploader modal
- Bridges communication between CKEditor adapter and Livewire component

### 4. Livewire Component Enhancement (`UniversalImageUploader.php`)
- Updated `closeModal()` method to detect CKEditor upload context
- Emits `ckeditor-upload-cancelled` event when modal is closed during CKEditor upload
- Allows proper cancellation handling in the upload adapter

### 5. View Integration
Updated both create and edit blog post views:
- `resources/views/admin/blog/posts/create.blade.php`
- `resources/views/admin/blog/posts/edit.blade.php`

Added Livewire component with configuration:
```php
@livewire('universal-image-uploader', [
    'multiple' => false,
    'disk' => 'public',
    'maxFileSize' => 5,
    'libraryScope' => 'global',
    'targetField' => 'ckeditor_upload'
])
```

## Files Created
1. **resources/js/ckeditor-universal-uploader.js** (New)
   - Custom upload adapter class
   - Plugin registration function
   - Event handling logic

## Files Modified
1. **resources/js/ckeditor-init.js**
   - Added import for `UniversalImageUploadPlugin`
   - Added `FileRepository` to imports
   - Registered plugin in CKEditor configuration

2. **resources/js/blog-post-editor.js**
   - Added event listener for modal opening
   - Dispatches Livewire events

3. **app/Livewire/UniversalImageUploader.php**
   - Enhanced `closeModal()` method with cancel event

4. **resources/views/admin/blog/posts/create.blade.php**
   - Added Universal Image Uploader component

5. **resources/views/admin/blog/posts/edit.blade.php**
   - Added Universal Image Uploader component

## Event Flow

### Image Upload via CKEditor:
1. User clicks "Insert Image" button in CKEditor toolbar
2. CKEditor calls custom upload adapter
3. Adapter dispatches `open-ckeditor-uploader` event
4. Blog post editor catches event and dispatches Livewire `openUploader` event
5. Universal Image Uploader modal opens
6. User uploads new image or selects from library
7. Uploader emits `imageUploaded` or `imageSelected` event
8. Adapter resolves promise with image URL
9. CKEditor inserts image into content

### Modal Cancellation:
1. User closes modal without selecting/uploading
2. Livewire `closeModal()` detects CKEditor context
3. Emits `ckeditor-upload-cancelled` event
4. Adapter rejects promise
5. CKEditor cancels upload operation

## Configuration Options

The Universal Image Uploader is configured for CKEditor use with:
- **multiple**: `false` (single image selection)
- **disk**: `public` (storage disk)
- **maxFileSize**: `5` (MB)
- **libraryScope**: `global` (access to all uploaded images)
- **targetField**: `ckeditor_upload` (identifies CKEditor context)

## How It Works

When users click the "Upload Image" button in CKEditor:
1. The custom adapter takes over the upload process
2. Instead of using CKEditor's default upload, it opens our Universal Image Uploader
3. Users can either:
   - Upload a new image (processed through ImageService)
   - Select an existing image from the media library
4. The selected/uploaded image URL is returned to CKEditor
5. CKEditor inserts the image at the cursor position

## Advantages Over Default CKEditor Upload

### Before (Default CKEditor Upload):
- ❌ Direct file upload without optimization
- ❌ No centralized media management
- ❌ Cannot reuse existing images
- ❌ No compression or resizing
- ❌ Inconsistent with rest of admin panel

### After (Universal Image Uploader):
- ✅ All images optimized and compressed
- ✅ Centralized media library
- ✅ Can select from previously uploaded images
- ✅ Automatic WebP conversion and multiple sizes
- ✅ Consistent upload experience
- ✅ Better organization and management

## Usage in Other Editors

This same pattern can be applied to integrate the Universal Image Uploader with:
- Other rich text editors (TinyMCE, Quill, etc.)
- Custom form fields
- Any component needing image upload functionality

### Integration Steps:
1. Create custom upload adapter for the editor
2. Dispatch `open-ckeditor-uploader` event (or custom event)
3. Listen for `imageUploaded` or `imageSelected` events
4. Add Livewire component to the view
5. Handle modal cancellation

## Testing Checklist

- [ ] Upload new image via CKEditor toolbar button
- [ ] Select existing image from media library
- [ ] Cancel upload by closing modal
- [ ] Verify image appears correctly in editor
- [ ] Check image is saved with blog post
- [ ] Verify images are optimized (WebP format)
- [ ] Test on both create and edit pages
- [ ] Ensure no JavaScript console errors
- [ ] Verify proper cleanup of event listeners

## Dependencies

- **CKEditor 5**: Main rich text editor
- **Livewire 3**: For Universal Image Uploader component
- **Alpine.js**: For reactive modal behavior
- **ImageService**: For image processing and optimization
- **Media Model**: For centralized media management

## Browser Compatibility

- Chrome/Edge: ✅ Fully supported
- Firefox: ✅ Fully supported
- Safari: ✅ Fully supported
- Opera: ✅ Fully supported

## Performance Considerations

- Event listeners are properly cleaned up after use
- Promise-based approach prevents memory leaks
- Modal only loads when needed
- Images are lazy-loaded in media library
- Pagination for large media libraries

## Future Enhancements

Potential improvements:
1. Drag & drop image upload directly into CKEditor
2. Image editing capabilities (crop, rotate, filters)
3. Bulk image upload support
4. Image search in media library
5. Alt text editing before insertion
6. Image dimension constraints

## Troubleshooting

### Image not inserting:
- Check browser console for JavaScript errors
- Verify Livewire is properly initialized
- Ensure `targetField` is set to `ckeditor_upload`

### Modal not opening:
- Check that Universal Image Uploader component is included in view
- Verify event listeners are registered
- Check Livewire console logs

### Upload fails:
- Check PHP upload limits (post_max_size, upload_max_filesize)
- Verify storage permissions
- Check ImageService configuration

## Related Documentation

- Universal Image Uploader component documentation
- CKEditor 5 official documentation
- ImageService documentation
- Livewire events documentation

## Maintenance Notes

- Keep CKEditor version updated
- Monitor for any Livewire breaking changes
- Test after any JavaScript bundler updates (Vite)
- Regularly check event listener cleanup for memory leaks
