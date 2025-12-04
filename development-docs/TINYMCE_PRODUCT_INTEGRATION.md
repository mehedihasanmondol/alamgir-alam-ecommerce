# TinyMCE Integration for Product Forms

## Overview
Successfully integrated TinyMCE HTML editor into product add and edit forms, matching the implementation used in blog posts.

## Implementation Details

### 1. Modified Files

#### `resources/views/livewire/admin/product/product-form.blade.php`
- Replaced standard textarea for product description with TinyMCE editor
- Added `wire:ignore` directive to prevent Livewire from interfering with TinyMCE
- Used unique ID `product-description-editor` for TinyMCE initialization

```blade
<div wire:ignore>
    <textarea id="product-description-editor" 
              class="w-full">{{ $description }}</textarea>
</div>
```

#### `resources/views/admin/product/create-livewire.blade.php`
- Added TinyMCE CDN script (same API key as blog posts)
- Added custom styling for TinyMCE editor
- Configured TinyMCE with comprehensive plugin set
- Implemented Livewire synchronization

#### `resources/views/admin/product/edit-livewire.blade.php`
- Same implementation as create page
- Ensures existing product descriptions load correctly in TinyMCE
- Maintains Livewire sync for updates

### 2. TinyMCE Configuration

#### Plugins Enabled:
- `advlist` - Advanced list options
- `autolink` - Automatic link detection
- `lists` - List formatting
- `link` - Link insertion/editing
- `image` - Image insertion/editing
- `charmap` - Special character insertion
- `preview` - Content preview
- `anchor` - Anchor insertion
- `searchreplace` - Find and replace
- `visualblocks` - Visual block display
- `code` - HTML code editor
- `fullscreen` - Full-screen editing
- `insertdatetime` - Date/time insertion
- `media` - Media embedding
- `table` - Table creation/editing
- `help` - Help documentation
- `wordcount` - Word counter

#### Toolbar Features:
- Undo/Redo
- Block formatting (headings, paragraphs)
- Text formatting (bold, italic, underline, strikethrough)
- Text/background colors
- Text alignment
- Lists (bulleted, numbered)
- Indentation
- Links, images, media, tables
- Code view
- Format removal
- Help

### 3. Livewire Integration

#### Key Implementation Points:

**Wire:ignore Directive:**
```blade
<div wire:ignore>
    <textarea id="product-description-editor">{{ $description }}</textarea>
</div>
```
This prevents Livewire from re-rendering the TinyMCE editor.

**Content Synchronization:**
```javascript
editor.on('change keyup', function() {
    @this.set('description', editor.getContent());
});
```
Updates the Livewire component property in real-time.

**Initial Content Loading:**
```javascript
editor.on('init', function() {
    editor.setContent(document.querySelector('#product-description-editor').value || '');
});
```
Loads existing content when editing products.

### 4. Image Upload Support

- Uses the same image upload route as blog posts: `admin.blog.upload-image`
- Supports drag-and-drop image uploads
- Includes upload progress tracking
- Automatic CSRF token handling
- Error handling for failed uploads

### 5. Styling

Custom CSS ensures the editor matches the application's design:
```css
.tox-tinymce {
    border-radius: 0.5rem !important;
    border: 1px solid #e2e8f0 !important;
}
```

## Usage

### Creating a New Product
1. Navigate to `/admin/products/create`
2. Fill in product details
3. Use the TinyMCE editor for the "Full Description" field
4. Add formatted text, images, tables, etc.
5. Save the product

### Editing an Existing Product
1. Navigate to `/admin/products/{id}/edit`
2. Existing description loads automatically in TinyMCE
3. Make changes using the rich text editor
4. Save updates

## Features

✅ **Rich Text Formatting** - Bold, italic, underline, colors, etc.
✅ **Image Upload** - Direct image upload within the editor
✅ **Code Editor** - HTML code view for advanced users
✅ **Tables** - Create and edit tables
✅ **Media Embedding** - Embed videos and other media
✅ **Full-Screen Mode** - Distraction-free editing
✅ **Word Count** - Track content length
✅ **Livewire Sync** - Real-time synchronization with form data
✅ **Consistent UI** - Matches blog post editor styling

## Technical Notes

### CDN vs Local Installation
- Currently using TinyMCE CDN (as per blog implementation)
- Uses the same API key: `8wacbe3zs5mntet5c9u50n4tenlqvgqm9bn1k6uctyqo3o7m`
- Note: Project guidelines prefer local installation, but matching blog implementation for consistency

### Livewire Compatibility
- `wire:ignore` is crucial for preventing Livewire from destroying the TinyMCE instance
- Manual synchronization via `@this.set()` ensures data is saved correctly
- Editor reinitializes properly when navigating between steps

### Browser Compatibility
- Works in all modern browsers
- Requires JavaScript enabled
- Mobile-responsive interface

## Troubleshooting

### Editor Not Loading
- Check browser console for JavaScript errors
- Verify TinyMCE CDN is accessible
- Ensure Livewire is properly loaded

### Content Not Saving
- Verify `@this.set('description', content)` is firing
- Check Livewire component has `$description` property
- Inspect network tab for form submission

### Images Not Uploading
- Verify `admin.blog.upload-image` route exists
- Check CSRF token is valid
- Ensure upload directory has write permissions

## Future Enhancements

- [ ] Consider migrating to local TinyMCE installation (per project guidelines)
- [ ] Add dedicated product image upload route
- [ ] Implement auto-save functionality
- [ ] Add content templates for common product descriptions
- [ ] Enable collaborative editing features

## Conclusion

TinyMCE has been successfully integrated into the product add/edit forms, providing a professional rich text editing experience consistent with the blog post editor. The implementation properly handles Livewire synchronization and includes all essential features for creating compelling product descriptions.
