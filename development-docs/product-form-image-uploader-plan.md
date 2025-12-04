# Product Form - Universal Image Uploader Integration Plan

## Overview
Integrate the universal image uploader and media library system into the product add/edit form for both main product images and variant images.

---

## Current Implementation Issues

### Main Product Images
- Uses `wire:model.live="images"` for direct file uploads
- Stores temporary file objects in `$this->images` array
- No ability to select from existing media library
- No drag-and-drop ordering
- Manual file processing in ProductService

### Variant Images
- Variants currently don't have proper image management
- Need to support media library for variant images
- Should allow selecting existing images or uploading new ones

---

## Proposed Solution

### Phase 1: Main Product Images ✅

#### 1.1 Update Product Form View
**File**: `resources/views/livewire/admin/product/product-form-enhanced.blade.php`

**Changes**:
- Replace file input with universal image uploader component
- Add "Select from Library" button
- Add "Upload New" button
- Display selected images with:
  - Thumbnail preview
  - Primary image indicator
  - Remove button
  - Drag handles for ordering
- Show image count and size info

**UI Layout**:
```html
<div class="space-y-4">
    <!-- Action Buttons -->
    <div class="flex gap-3">
        <button @click="$dispatch('openMediaLibrary', { field: 'product_images' })">
            Select from Library
        </button>
        <button @click="$dispatch('openUploader', { field: 'product_images' })">
            Upload New
        </button>
    </div>
    
    <!-- Selected Images Grid (with Sortable.js) -->
    <div id="product-images-grid" class="grid grid-cols-4 gap-4">
        @foreach($selectedImages as $index => $image)
            <!-- Image card with drag handle, primary badge, remove -->
        @endforeach
    </div>
</div>

<!-- Universal Image Uploader Component -->
<livewire:universal-image-uploader 
    :targetField="'product_images'"
    :allowMultiple="true"
    :maxFiles="10"
/>
```

#### 1.2 Update Product Form Livewire Component
**File**: `app/Livewire/Admin/Product/ProductForm.php`

**Changes**:
- Remove `$images` property (temporary files)
- Add `$selectedImages` property (array of media IDs with metadata)
- Add `$primaryImageIndex` property
- Add listeners for image uploader events:
  - `imageSelected` - Handle media library selection
  - `imageUploaded` - Handle new uploads
- Add methods:
  - `addImages($mediaIds)` - Add images from library/upload
  - `removeImage($index)` - Remove image
  - `setPrimaryImage($index)` - Set primary
  - `reorderImages($newOrder)` - Handle drag-drop ordering

**Data Structure**:
```php
public $selectedImages = [
    [
        'media_id' => 123,
        'is_primary' => true,
        'sort_order' => 0,
        'url' => 'https://...',
        'thumbnail_url' => 'https://...',
    ],
    // ...
];
```

#### 1.3 Update ProductService
**File**: `app/Modules/Ecommerce/Product/Services/ProductService.php`

**Methods to Update**:
- `createImages($product, $data)` - Save media_id instead of uploading files
- `updateImages($product, $data)` - Update with media_id references
- `attachImagesToProduct($product, $images)` - New method for media_id

**Logic**:
```php
// OLD: Upload files and save paths
foreach ($images as $file) {
    $path = $file->store('products');
    ProductImage::create(['product_id' => $product->id, 'image_path' => $path]);
}

// NEW: Save media_id references
foreach ($selectedImages as $imageData) {
    ProductImage::create([
        'product_id' => $product->id,
        'media_id' => $imageData['media_id'],
        'is_primary' => $imageData['is_primary'],
        'sort_order' => $imageData['sort_order'],
    ]);
}
```

---

### Phase 2: Variant Images ✅

#### 2.1 Variant Management Component
**File**: New component or integrate into existing variation manager

**Features**:
- Each variant gets an image selector
- Can select from media library or upload new
- Shows thumbnail preview
- Remove/change image option

**UI for Each Variant**:
```html
<div class="variant-image-section">
    <label>Variant Image</label>
    
    @if($variant['image_media_id'])
        <!-- Show current image -->
        <img src="{{ $variant['image_thumbnail_url'] }}" />
        <button @click="removeVariantImage({{ $index }})">Remove</button>
    @else
        <!-- Image selector -->
        <button @click="selectVariantImage({{ $index }})">
            Select Image
        </button>
    @endif
</div>
```

#### 2.2 Update ProductVariant Handling
**In ProductService**:
- Save `media_id` for each variant
- Handle variant image updates
- Support removing variant images

---

### Phase 3: Image Ordering (Sortable.js) ✅

#### 3.1 Add Sortable.js
**Install**:
```bash
npm install sortablejs
```

**Import in resources/js/app.js**:
```javascript
import Sortable from 'sortablejs';

// Initialize on product images grid
document.addEventListener('livewire:navigated', () => {
    const grid = document.getElementById('product-images-grid');
    if (grid) {
        Sortable.create(grid, {
            animation: 150,
            handle: '.drag-handle',
            onEnd: function(evt) {
                // Dispatch event to Livewire with new order
                Livewire.dispatch('reorderImages', {
                    oldIndex: evt.oldIndex,
                    newIndex: evt.newIndex
                });
            }
        });
    }
});
```

#### 3.2 Update Component to Handle Ordering
```php
#[On('reorderImages')]
public function reorderImages($oldIndex, $newIndex)
{
    $image = array_splice($this->selectedImages, $oldIndex, 1)[0];
    array_splice($this->selectedImages, $newIndex, 0, [$image]);
    
    // Update sort_order for all images
    foreach ($this->selectedImages as $index => $image) {
        $this->selectedImages[$index]['sort_order'] = $index;
    }
}
```

---

## Implementation Steps

### Step 1: Prepare Livewire Component ✅
1. Update `ProductForm.php`:
   - Add `$selectedImages` property
   - Add image management methods
   - Add event listeners
   - Update validation rules

### Step 2: Update Form View ✅
1. Update `product-form-enhanced.blade.php`:
   - Replace file input with universal uploader integration
   - Add media library selection button
   - Add selected images grid with:
     - Drag handles
     - Primary badge
     - Remove buttons
   - Include universal-image-uploader component

### Step 3: Update ProductService ✅
1. Modify image saving logic to use `media_id`
2. Update `createImages()` method
3. Update `updateImages()` method
4. Handle primary image designation
5. Handle sort ordering

### Step 4: Add JavaScript for Sortable ✅
1. Install sortablejs
2. Initialize on images grid
3. Dispatch Livewire events on reorder

### Step 5: Variant Images ✅
1. Add variant image selector UI
2. Update variant save logic for `media_id`
3. Test variant image display

### Step 6: Testing ✅
1. Test adding new product with images
2. Test editing product images
3. Test primary image designation
4. Test image ordering
5. Test variant images
6. Test image removal
7. Test media library selection

---

## Database Schema (Already Done)

- ✅ `product_images.media_id` - Foreign key to media_library
- ✅ `product_variants.media_id` - Foreign key to media_library
- ✅ Models updated with relationships and helper methods

---

## Backward Compatibility

All changes maintain backward compatibility:
- Helper methods fall back to old `image_path` fields
- Existing products continue to work
- No data migration required (optional)

---

## Benefits After Implementation

1. **Centralized Media Library**: All images in one place
2. **Reusable Images**: Select from existing images across products
3. **Better Organization**: Tag, search, filter images
4. **Image Ordering**: Drag-drop to reorder product images
5. **Primary Image**: Clear designation for main product image
6. **Variant Images**: Proper image support for product variants
7. **No Duplicate Uploads**: Reuse existing images
8. **CDN Ready**: Easy to serve images from CDN

---

## Estimated Time

- **Phase 1** (Main Product Images): 3-4 hours
- **Phase 2** (Variant Images): 2-3 hours
- **Phase 3** (Ordering with Sortable.js): 1-2 hours
- **Testing & Refinement**: 1-2 hours

**Total**: 7-11 hours of focused development

---

## Priority

**HIGH** - This is the most important remaining piece of the media library migration. Product add/edit is used daily by admins and needs the full power of the media library system.

---

## Next Actions

1. Create backup of current form
2. Implement Phase 1 (main product images)
3. Test thoroughly
4. Implement Phase 2 (variant images)
5. Add Sortable.js for ordering
6. Final testing
7. Deploy

