# Image Upload Fix - PHP Ini Limits Implementation

## Issue Reported
User saw error message: "The images.0 field must not be greater than 2048 kilobytes" with hardcoded "Max 2MB per image" text, even though we implemented dynamic PHP ini limits.

## Root Cause
The `ProductForm` component still had a hardcoded validation rule of `'images.*' => 'nullable|image|max:2048'` instead of using the dynamic PHP ini limits from `ImageService`.

## Fix Applied

### 1. **Updated ProductForm Component**

**File**: `app/Livewire/Admin/Product/ProductForm.php`

**Changes**:
- Added `ImageService` import
- Added `$maxUploadSize` and `$maxUploadSizeFormatted` properties
- Updated `rules()` method to use dynamic size from PHP ini:
  ```php
  $maxSize = ImageService::getMaxUploadSize() / 1024;
  'images.*' => "nullable|image|max:{$maxSize}"
  ```
- Added custom validation messages:
  ```php
  'images.*.max' => 'Each image must not exceed ' . ImageService::getMaxUploadSizeFormatted() . '. Your server PHP settings limit uploads to this size.'
  ```
- Initialized properties in `mount()` method

### 2. **ImageUploader Already Fixed**

**File**: `app/Livewire/Admin/Product/ImageUploader.php`

This component was already correctly implemented with:
- Dynamic validation rules
- Custom error messages
- PHP ini size limits

## How It Works Now

### Both Components Now Use Dynamic Limits

**ImageUploader** (for `/admin/products/{id}/images` page):
- Reads PHP ini: `upload_max_filesize`, `post_max_size`, `memory_limit`
- Uses smallest value for validation
- Shows dynamic size limit in UI
- Custom error messages

**ProductForm** (for `/admin/products/create` and `/admin/products/{id}/edit` pages):
- Same dynamic validation as ImageUploader
- Validates images during product creation/edit
- Shows clear error messages with actual PHP ini limits

## Validation Flow

### 1. Frontend (Blade View)
```blade
<p class="text-sm text-gray-500 mb-4">
    Images will be converted to WebP format. 
    Max size: <strong>{{ $maxUploadSizeFormatted }}</strong>
</p>
```

### 2. Backend (Livewire Component)
```php
protected function rules()
{
    $maxSize = ImageService::getMaxUploadSize() / 1024;
    return [
        'images.*' => "nullable|image|max:{$maxSize}",
    ];
}

protected function messages()
{
    return [
        'images.*.max' => 'Each image must not exceed ' . 
            ImageService::getMaxUploadSizeFormatted() . 
            '. Your server PHP settings limit uploads to this size.',
    ];
}
```

## Error Messages

### Before Fix
- "The images.0 field must not be greater than 2048 kilobytes."
- Generic Laravel validation message
- Confusing for users

### After Fix
- "Each image must not exceed 8 MB. Your server PHP settings limit uploads to this size."
- Clear, user-friendly message
- Shows actual server limit
- Explains why the limit exists

## Testing

To test the fix:

1. **Check Current PHP Limits**:
   ```php
   ImageService::getMaxUploadSizeFormatted(); // e.g., "8 MB"
   ```

2. **Upload Large Image**:
   - Go to `/admin/products/create` or `/admin/products/{id}/edit`
   - Try uploading image larger than PHP limit
   - Should see: "Each image must not exceed X MB. Your server PHP settings limit uploads to this size."

3. **Upload Valid Image**:
   - Upload image within PHP limits
   - Should convert to WebP
   - Should compress to 85% quality
   - Should succeed

## Benefits

✅ **Consistent validation** across all product forms
✅ **Clear error messages** with actual limits
✅ **No hardcoded values** - always matches server config
✅ **WebP compression** reduces file size by 30-70%
✅ **User-friendly** - explains why upload failed

## Files Modified

1. `app/Livewire/Admin/Product/ProductForm.php`
   - Added ImageService integration
   - Dynamic validation rules
   - Custom error messages
   - Upload size properties

2. `app/Livewire/Admin/Product/ImageUploader.php`
   - Already had dynamic validation
   - Added custom error messages for consistency

## PHP Ini Configuration

Your current server limits are automatically detected from:
- `upload_max_filesize` (e.g., 8M)
- `post_max_size` (e.g., 10M)  
- `memory_limit` (e.g., 128M)

The system uses the **smallest** of these three values to ensure successful uploads.

To increase limits, update your `php.ini`:
```ini
upload_max_filesize = 16M
post_max_size = 20M
memory_limit = 256M
```

Then restart your web server.

## Summary

✅ Fixed hardcoded 2MB validation in ProductForm
✅ All image uploads now use dynamic PHP ini limits
✅ Clear, helpful error messages
✅ Automatic WebP compression
✅ Consistent behavior across all product forms
