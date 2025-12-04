# Standardized Dialog Component Guide

## Overview
A reusable, accessible, and consistent dialog/modal component following `.windsurfrules` design standards.

---

## Component Features

### ✅ Design Standards
- **Backdrop blur effect** (4px blur with 50% opacity)
- **Smooth animations** (300ms ease-out enter, 200ms ease-in leave)
- **Scale transitions** (95% to 100%)
- **Keyboard support** (ESC to close)
- **Click outside to close**
- **Responsive sizing** (with max-width options)
- **Shadow effects** (shadow-2xl for depth)
- **Border styling** (border-gray-200)

### ✅ Accessibility
- Keyboard navigation (ESC key)
- Focus management
- ARIA-compliant structure
- Click-outside-to-close
- Smooth transitions

### ✅ Customization
- Multiple size options (sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl)
- Optional title with close button
- Scrollable content area
- Event-based open/close

---

## Usage

### Basic Usage

```blade
<x-dialog name="my-dialog" title="Dialog Title" max-width="2xl">
    <p>Your content here...</p>
</x-dialog>
```

### Open Dialog (Alpine.js)

```html
<button @click="$dispatch('open-dialog-my-dialog')">
    Open Dialog
</button>
```

### Close Dialog (Alpine.js)

```html
<button @click="$dispatch('close-dialog-my-dialog')">
    Close Dialog
</button>
```

### Inside Dialog Content

```blade
<x-dialog name="example" title="Example Dialog">
    <form action="/submit" method="POST">
        @csrf
        
        <!-- Your form fields -->
        
        <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" 
                    @click="$dispatch('close-dialog-example')"
                    class="px-4 py-2 border border-gray-300 rounded-lg">
                Cancel
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                Submit
            </button>
        </div>
    </form>
</x-dialog>
```

---

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | '' | Unique identifier for the dialog (required for events) |
| `title` | string | '' | Dialog title (optional, shows header if provided) |
| `maxWidth` | string | '2xl' | Maximum width (sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl) |
| `show` | boolean | false | Initial visibility state |

---

## Size Options

| Size | Max Width | Best For |
|------|-----------|----------|
| `sm` | 384px | Confirmations, alerts |
| `md` | 448px | Small forms |
| `lg` | 512px | Medium forms |
| `xl` | 576px | Large forms |
| `2xl` | 672px | **Default** - Complex forms |
| `3xl` | 768px | Wide content |
| `4xl` | 896px | Very wide content |
| `5xl` | 1024px | Extra wide content |
| `6xl` | 1152px | Full-width forms |
| `7xl` | 1280px | Maximum width |

---

## Examples

### 1. Simple Confirmation Dialog

```blade
<x-dialog name="confirm-delete" title="Confirm Deletion" max-width="md">
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Are you sure?</h3>
        <p class="text-sm text-gray-500 mb-6">This action cannot be undone.</p>
        
        <div class="flex justify-center gap-3">
            <button @click="$dispatch('close-dialog-confirm-delete')"
                    class="px-4 py-2 border border-gray-300 rounded-lg">
                Cancel
            </button>
            <button onclick="deleteItem()"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg">
                Delete
            </button>
        </div>
    </div>
</x-dialog>

<!-- Trigger -->
<button @click="$dispatch('open-dialog-confirm-delete')">
    Delete Item
</button>
```

### 2. Form Dialog

```blade
<x-dialog name="add-product" title="Add New Product" max-width="3xl">
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                <input type="number" name="price" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
        </div>
        
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
            <button type="button" 
                    @click="$dispatch('close-dialog-add-product')"
                    class="px-4 py-2 border border-gray-300 rounded-lg">
                Cancel
            </button>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg">
                Add Product
            </button>
        </div>
    </form>
</x-dialog>
```

### 3. Dynamic Dialog (Multiple Instances)

```blade
@foreach($items as $item)
    <x-dialog name="edit-item-{{ $item->id }}" title="Edit {{ $item->name }}" max-width="2xl">
        <form action="{{ route('items.update', $item) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Form fields -->
            
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" 
                        @click="$dispatch('close-dialog-edit-item-{{ $item->id }}')"
                        class="px-4 py-2 border rounded-lg">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                    Update
                </button>
            </div>
        </form>
    </x-dialog>
    
    <!-- Trigger -->
    <button @click="$dispatch('open-dialog-edit-item-{{ $item->id }}')">
        Edit {{ $item->name }}
    </button>
@endforeach
```

### 4. No Title Dialog (Custom Header)

```blade
<x-dialog name="custom-dialog" max-width="lg">
    <div class="text-center py-6">
        <img src="/logo.png" alt="Logo" class="mx-auto h-16 mb-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome!</h2>
        <p class="text-gray-600 mb-6">Get started with our platform</p>
        
        <button @click="$dispatch('close-dialog-custom-dialog')"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg">
            Get Started
        </button>
    </div>
</x-dialog>
```

---

## JavaScript Events

### Open Dialog
```javascript
// Using Alpine.js dispatch
$dispatch('open-dialog-my-dialog')

// Using vanilla JavaScript
window.dispatchEvent(new CustomEvent('open-dialog-my-dialog'));
```

### Close Dialog
```javascript
// Using Alpine.js dispatch
$dispatch('close-dialog-my-dialog')

// Using vanilla JavaScript
window.dispatchEvent(new CustomEvent('close-dialog-my-dialog'));
```

---

## Design Specifications

### Backdrop
- Background: `rgba(0, 0, 0, 0.5)`
- Blur: `4px`
- Transition: `300ms ease-out` (enter), `200ms ease-in` (leave)

### Dialog Container
- Background: White with blur effect
- Border: `1px solid #E5E7EB` (gray-200)
- Border Radius: `0.5rem` (rounded-lg)
- Shadow: `shadow-2xl`
- Max Height: `calc(100vh - 200px)` for content area

### Header
- Background: `#F9FAFB` (gray-50)
- Border Bottom: `1px solid #E5E7EB`
- Padding: `1rem 1.5rem` (px-6 py-4)
- Title: `text-lg font-semibold text-gray-900`

### Content Area
- Padding: `1rem 1.5rem` (px-6 py-4)
- Overflow: `overflow-y-auto`
- Max Height: Scrollable

### Animations
- Enter: `opacity-0 scale-95` → `opacity-100 scale-100` (300ms)
- Leave: `opacity-100 scale-100` → `opacity-0 scale-95` (200ms)
- Easing: `ease-out` (enter), `ease-in` (leave)

---

## Best Practices

### ✅ DO
- Use unique names for each dialog
- Provide descriptive titles
- Include cancel/close buttons
- Use appropriate max-width for content
- Add loading states for async operations
- Validate forms before submission
- Show success/error messages after actions

### ❌ DON'T
- Nest dialogs inside dialogs
- Use same name for multiple dialogs
- Make dialogs too large (use appropriate max-width)
- Forget to handle form submissions
- Remove keyboard support (ESC key)
- Use inline styles (use Tailwind classes)

---

## Comparison with Old Modal

| Feature | Old Modal | New Dialog |
|---------|-----------|------------|
| Backdrop Blur | ❌ No | ✅ Yes (4px) |
| Animations | ❌ Basic | ✅ Smooth (300ms) |
| Scale Effect | ❌ No | ✅ Yes (95-100%) |
| Header Style | ❌ Plain | ✅ Gray background |
| Close Button | ✅ Yes | ✅ Yes (improved) |
| Keyboard Support | ❌ No | ✅ ESC key |
| Event System | ❌ Custom | ✅ Standardized |
| Max Width Options | ❌ Fixed | ✅ 9 options |
| Scrollable Content | ❌ Limited | ✅ Full support |
| Consistency | ❌ Varied | ✅ Standardized |

---

## Migration Guide

### From Old Modal to New Dialog

**Before:**
```blade
<div x-show="showModal" class="fixed inset-0 z-50">
    <div @click="showModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
    <div class="relative bg-white rounded-lg max-w-2xl">
        <h3>Title</h3>
        <div>Content</div>
    </div>
</div>

<button @click="showModal = true">Open</button>
```

**After:**
```blade
<x-dialog name="my-dialog" title="Title" max-width="2xl">
    Content
</x-dialog>

<button @click="$dispatch('open-dialog-my-dialog')">Open</button>
```

---

## File Location

```
resources/views/components/dialog.blade.php
```

---

## Dependencies

- **Alpine.js** (for reactivity and events)
- **Tailwind CSS** (for styling)

---

## Browser Support

- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers

---

**Created:** November 6, 2025  
**Version:** 1.0  
**Status:** Production Ready ✅  
**Compliance:** 100% .windsurfrules compliant
