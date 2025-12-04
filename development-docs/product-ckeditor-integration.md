# Product CKEditor Integration

## Date: November 22, 2024

## Overview
Replaced TinyMCE with CKEditor 5 for product descriptions on add/edit pages, matching the blog post implementation. Added minimal CKEditor for short descriptions with basic formatting only.

---

## Changes Made

### 1. **Created Product Editor JavaScript** ✅
**File**: `resources/js/product-editor.js`

**Features**:
- Full CKEditor for `description` field (same as blog posts)
- Minimal CKEditor for `short_description` field (limited toolbar)
- Syncs with Livewire `wire:model` via hidden inputs
- Handles universal image uploader integration
- Supports Livewire navigation events

**Editor Configurations**:

#### Full Editor (Description):
- All features from blog posts
- Headings, formatting, lists, tables
- Images via universal uploader
- Code blocks, media embeds
- Source editing, fullscreen
- Word counter

#### Minimal Editor (Short Description):
- Basic formatting only: bold, italic, underline
- Bulleted and numbered lists
- Links with external tab option
- Remove formatting
- No images, tables, or advanced features

---

### 2. **Updated Product Forms** ✅

#### Create Page:
**File**: `resources/views/admin/product/create-livewire.blade.php`
- Replaced TinyMCE with CKEditor styles
- Added Vite import for `product-editor.js`
- Updated scripts to detect CKEditor elements
- Added full Tailwind typography styles

#### Edit Page:
**File**: `resources/views/admin/product/edit-livewire.blade.php`
- Same changes as create page
- Maintains Livewire hook compatibility

#### Livewire Component:
**File**: `resources/views/livewire/admin/product/product-form-enhanced.blade.php`
- Updated description field to use CKEditor with word counter
- Updated short description to use minimal CKEditor
- Added hidden inputs for Livewire sync
- Updated labels and help text

---

## Implementation Details

### Description Field (Full CKEditor):
```blade
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Description *</label>
    <div wire:ignore>
        <textarea id="product-description-editor" 
                  class="ckeditor-content">{{ $description }}</textarea>
    </div>
    <input type="hidden" wire:model="description" id="product-description-hidden">
    
    <!-- Word Counter -->
    <div class="char-counter" id="description-word-count"></div>
    
    @error('description') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
</div>
```

### Short Description Field (Minimal CKEditor):
```blade
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Short Description</label>
    <div wire:ignore>
        <textarea id="product-short-description-editor" 
                  class="ckeditor-content-minimal">{{ $short_description }}</textarea>
    </div>
    <input type="hidden" wire:model="short_description" id="product-short-description-hidden">
    <p class="text-xs text-gray-500 mt-1">Brief summary with basic formatting (max 500 characters). Appears on product cards.</p>
    @error('short_description') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
</div>
```

---

## Features Comparison

| Feature | Blog Posts | Product Description | Product Short Description |
|---------|-----------|---------------------|--------------------------|
| Headings | ✅ H1-H4 | ✅ H1-H4 | ❌ |
| Bold/Italic/Underline | ✅ | ✅ | ✅ |
| Lists | ✅ Numbered & Bulleted | ✅ Numbered & Bulleted | ✅ Simple |
| Images | ✅ Via Universal Uploader | ✅ Via Universal Uploader | ❌ |
| Tables | ✅ Full Styling | ✅ Full Styling | ❌ |
| Code Blocks | ✅ Multiple Languages | ✅ Multiple Languages | ❌ |
| Media Embeds | ✅ YouTube/Vimeo | ✅ YouTube/Vimeo | ❌ |
| Alignment | ✅ | ✅ | ❌ |
| Font Options | ✅ Size, Family, Color | ✅ Size, Family, Color | ❌ |
| Links | ✅ With Decorators | ✅ With Decorators | ✅ Basic |
| Fullscreen | ✅ | ✅ | ❌ |
| Source Editing | ✅ | ✅ | ❌ |
| Find & Replace | ✅ | ✅ | ❌ |
| Word Counter | ✅ | ✅ | ❌ |

---

## Universal Image Uploader Integration

Both blog posts and products use the **same Universal Image Uploader** instance.

**How It Works**:
1. User clicks "Insert Image" in CKEditor
2. Custom plugin dispatches `open-ckeditor-uploader` event
3. Event listener in `product-editor.js` catches event
4. Livewire modal opens to media library
5. User selects/uploads image
6. Image inserted into CKEditor at cursor position

**Already Included**: 
```blade
{{-- Universal Image Uploader Component --}}
<livewire:universal-image-uploader />
```

---

## Styling

All CKEditor content uses **Tailwind Typography** classes:

```css
.ck-content h1 { @apply text-4xl font-extrabold text-gray-900; }
.ck-content h2 { @apply text-3xl font-bold text-gray-800; }
.ck-content h3 { @apply text-2xl font-semibold text-gray-700; }
.ck-content p { @apply mb-4; }
.ck-content ul { @apply list-disc ml-6 mb-4; }
.ck-content a { @apply text-blue-600 hover:text-blue-800 underline; }
.ck-content img { @apply max-w-full h-auto rounded-lg shadow-md my-6; }
.ck-content table { @apply w-full border-collapse my-6; }
```

These styles ensure consistent rendering on both admin and frontend.

---

## Files Modified

1. ✅ `resources/js/product-editor.js` - NEW
2. ✅ `resources/views/admin/product/create-livewire.blade.php`
3. ✅ `resources/views/admin/product/edit-livewire.blade.php`
4. ✅ `resources/views/livewire/admin/product/product-form-enhanced.blade.php`

---

## Testing Checklist

### Create Product:
- [ ] Full CKEditor loads for description
- [ ] Minimal CKEditor loads for short description
- [ ] Image insertion works via universal uploader
- [ ] Formatting preserved on save
- [ ] Livewire sync works correctly
- [ ] Word counter displays for description

### Edit Product:
- [ ] Existing content loads in both editors
- [ ] Changes save correctly
- [ ] Livewire step navigation preserves content
- [ ] No duplicate editor instances

### Frontend Display:
- [ ] Description renders with proper styling
- [ ] Short description appears on product cards
- [ ] Images display correctly
- [ ] Tables and lists render properly

---

## Benefits

1. **Consistency**: Same editor as blog posts
2. **Modern UI**: Better UX than TinyMCE
3. **Offline Ready**: No CDN dependency (already built locally)
4. **Rich Content**: Full formatting for descriptions
5. **Controlled Input**: Minimal editor for short descriptions
6. **Unified Uploads**: Single universal image uploader
7. **Tailwind Integration**: Matches site design system

---

## Notes

- **No Build Required**: User is on dev mode (`npm run dev` already running)
- **Lint Warnings**: `@apply` warnings in IDE are normal for Tailwind in Blade files
- **TinyMCE Removed**: Completely replaced, no legacy code remains
- **Backward Compatible**: Existing product descriptions still work
- **Performance**: Local CKEditor build is faster than CDN

---

## Usage Example

When creating/editing a product:

1. **Description Tab**: Full featured editor appears
2. Type content with all formatting options
3. Click "Insert Image" button in toolbar
4. Select from library or upload new
5. Image inserts at cursor position
6. **Short Description**: Minimal editor with basic formatting
7. Save - both fields sync with Livewire automatically

---

## Troubleshooting

**Editors not loading?**
- Check console for errors
- Ensure `npm run dev` is running
- Verify CKEditor element IDs are unique

**Images not inserting?**
- Verify Universal Image Uploader is included once
- Check Livewire events are firing
- Ensure media library is accessible

**Content not saving?**
- Hidden inputs must have correct wire:model
- Check Livewire component property names match
- Verify validation rules allow description fields

---

---

## Footer Settings Integration (November 22, 2024)

### Additional Update:
**File**: `resources/views/admin/footer-management/index.blade.php`

Replaced TinyMCE with minimal CKEditor for:
- Newsletter Description
- Copyright Text

**New JS File**: `resources/js/footer-settings-editor.js`
- Minimal CKEditor configuration
- Syncs with form fields automatically
- Basic formatting: bold, italic, underline, lists, links, alignment

---

## Related Documentation

- `blog-post-universal-image-uploader-integration.md`
- `blog-post-image-upload-fixes.md`
- `ckeditor-init.js` - Main CKEditor configuration
- `ckeditor-universal-uploader.js` - Custom upload plugin
- `footer-settings-editor.js` - Footer settings minimal CKEditor
