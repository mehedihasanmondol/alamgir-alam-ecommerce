# Universal Image Uploader - Quick Start Guide

## ✅ Setup Complete!

All components have been created and are ready to use. Follow these steps to activate the system:

## 1. Run Database Migration

```bash
php artisan migrate
```

This creates two tables:
- `media_library` - Stores uploaded images
- `image_upload_settings` - Stores configuration

## 2. Seed Default Settings

```bash
php artisan db:seed --class=ImageUploadSettingSeeder
```

This creates 18 default settings including compression quality, size presets, and limits.

## 3. Build Assets

```bash
npm run build
```

This compiles CropperJS and the image-cropper.js module.

## 4. Clear Caches

```bash
php artisan optimize:clear
```

## 5. Test the Component

Create a test page to verify the component works:

```blade
{{-- resources/views/test-uploader.blade.php --}}
<x-app-layout>
    <div class="container mx-auto p-8">
        <h1 class="text-2xl font-bold mb-4">Image Uploader Test</h1>
        
        <x-image-uploader 
            target-field="test_image"
        />
    </div>
</x-app-layout>
```

Add a test route:
```php
// routes/web.php
Route::get('/test-uploader', function () {
    return view('test-uploader');
})->middleware('auth');
```

Visit: `http://your-domain/test-uploader`

---

## Using with Product Categories

### Step 1: Add Image Column to Categories Table

Create migration:
```bash
php artisan make:migration add_image_id_to_categories_table
```

```php
public function up(): void
{
    Schema::table('categories', function (Blueprint $table) {
        $table->foreignId('media_id')->nullable()->constrained('media_library')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('categories', function (Blueprint $table) {
        $table->dropForeign(['media_id']);
        $table->dropColumn('media_id');
    });
}
```

Run migration:
```bash
php artisan migrate
```

### Step 2: Update Category Model

```php
// app/Modules/Ecommerce/Category/Models/Category.php

use App\Models\Media;

class Category extends Model
{
    protected $fillable = [
        // ... existing fields ...
        'media_id',
    ];
    
    /**
     * Get the category image.
     */
    public function media()
    {
        return $this->belongsTo(Media::class);
    }
    
    /**
     * Get the image URL attribute.
     */
    public function getImageUrlAttribute()
    {
        return $this->media?->large_url ?? asset('images/default-category.jpg');
    }
}
```

### Step 3: Update Category Create/Edit Forms

```blade
{{-- resources/views/admin/categories/create.blade.php --}}

{{-- Add this in the form --}}
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Category Image
    </label>
    
    <div x-data="{ categoryImage: null }">
        <x-image-uploader 
            target-field="category_image"
            library-scope="global"
            @image-updated="categoryImage = $event.detail.media[0].id"
            @image-removed="categoryImage = null"
        />
        
        {{-- Hidden input to store media ID --}}
        <input type="hidden" name="media_id" x-model="categoryImage">
    </div>
</div>
```

For edit form with existing image:
```blade
{{-- resources/views/admin/categories/edit.blade.php --}}

<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Category Image
    </label>
    
    <div x-data="{ categoryImage: {{ $category->media_id ?? 'null' }} }">
        <x-image-uploader 
            target-field="category_image"
            library-scope="global"
            :preview-url="$category->media?->large_url"
            @image-updated="categoryImage = $event.detail.media[0].id"
            @image-removed="categoryImage = null"
        />
        
        <input type="hidden" name="media_id" x-model="categoryImage">
    </div>
</div>
```

### Step 4: Update Category Controller

```php
// app/Modules/Ecommerce/Category/Controllers/CategoryController.php

public function store(Request $request)
{
    $validated = $request->validate([
        // ... existing validations ...
        'media_id' => 'nullable|exists:media_library,id',
    ]);
    
    $category = Category::create($validated);
    
    return redirect()->route('admin.categories.index')
        ->with('success', 'Category created successfully!');
}

public function update(Request $request, Category $category)
{
    $validated = $request->validate([
        // ... existing validations ...
        'media_id' => 'nullable|exists:media_library,id',
    ]);
    
    $category->update($validated);
    
    return redirect()->route('admin.categories.index')
        ->with('success', 'Category updated successfully!');
}
```

### Step 5: Display in Frontend

```blade
{{-- Display category image --}}
<img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-48 object-cover">

{{-- Or use media sizes --}}
<img src="{{ $category->media->small_url }}" alt="{{ $category->name }}"> {{-- Small: 600px --}}
<img src="{{ $category->media->medium_url }}" alt="{{ $category->name }}"> {{-- Medium: 1200px --}}
<img src="{{ $category->media->large_url }}" alt="{{ $category->name }}"> {{-- Large: 1920px --}}
```

---

## Component Attributes Reference

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `multiple` | boolean | `false` | Allow multiple uploads |
| `disk` | string | `'public'` | Storage disk |
| `maxFileSize` | integer | `5` | Max size in MB |
| `maxWidth` | integer | `4000` | Max width in pixels |
| `maxHeight` | integer | `4000` | Max height in pixels |
| `preserveOriginal` | boolean | `false` | Keep original file |
| `defaultCompression` | integer | `70` | Compression (0-100) |
| `libraryScope` | string | `'global'` | user/global |
| `targetField` | string | `null` | Field identifier |
| `previewUrl` | string | `null` | Existing image URL |
| `previewAlt` | string | `'Preview'` | Alt text |

---

## Common Use Cases

### Blog Post Featured Image
```blade
<x-image-uploader 
    target-field="featured_image"
    :preview-url="$post->featured_image_url ?? null"
    library-scope="global"
/>
```

### Brand Logo
```blade
<x-image-uploader 
    target-field="brand_logo"
    :preview-url="$brand->logo_url ?? null"
    :max-file-size="2"
    library-scope="global"
/>
```

### Product Gallery (Multiple)
```blade
<x-image-uploader 
    :multiple="true"
    target-field="product_gallery"
    library-scope="global"
/>
```

### User Profile Picture (User Scope)
```blade
<x-image-uploader 
    target-field="profile_picture"
    :preview-url="auth()->user()->avatar_url ?? null"
    library-scope="user"
    :max-file-size="2"
/>
```

---

## Settings Management

Access settings via admin panel (create admin interface) or programmatically:

```php
use App\Models\ImageUploadSetting;

// Get setting
$compression = ImageUploadSetting::get('default_compression', 70);

// Update setting
ImageUploadSetting::set('default_compression', 80, 'number');

// Get all settings
$settings = ImageUploadSetting::getAllSettings();
```

---

## Troubleshooting

### Images Not Uploading
- Check `php.ini`: `upload_max_filesize` and `post_max_size`
- Verify storage permissions: `chmod -R 775 storage/app/public`
- Check Livewire temp directory: `storage/livewire-tmp`

### Cropper Not Showing
- Rebuild assets: `npm run build`
- Clear browser cache
- Check console for JS errors

### Settings Not Saving
- Run seeder: `php artisan db:seed --class=ImageUploadSettingSeeder`
- Clear cache: `php artisan cache:clear`

### Images Not Optimizing
- Optional: Install optimizer binaries (jpegoptim, optipng, pngquant, svgo, gifsicle)
- Or disable: `ImageUploadSetting::set('enable_optimizer', false, 'boolean')`

---

## Next Features to Implement

1. **Admin Settings Panel** - Create UI for managing ImageUploadSetting
2. **Bulk Operations** - Select multiple images and delete/tag
3. **Image Metadata Editor** - Edit alt text, description, tags
4. **Usage Tracking** - Show where each image is used
5. **Image Variants** - Additional custom size presets
6. **CDN Integration** - Serve images from CDN

---

## Documentation

- **Full Documentation**: `development-docs/universal-image-uploader-documentation.md`
- **API Reference**: See documentation for all methods
- **Examples**: See documentation for more usage examples

---

## Support

For issues or questions:
1. Check the full documentation
2. Verify all setup steps are completed
3. Check browser console for errors
4. Review error logs: `storage/logs/laravel.log`
