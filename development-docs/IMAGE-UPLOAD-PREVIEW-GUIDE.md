# Image Upload Preview Feature Guide

## Overview
Real-time image preview functionality for file uploads using Alpine.js, providing instant visual feedback before form submission.

---

## Features Implemented

### ✅ Real-Time Preview
- Instant preview when image is selected
- Shows actual image before upload
- Smooth fade-in animation (200ms)
- Scale transition effect (95% to 100%)

### ✅ File Information Display
- Shows selected file name
- Green checkmark icon for confirmation
- File name displayed below preview

### ✅ Remove/Clear Functionality
- Hover overlay on preview image
- "Remove" button appears on hover
- Clears preview and resets input
- Smooth opacity transition

### ✅ Visual Feedback
- Border highlight on preview (border-2)
- Shadow effect for depth
- Hover overlay with dark background
- Icon indicators for status

### ✅ Helpful Guidelines
- Recommended image specifications
- Size, format, and file size limits
- Info icon with detailed list
- Error message display

---

## Implementation Details

### Alpine.js Data Structure

```javascript
x-data="{ 
    imagePreview: '',           // Stores preview URL
    fileName: '',               // Stores file name
    previewImage(event) {       // Handles file selection
        const file = event.target.files[0];
        if (file) {
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    },
    clearPreview() {            // Clears preview
        this.imagePreview = '';
        this.fileName = '';
        $refs.imageInput.value = '';
    }
}"
```

### HTML Structure

```blade
<!-- Image Preview Container -->
<div x-show="imagePreview" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     class="mb-4 relative group">
    
    <!-- Preview Image -->
    <img :src="imagePreview" 
         alt="Preview" 
         class="w-full h-48 object-cover rounded-lg border-2 border-gray-300 shadow-sm">
    
    <!-- Hover Overlay -->
    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 rounded-lg flex items-center justify-center">
        <button type="button"
                @click="clearPreview()"
                class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">
            Remove
        </button>
    </div>
    
    <!-- File Name -->
    <p x-show="fileName" class="text-xs text-gray-600 mt-2 flex items-center">
        <svg class="w-4 h-4 mr-1 text-green-500">...</svg>
        <span x-text="fileName"></span>
    </p>
</div>

<!-- File Input -->
<input type="file" 
       x-ref="imageInput"
       name="image" 
       accept="image/*"
       @change="previewImage($event)"
       class="...">
```

---

## Usage in Other Forms

### Basic Implementation

```blade
<div x-data="{ 
    imagePreview: '',
    fileName: '',
    previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    },
    clearPreview() {
        this.imagePreview = '';
        this.fileName = '';
        $refs.imageInput.value = '';
    }
}">
    <!-- Preview -->
    <div x-show="imagePreview" class="mb-4 relative group">
        <img :src="imagePreview" alt="Preview" class="w-full h-48 object-cover rounded-lg border-2 border-gray-300">
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 rounded-lg flex items-center justify-center">
            <button type="button" @click="clearPreview()" class="opacity-0 group-hover:opacity-100 px-4 py-2 bg-red-600 text-white rounded-lg">
                Remove
            </button>
        </div>
    </div>
    
    <!-- Input -->
    <input type="file" 
           x-ref="imageInput"
           @change="previewImage($event)"
           accept="image/*">
</div>
```

### With Existing Image (Edit Mode)

```blade
<div x-data="{ 
    imagePreview: '{{ $item->image_url ?? '' }}',  // Load existing image
    fileName: '',
    previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    },
    clearPreview() {
        this.imagePreview = '{{ $item->image_url ?? '' }}';  // Reset to original
        this.fileName = '';
        $refs.imageInput.value = '';
    }
}">
    <!-- Preview -->
    <div x-show="imagePreview" class="mb-4 relative group">
        <img :src="imagePreview" alt="Preview" class="w-full h-48 object-cover rounded-lg">
        <button type="button" @click="clearPreview()">Remove</button>
    </div>
    
    <!-- Input -->
    <input type="file" x-ref="imageInput" @change="previewImage($event)">
</div>
```

### Multiple Images Preview

```blade
<div x-data="{ 
    images: [],
    addImage(event) {
        const files = Array.from(event.target.files);
        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.images.push({
                    url: e.target.result,
                    name: file.name
                });
            };
            reader.readAsDataURL(file);
        });
    },
    removeImage(index) {
        this.images.splice(index, 1);
    }
}">
    <!-- Preview Grid -->
    <div class="grid grid-cols-3 gap-4 mb-4">
        <template x-for="(image, index) in images" :key="index">
            <div class="relative group">
                <img :src="image.url" class="w-full h-32 object-cover rounded-lg">
                <button @click="removeImage(index)" class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded">
                    <svg class="w-4 h-4">...</svg>
                </button>
            </div>
        </template>
    </div>
    
    <!-- Input -->
    <input type="file" @change="addImage($event)" multiple accept="image/*">
</div>
```

---

## Design Specifications

### Preview Container
- Width: `w-full` (100%)
- Height: `h-48` (192px)
- Border: `border-2 border-gray-300`
- Border Radius: `rounded-lg` (0.5rem)
- Object Fit: `object-cover`
- Shadow: `shadow-sm`

### Hover Overlay
- Background: `bg-black`
- Opacity: `bg-opacity-0` → `bg-opacity-40` on hover
- Transition: `transition-all duration-200`
- Display: Flexbox centered

### Remove Button
- Background: `bg-red-600 hover:bg-red-700`
- Text: White
- Padding: `px-4 py-2`
- Border Radius: `rounded-lg`
- Opacity: `opacity-0` → `opacity-100` on hover
- Transition: `transition-opacity duration-200`

### File Name Display
- Font Size: `text-xs`
- Color: `text-gray-600`
- Icon: Green checkmark (`text-green-500`)
- Margin: `mt-2`

### Transitions
- Enter: `ease-out duration-200`
- Start: `opacity-0 scale-95`
- End: `opacity-100 scale-100`

---

## Browser Compatibility

### FileReader API Support
- ✅ Chrome/Edge (all versions)
- ✅ Firefox (all versions)
- ✅ Safari (all versions)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

### Image Format Support
- ✅ JPG/JPEG
- ✅ PNG
- ✅ WebP
- ✅ GIF
- ✅ SVG
- ✅ BMP

---

## Best Practices

### ✅ DO
- Show preview immediately after selection
- Provide clear remove/clear option
- Display file name for confirmation
- Show recommended specifications
- Validate file size and type
- Use smooth transitions
- Provide visual feedback

### ❌ DON'T
- Upload without preview
- Hide file name
- Make remove button hard to find
- Skip validation messages
- Use jarring animations
- Forget mobile optimization

---

## Validation Examples

### Client-Side Validation

```javascript
previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        // Check file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            event.target.value = '';
            return;
        }
        
        // Check file type
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('Only JPG, PNG, and WebP files are allowed');
            event.target.value = '';
            return;
        }
        
        this.fileName = file.name;
        const reader = new FileReader();
        reader.onload = (e) => {
            this.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
```

### Server-Side Validation (Laravel)

```php
// In FormRequest
public function rules()
{
    return [
        'image' => [
            'required',
            'image',
            'mimes:jpeg,png,jpg,webp',
            'max:2048', // 2MB
            'dimensions:min_width=800,min_height=400'
        ]
    ];
}

public function messages()
{
    return [
        'image.required' => 'Please select an image',
        'image.image' => 'File must be an image',
        'image.mimes' => 'Only JPG, PNG, and WebP formats are allowed',
        'image.max' => 'Image size must not exceed 2MB',
        'image.dimensions' => 'Image must be at least 800x400 pixels'
    ];
}
```

---

## Accessibility

### Screen Reader Support
```blade
<label for="image-upload" class="block text-sm font-medium text-gray-700 mb-2">
    Slider Image <span class="text-red-500" aria-label="required">*</span>
</label>

<input type="file" 
       id="image-upload"
       name="image"
       aria-describedby="image-help"
       accept="image/*">

<p id="image-help" class="text-xs text-gray-500 mt-1">
    Recommended size: 1920x400px (JPG, PNG, WebP)
</p>
```

---

## Performance Considerations

### File Size Limits
- Recommended: 2MB max
- Large files slow down preview
- Use compression for production

### Image Optimization
```javascript
// Optional: Resize before preview
previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                // Resize if needed
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.width = 800;
                canvas.height = 400;
                ctx.drawImage(img, 0, 0, 800, 400);
                this.imagePreview = canvas.toDataURL('image/jpeg', 0.9);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
```

---

## Troubleshooting

### Preview Not Showing
1. Check Alpine.js is loaded
2. Verify x-data is properly initialized
3. Check browser console for errors
4. Ensure FileReader API is supported

### Image Distorted
- Use `object-cover` for consistent aspect ratio
- Set explicit height: `h-48` or similar
- Consider using `object-contain` for full image

### Remove Button Not Working
- Check x-ref is correctly set
- Verify clearPreview() function exists
- Ensure button is inside x-data scope

---

**Created:** November 6, 2025  
**Version:** 1.0  
**Status:** Production Ready ✅  
**Dependencies:** Alpine.js, Tailwind CSS
