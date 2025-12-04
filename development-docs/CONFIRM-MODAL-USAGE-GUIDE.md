# Confirm Modal Usage Guide

## Overview
Standardized confirmation dialog for destructive actions (delete, remove, etc.) across the admin panel.

---

## Component Features

### ✅ Professional Design
- Red warning icon in circular background
- Clear title and message
- Backdrop blur effect (4px)
- Smooth animations (300ms)
- Scale transition (90% to 100%)

### ✅ User Experience
- Clear warning for destructive actions
- Cancel button (gray) for safety
- Confirm button (red) for action
- Click outside to cancel
- Keyboard support (ESC to close)

### ✅ Consistency
- Same design across all delete actions
- Matches project design standards
- Professional appearance

---

## Basic Usage

### 1. Simple Delete Confirmation

```blade
<!-- Hidden Form -->
<form id="delete-item-{{ $item->id }}" 
      action="{{ route('items.destroy', $item) }}" 
      method="POST" 
      style="display: inline;">
    @csrf
    @method('DELETE')
</form>

<!-- Delete Button -->
<button type="button"
        @click="$dispatch('confirm-modal', {
            title: 'Delete Item',
            message: 'Are you sure you want to delete this item? This action cannot be undone.',
            onConfirm: () => document.getElementById('delete-item-{{ $item->id }}').submit()
        })"
        class="px-4 py-2 bg-red-600 text-white rounded-lg">
    Delete
</button>
```

### 2. Delete with Item Name

```blade
<button type="button"
        @click="$dispatch('confirm-modal', {
            title: 'Delete Product',
            message: 'Are you sure you want to delete &quot;{{ addslashes($product->name) }}&quot;? This action cannot be undone.',
            onConfirm: () => document.getElementById('delete-product-{{ $product->id }}').submit()
        })">
    Delete
</button>
```

### 3. Custom Action (Not Delete)

```blade
<button type="button"
        @click="$dispatch('confirm-modal', {
            title: 'Deactivate Account',
            message: 'Are you sure you want to deactivate your account? You can reactivate it later.',
            onConfirm: () => {
                // Custom JavaScript action
                fetch('/api/deactivate', { method: 'POST' })
                    .then(() => window.location.reload());
            }
        })">
    Deactivate
</button>
```

### 4. With Additional Logic

```blade
<button type="button"
        @click="$dispatch('confirm-modal', {
            title: 'Clear Cart',
            message: 'Are you sure you want to clear all items from your cart?',
            onConfirm: () => {
                // Show loading
                document.getElementById('loading').classList.remove('hidden');
                
                // Submit form
                document.getElementById('clear-cart-form').submit();
            }
        })">
    Clear Cart
</button>
```

---

## Event Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `title` | string | No | Dialog title (default: "Confirm Action") |
| `message` | string | No | Dialog message (default: "Are you sure?") |
| `onConfirm` | function | Yes | Function to execute on confirmation |

---

## Examples by Use Case

### Delete Slider

```blade
<form id="delete-slider-form-{{ $slider->id }}" 
      action="{{ route('admin.homepage-settings.slider.destroy', $slider) }}" 
      method="POST" 
      style="display: inline;">
    @csrf
    @method('DELETE')
</form>

<button type="button"
        @click="$dispatch('confirm-modal', {
            title: 'Delete Slider',
            message: 'Are you sure you want to delete &quot;{{ addslashes($slider->title) }}&quot;? This action cannot be undone.',
            onConfirm: () => document.getElementById('delete-slider-form-{{ $slider->id }}').submit()
        })"
        class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
    <svg class="w-5 h-5">...</svg>
</button>
```

### Delete Product

```blade
<form id="delete-product-{{ $product->id }}" 
      action="{{ route('admin.products.destroy', $product) }}" 
      method="POST">
    @csrf
    @method('DELETE')
</form>

<button type="button"
        @click="$dispatch('confirm-modal', {
            title: 'Delete Product',
            message: 'Are you sure you want to delete &quot;{{ addslashes($product->name) }}&quot;? This will also remove all associated data.',
            onConfirm: () => document.getElementById('delete-product-{{ $product->id }}').submit()
        })">
    Delete Product
</button>
```

### Delete Category

```blade
<button type="button"
        @click="$dispatch('confirm-modal', {
            title: 'Delete Category',
            message: 'Are you sure you want to delete &quot;{{ addslashes($category->name) }}&quot;? All products in this category will be moved to Uncategorized.',
            onConfirm: () => document.getElementById('delete-category-{{ $category->id }}').submit()
        })">
    Delete
</button>
```

### Delete User

```blade
<button type="button"
        @click="$dispatch('confirm-modal', {
            title: 'Delete User',
            message: 'Are you sure you want to delete {{ $user->name }}? This will permanently remove their account and all associated data.',
            onConfirm: () => document.getElementById('delete-user-{{ $user->id }}').submit()
        })">
    Delete User
</button>
```

### Bulk Delete

```blade
<button type="button"
        @click="$dispatch('confirm-modal', {
            title: 'Delete Selected Items',
            message: 'Are you sure you want to delete ' + selectedCount + ' items? This action cannot be undone.',
            onConfirm: () => {
                document.getElementById('bulk-delete-form').submit();
            }
        })">
    Delete Selected
</button>
```

---

## Design Specifications

### Modal Container
- Max Width: `max-w-md` (448px)
- Padding: `p-6` (1.5rem)
- Background: `rgba(255, 255, 255, 0.95)` with blur
- Border: `1px solid #E5E7EB` (gray-200)
- Border Radius: `rounded-lg` (0.5rem)
- Shadow: `shadow-2xl`

### Icon
- Size: `w-12 h-12` (48px)
- Background: `bg-red-100`
- Icon Color: `text-red-600`
- Border Radius: `rounded-full`
- Margin: `mx-auto mb-4`

### Title
- Font Size: `text-lg` (1.125rem)
- Font Weight: `font-bold`
- Color: `text-gray-900`
- Alignment: `text-center`
- Margin: `mb-2`

### Message
- Font Size: `text-sm` (0.875rem)
- Color: `text-gray-600`
- Alignment: `text-center`
- Margin: `mb-6`

### Buttons
- **Cancel Button**:
  - Background: `bg-gray-100 hover:bg-gray-200`
  - Text: `text-gray-700`
  - Padding: `px-4 py-2`
  - Font: `text-sm font-medium`
  
- **Confirm Button**:
  - Background: `bg-red-600 hover:bg-red-700`
  - Text: `text-white`
  - Padding: `px-4 py-2`
  - Font: `text-sm font-medium`

### Animations
- Enter: `opacity-0 scale-90` → `opacity-100 scale-100` (300ms)
- Leave: `opacity-100 scale-100` → `opacity-0 scale-90` (200ms)
- Easing: `ease-out` (enter), `ease-in` (leave)

---

## Best Practices

### ✅ DO
- Use for all destructive actions (delete, remove, clear)
- Include item name in message when possible
- Use clear, descriptive titles
- Explain consequences in message
- Keep messages concise but informative
- Use proper HTML escaping for item names

### ❌ DON'T
- Use for non-destructive actions
- Make messages too long
- Use technical jargon
- Skip the confirmation for important actions
- Use inline JavaScript confirms
- Forget to escape special characters in names

---

## Message Templates

### Delete Actions
```
'Are you sure you want to delete "[ITEM_NAME]"? This action cannot be undone.'
```

### Remove Actions
```
'Are you sure you want to remove "[ITEM_NAME]" from [LOCATION]?'
```

### Clear Actions
```
'Are you sure you want to clear all [ITEMS]? This action cannot be undone.'
```

### Deactivate Actions
```
'Are you sure you want to deactivate "[ITEM_NAME]"? You can reactivate it later.'
```

### Archive Actions
```
'Are you sure you want to archive "[ITEM_NAME]"? It will be moved to the archive.'
```

---

## Escaping Special Characters

### Using addslashes() (Blade)
```blade
message: 'Delete &quot;{{ addslashes($item->name) }}&quot;?'
```

### Using e() (Blade)
```blade
message: 'Delete "{{ e($item->name) }}"?'
```

### Using JavaScript
```javascript
message: `Delete "${item.name.replace(/"/g, '&quot;')}"?`
```

---

## Integration Checklist

When adding confirm modal to a delete action:

- [ ] Create hidden form with unique ID
- [ ] Add @csrf and @method('DELETE')
- [ ] Change button type to "button" (not "submit")
- [ ] Add @click with $dispatch('confirm-modal', {...})
- [ ] Set appropriate title
- [ ] Set descriptive message with item name
- [ ] Escape special characters in item name
- [ ] Set onConfirm to submit the form
- [ ] Test the confirmation flow
- [ ] Verify form submission works

---

## Troubleshooting

### Modal Not Showing
1. Check if `<x-confirm-modal />` is included in layout
2. Verify Alpine.js is loaded
3. Check browser console for errors
4. Ensure event name is correct: `confirm-modal`

### Form Not Submitting
1. Verify form ID matches in onConfirm
2. Check form action and method
3. Ensure @csrf token is present
4. Check network tab for submission

### Special Characters Breaking
1. Use `addslashes()` or `e()` for Blade
2. Use `&quot;` instead of `"` in messages
3. Test with items containing quotes, apostrophes

---

## Component Location

```
resources/views/components/confirm-modal.blade.php
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
