# Universal Image Uploader - Complete Documentation

## Overview
A comprehensive, reusable image uploader component for Laravel + Livewire + Tailwind CSS with WebP compression, client-side cropping, and a three-tab modal UI (Library, Upload, Settings).

## Features
- ✅ Three-tab modal interface (Library, Upload, Settings)
- ✅ Client-side image cropping with CropperJS
- ✅ Aggressive WebP compression with configurable quality
- ✅ Multi-size generation (Large, Medium, Small)
- ✅ Drag & drop file upload
- ✅ Image library with search, filters, and pagination
- ✅ Preview after upload with replace/remove options
- ✅ Configurable settings stored in database
- ✅ Aspect ratio presets (Free, Square, 16:9, 9:16, etc.)
- ✅ Estimated file size preview
- ✅ Image optimization with Spatie Image Optimizer
- ✅ Mobile responsive design
- ✅ Keyboard accessible (Esc to close)
- ✅ Multiple file upload support

## Installation Steps Completed

### 1. Packages Installed
```bash
composer require spatie/image-optimizer
npm install cropperjs
```

### 2. Database Migration Created
- **File**: `database/migrations/2024_11_21_000001_create_media_library_table.php`
- **Tables**: 
  - `media_library` - Stores uploaded images with metadata
  - `image_upload_settings` - Stores component settings

**Run Migration**:
```bash
php artisan migrate
```

### 3. Models Created
- `app/Models/Media.php` - Media library model
- `app/Models/ImageUploadSetting.php` - Settings model with cache support

### 4. Seeder Created
- **File**: `database/seeders/ImageUploadSettingSeeder.php`

**Run Seeder**:
```bash
php artisan db:seed --class=ImageUploadSettingSeeder
```

### 5. ImageService Enhanced
- **File**: `app/Services/ImageService.php`
- **New Methods**:
  - `processUniversalUpload()` - Main upload processing
  - `processImageSize()` - Generate size variants
  - `base64ToUploadedFile()` - Handle cropped images
  - `validateUpload()` - Validation logic
  - `optimizeImage()` - Spatie optimizer integration

### 6. Livewire Component Created
- **File**: `app/Livewire/UniversalImageUploader.php`
- **Features**:
  - Library tab with search, filters, pagination
  - Upload tab with file management
  - Settings tab with configuration
  - Event dispatching for parent components

### 7. Blade Views Created
- `resources/views/livewire/universal-image-uploader.blade.php` - Main modal view
- `resources/views/components/image-uploader.blade.php` - Reusable wrapper component
- `resources/views/components/cropper-modal.blade.php` - Cropper modal component

### 8. JavaScript Integration
- `resources/js/app.js` - CropperJS imports
- `resources/js/image-cropper.js` - Cropper functionality and Alpine.js components
- `resources/css/app.css` - CropperJS CSS import

## Usage Examples

### Basic Usage

```blade
{{-- Simple single image uploader --}}
<x-image-uploader 
    target-field="category_image"
/>
```

### Multiple Images

```blade
{{-- Multiple image uploader --}}
<x-image-uploader 
    :multiple="true"
    target-field="product_images"
/>
```

### With Preview

```blade
{{-- Uploader with existing image preview --}}
<x-image-uploader 
    target-field="featured_image"
    :preview-url="$product->image_url ?? null"
    preview-alt="Product Image"
/>
```

### Custom Configuration

```blade
{{-- Fully customized uploader --}}
<x-image-uploader 
    :multiple="false"
    disk="s3"
    :max-file-size="10"
    :max-width="5000"
    :max-height="5000"
    :preserve-original="true"
    :default-compression="80"
    library-scope="user"
    target-field="banner_image"
    :preview-url="$banner->image_url ?? null"
/>
```

## Component Attributes

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `multiple` | boolean | `false` | Allow multiple file uploads |
| `disk` | string | `'public'` | Storage disk (local, public, s3) |
| `maxFileSize` | integer | `5` | Max file size in MB |
| `maxWidth` | integer | `4000` | Max image width in pixels |
| `maxHeight` | integer | `4000` | Max image height in pixels |
| `preserveOriginal` | boolean | `false` | Keep original uploaded file |
| `defaultCompression` | integer | `70` | Default WebP compression (0-100) |
| `libraryScope` | string | `'global'` | Library scope (user, global) |
| `targetField` | string | `null` | Field name for parent component |
| `previewUrl` | string | `null` | Existing image URL for preview |
| `previewAlt` | string | `'Preview'` | Alt text for preview image |

## Event Handling

### Listen for Image Upload

```blade
<div x-data="{ imageUrl: null }">
    <x-image-uploader 
        target-field="product_image"
        @image-updated="imageUrl = $event.detail.media[0].large_url"
    />
    
    <div x-show="imageUrl">
        <p>Selected: <span x-text="imageUrl"></span></p>
    </div>
</div>
```

### Listen for Image Removal

```blade
<div x-data="{ hasImage: true }">
    <x-image-uploader 
        :preview-url="$image"
        @image-removed="hasImage = false"
    />
</div>
```

### Livewire Integration

```php
// In your Livewire component
class ProductForm extends Component
{
    public $productImage;
    
    protected $listeners = ['imageUploaded' => 'handleImageUpload'];
    
    public function handleImageUpload($data)
    {
        if (!empty($data['media'])) {
            $this->productImage = $data['media'][0]['id'];
        }
    }
}
```

## Settings Management

### Default Settings (from Seeder)

| Setting | Default | Description |
|---------|---------|-------------|
| `default_compression` | 70 | WebP compression quality |
| `size_large_width` | 1920 | Large variant width |
| `size_large_height` | 1920 | Large variant height |
| `size_medium_width` | 1200 | Medium variant width |
| `size_medium_height` | 1200 | Medium variant height |
| `size_small_width` | 600 | Small variant width |
| `size_small_height` | 600 | Small variant height |
| `max_file_size` | 5 | Max file size in MB |
| `max_width` | 4000 | Max image width |
| `max_height` | 4000 | Max image height |
| `enable_optimizer` | true | Enable Spatie optimizer |

### Programmatic Settings Access

```php
use App\Models\ImageUploadSetting;

// Get a setting
$compression = ImageUploadSetting::get('default_compression', 70);

// Set a setting
ImageUploadSetting::set('default_compression', 80, 'number');

// Get all settings
$allSettings = ImageUploadSetting::getAllSettings();
```

## Database Schema

### media_library Table

```sql
- id (bigint, primary)
- user_id (bigint, nullable, foreign)
- original_filename (varchar)
- filename (varchar)
- mime_type (varchar)
- extension (varchar)
- size (int) - bytes
- width (int)
- height (int)
- aspect_ratio (decimal)
- disk (varchar)
- path (varchar)
- large_path (varchar)
- medium_path (varchar)
- small_path (varchar)
- metadata (json)
- alt_text (varchar, nullable)
- description (text, nullable)
- tags (json, nullable)
- scope (enum: user, global)
- created_at, updated_at
```

### Accessing Media

```php
use App\Models\Media;

// Get media by ID
$media = Media::find(1);

// URLs
$media->url;        // Original
$media->large_url;  // Large variant
$media->medium_url; // Medium variant
$media->small_url;  // Small variant

// Metadata
$media->formatted_size;  // "1.5 MB"
$media->width;           // 1920
$media->height;          // 1080
$media->aspect_ratio;    // 1.7778

// Delete with files
$media->deleteWithFiles();
```

## Image Processing Flow

1. **User selects/drops files**
2. **Files displayed in Upload tab**
3. **User clicks "Edit & Crop"**
4. **Cropper modal opens with**:
   - Aspect ratio presets
   - Compression quality slider
   - Transform controls (rotate, flip, zoom)
   - Estimated file size
5. **User applies crop**
6. **Cropped image saved as base64**
7. **User clicks "Upload"**
8. **Server processing**:
   - Validate file size, dimensions, MIME type
   - Convert to WebP
   - Generate 3 size variants (Large, Medium, Small)
   - Run Spatie optimizer (if enabled)
   - Store in organized folders: `images/{year}/{month}/`
   - Save metadata to database
9. **Event dispatched to parent**
10. **Preview updated in trigger component**

## File Naming Convention

Generated files follow this pattern:
```
{slug}_{uniqid}_{timestamp}.webp
```

Example:
```
my-product-image_673e9f1234567_1732186758.webp
```

Size variants use prefixes:
- `l__` - Large
- `m__` - Medium
- `s__` - Small

## Storage Structure

```
storage/app/public/images/
├── 2024/
│   ├── 11/
│   │   ├── l__product-image_123_456.webp  (Large)
│   │   ├── m__product-image_123_456.webp  (Medium)
│   │   └── s__product-image_123_456.webp  (Small)
│   └── 12/
│       └── ...
└── 2025/
    └── ...
```

## Security Features

- ✅ CSRF protection (Livewire)
- ✅ File type validation (MIME)
- ✅ File size validation
- ✅ Dimension validation
- ✅ Filename sanitization
- ✅ User-based permissions (library scope)
- ✅ Auth check for delete operations

## Performance Optimizations

- ✅ Settings cached (1 hour TTL)
- ✅ Lazy loading with pagination
- ✅ Client-side validation before upload
- ✅ Aggressive WebP compression
- ✅ Post-processing optimization (Spatie)
- ✅ Multi-size generation for responsive images
- ✅ Organized folder structure (year/month)

## Accessibility Features

- ✅ Keyboard navigation (Tab, Enter, Esc)
- ✅ ARIA attributes
- ✅ Focus trap in modals
- ✅ Proper labels and alt text
- ✅ Screen reader friendly

## Browser Compatibility

- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers
- ⚠️ IE11 not supported (uses modern JS)

## Troubleshooting

### Images Not Uploading
- Check PHP `upload_max_filesize` and `post_max_size`
- Verify storage permissions
- Check Livewire temp storage configuration

### Cropper Not Working
- Ensure CropperJS is imported: `npm run build`
- Check browser console for JS errors
- Verify Alpine.js is loaded

### Settings Not Saving
- Run seeder: `php artisan db:seed --class=ImageUploadSettingSeeder`
- Clear cache: `php artisan cache:clear`

### Optimizer Failing
- Install optimizer binaries (optional)
- Disable optimizer in settings if not needed

## Next Steps

To use this component in product categories:

1. **Run migrations and seeder**:
```bash
php artisan migrate
php artisan db:seed --class=ImageUploadSettingSeeder
```

2. **Build assets**:
```bash
npm run build
```

3. **Add to category form**:
```blade
<x-image-uploader 
    target-field="category_image"
    :preview-url="$category->image_url ?? null"
    library-scope="global"
/>
```

4. **Handle upload in controller**:
```php
use App\Models\Media;

public function store(Request $request)
{
    $category = Category::create([
        'name' => $request->name,
        'image_id' => $request->category_image, // Media ID
    ]);
}
```

## Support

For issues or questions, refer to:
- CropperJS docs: https://github.com/fengyuanchen/cropperjs
- Intervention Image: https://image.intervention.io/
- Spatie Image Optimizer: https://github.com/spatie/image-optimizer
