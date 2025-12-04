# Category Media ID Debug Guide

**Date:** November 21, 2025  
**Issue:** Verifying media_id is properly saved when selecting images from universal image uploader

---

## Debugging Features Added

### 1. **Frontend Debugging (Browser Console)**

Added console logs to track the entire flow:

```javascript
// Initial state
console.log('Initial media_id:', categoryImage)

// When image is selected/uploaded
console.log('Image updated event:', event.detail)
console.log('Category image set to:', this.categoryImage)

// When image is removed
console.log('Image removed')
```

### 2. **Visual Debug Display**

Added on-screen display showing selected media ID:
```html
<p class="mt-2 text-xs text-gray-500" x-show="categoryImage">
    Selected Media ID: <span x-text="categoryImage"></span>
</p>
```

### 3. **Backend Debugging (Laravel Logs)**

Added comprehensive logging in controller:

```php
// Log incoming request
Log::info('Category Update Request', [
    'category_id' => $category->id,
    'media_id_in_request' => $request->input('media_id'),
    'all_request_data' => $request->except(['_token', '_method'])
]);

// Log validated data
Log::info('Category Update Validated Data', [
    'validated_data' => $validated
]);

// Log after update
Log::info('Category After Update', [
    'category_id' => $category->id,
    'media_id' => $category->media_id
]);
```

---

## Testing Steps

### **Step 1: Open Browser Console**
1. Open category edit page
2. Press F12 to open Developer Tools
3. Go to Console tab
4. Look for: `Initial media_id: [number or null]`

### **Step 2: Select Image from Library**
1. Click on image uploader
2. Go to Library tab
3. Select an image
4. Click "Select" button
5. **Check Console for:**
   ```
   Image updated event: {media: Array(1), field: "category_image"}
   Category image set to: [media_id_number]
   ```
6. **Check Visual Display:**
   - Should show: "Selected Media ID: [number]"

### **Step 3: Verify Hidden Input**
1. In browser console, type:
   ```javascript
   document.querySelector('input[name="media_id"]').value
   ```
2. Should return the media ID number

### **Step 4: Submit Form**
1. Click "Update Category" button
2. **Check Laravel Logs** (`storage/logs/laravel.log`):
   ```
   [timestamp] local.INFO: Category Update Request
   {
       "category_id": 1,
       "media_id_in_request": "123",
       "all_request_data": {...}
   }
   
   [timestamp] local.INFO: Category Update Validated Data
   {
       "validated_data": {
           "name": "...",
           "media_id": "123",
           ...
       }
   }
   
   [timestamp] local.INFO: Category After Update
   {
       "category_id": 1,
       "media_id": "123"
   }
   ```

### **Step 5: Verify Database**
1. Check database directly:
   ```sql
   SELECT id, name, media_id FROM categories WHERE id = [category_id];
   ```
2. The media_id column should contain the selected media ID

### **Step 6: Verify Display**
1. Go to category list page
2. The category should show the selected image
3. Edit the category again
4. The selected image should appear in the preview

---

## Troubleshooting

### **Issue: Console shows "undefined" for media ID**

**Possible Causes:**
1. Event structure is different than expected
2. Media array is empty
3. Event not firing

**Solution:**
- Check full event object: `console.log('Full event:', event)`
- Verify event.detail.media exists and has items
- Check if Livewire component is dispatching events correctly

---

### **Issue: Hidden input value is empty**

**Possible Causes:**
1. Alpine.js not binding properly
2. Variable scope issue
3. Input outside Alpine.js scope

**Solution:**
- Verify Alpine.js is loaded: Check for `[x-cloak]` in page
- Check Alpine.js scope: Input must be inside `x-data` div
- Manually inspect: `document.querySelector('input[name="media_id"]')`

---

### **Issue: media_id not in request payload**

**Possible Causes:**
1. Hidden input not inside form tags
2. Form submission intercepted by JavaScript
3. Input disabled or has wrong name attribute

**Solution:**
- Verify input is inside `<form>` tags
- Check form method is POST with @method('PUT')
- Inspect FormData before submission:
  ```javascript
  document.querySelector('form').addEventListener('submit', (e) => {
      const formData = new FormData(e.target);
      console.log('Form data:', Object.fromEntries(formData));
  });
  ```

---

### **Issue: Validation fails for media_id**

**Possible Causes:**
1. Media ID doesn't exist in media table
2. Validation rule too strict
3. Value is string instead of integer

**Solution:**
- Check media exists: `SELECT * FROM media WHERE id = [media_id]`
- Verify validation rule: `'media_id' => 'nullable|exists:media,id'`
- Check data type in logs

---

### **Issue: media_id not saved to database**

**Possible Causes:**
1. Not in fillable array
2. Not in validated data
3. Database column doesn't exist

**Solution:**
- Check model fillable: `protected $fillable = [..., 'media_id']`
- Check validated data in logs
- Verify column: `SHOW COLUMNS FROM categories LIKE 'media_id'`

---

## Expected Flow

```
1. User selects image
   ↓
2. Livewire dispatches 'imageUploaded' or 'imageSelected' event
   ↓
3. Alpine.js catches event via window.addEventListener
   ↓
4. Alpine.js updates categoryImage variable
   ↓
5. Hidden input :value binding updates
   ↓
6. Form submits with media_id in payload
   ↓
7. Controller receives and validates media_id
   ↓
8. Category model updates with new media_id
   ↓
9. Database saves media_id
   ↓
10. Category displays image using media relationship
```

---

## Files to Check

### **Frontend:**
- `resources/views/admin/categories/edit.blade.php` - Form and Alpine.js
- `resources/views/components/image-uploader.blade.php` - Component events
- `resources/views/livewire/universal-image-uploader.blade.php` - Event dispatch

### **Backend:**
- `app/Http/Controllers/Admin/CategoryController.php` - Request handling
- `app/Modules/Ecommerce/Category/Models/Category.php` - Model definition
- `storage/logs/laravel.log` - Debug logs

### **Database:**
- `categories` table - Check media_id column
- `media` table - Verify media records exist

---

## Cleanup After Testing

Once issue is resolved, remove debug code:

1. **Remove console.log statements** from edit.blade.php
2. **Remove visual debug display** (Selected Media ID text)
3. **Remove or comment out Log::info** statements in controller
4. **Keep the fix** (proper event handling and binding)

---

## Status

- ✅ Debug logging added to frontend
- ✅ Debug logging added to backend
- ✅ Visual feedback added
- ✅ Testing guide created
- ⏳ Waiting for test results
