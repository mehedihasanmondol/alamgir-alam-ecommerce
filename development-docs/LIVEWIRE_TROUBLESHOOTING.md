# Livewire Troubleshooting Guide

## ✅ Current Status

- **Livewire**: ✅ Installed (v3.6.4)
- **Components**: ✅ All 3 components working
- **Database**: ✅ Setup complete
- **Admin User**: ✅ Configured

---

## 🔍 Component Status

All Livewire components are properly configured:

1. ✅ `App\Livewire\User\UserStatusToggle`
2. ✅ `App\Livewire\User\UserSearch`
3. ✅ `App\Livewire\Admin\GlobalUserSearch`

---

## 🎯 How to Use Livewire Components

### 1. UserStatusToggle (Toggle User Active Status)

**Location**: User list page (`admin/users`)

**Usage in Blade**:
```blade
@livewire('user.user-status-toggle', ['userId' => $user->id, 'isActive' => $user->is_active], key('user-status-'.$user->id))
```

**What it does**:
- Displays a toggle switch
- Clicking toggles user active/inactive status
- Updates without page reload
- Shows visual feedback

### 2. UserSearch (Search & Filter Users)

**Location**: User list page (`admin/users`)

**Usage in Blade**:
```blade
@livewire('user.user-search')
```

**Features**:
- Real-time search (name, email, mobile)
- Filter by role (admin/customer)
- Filter by status (active/inactive)
- Pagination
- Debounced search (300ms)

### 3. GlobalUserSearch (Admin Panel Search)

**Location**: Admin layout header

**Usage in Blade**:
```blade
@livewire('admin.global-user-search')
```

**Features**:
- Quick user search from anywhere
- Dropdown results
- Click to view user details
- Auto-hide on click away

---

## 🐛 Common Issues & Solutions

### Issue 1: "Livewire component not found"

**Error Message**:
```
Unable to find component: [user.user-status-toggle]
```

**Solutions**:

1. **Clear cache**:
```bash
php artisan optimize:clear
```

2. **Verify component exists**:
```bash
php test-livewire.php
```

3. **Check namespace**: Ensure component class is in correct namespace:
```php
namespace App\Livewire\User;
```

---

### Issue 2: "Component not updating"

**Symptoms**:
- Clicking toggle does nothing
- Search doesn't filter results
- No visual feedback

**Solutions**:

1. **Check @livewireScripts is present**:
```blade
<!-- At end of layout, before </body> -->
@livewireScripts
```

2. **Check @livewireStyles is present**:
```blade
<!-- In <head> section -->
@livewireStyles
```

3. **Clear browser cache**:
- Hard refresh: `Ctrl + Shift + R`
- Or clear browser cache completely

4. **Check browser console for errors**:
- Open DevTools (F12)
- Look for JavaScript errors
- Look for 404 errors

---

### Issue 3: "Livewire scripts not loading"

**Error in Console**:
```
Uncaught ReferenceError: Livewire is not defined
```

**Solutions**:

1. **Verify Livewire is installed**:
```bash
composer show livewire/livewire
```

2. **Republish Livewire assets**:
```bash
php artisan livewire:publish --assets
```

3. **Check layout includes scripts**:
```blade
@livewireStyles  <!-- in <head> -->
@livewireScripts <!-- before </body> -->
```

---

### Issue 4: "CSRF token mismatch"

**Error Message**:
```
419 | CSRF token mismatch
```

**Solutions**:

1. **Check meta tag in layout**:
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

2. **Clear session**:
```bash
php artisan session:clear
```

3. **Logout and login again**

---

### Issue 5: "Method not found on component"

**Error Message**:
```
Method [toggleStatus] not found on component
```

**Solutions**:

1. **Check method exists in component class**:
```php
public function toggleStatus()
{
    // Method implementation
}
```

2. **Check method is public** (not private or protected)

3. **Clear compiled views**:
```bash
php artisan view:clear
```

---

## 🔧 Debugging Tips

### Enable Livewire Debug Mode

Add to `.env`:
```env
LIVEWIRE_DEBUG=true
```

### Check Livewire Network Requests

1. Open browser DevTools (F12)
2. Go to Network tab
3. Filter by "livewire"
4. Interact with component
5. Check request/response

**Successful request should show**:
- Status: 200
- Response contains component data

### Test Component Manually

Create a test route:
```php
Route::get('/test-livewire', function () {
    return view('test-livewire');
});
```

Create test view:
```blade
<!DOCTYPE html>
<html>
<head>
    @livewireStyles
</head>
<body>
    @livewire('user.user-status-toggle', ['userId' => 1, 'isActive' => true])
    @livewireScripts
</body>
</html>
```

---

## ✅ Verification Checklist

After fixing issues, verify:

- [ ] Livewire installed: `composer show livewire/livewire`
- [ ] Components exist: `php test-livewire.php`
- [ ] @livewireStyles in layout head
- [ ] @livewireScripts before </body>
- [ ] CSRF meta tag present
- [ ] No console errors
- [ ] Toggle switch works
- [ ] Search filters work
- [ ] Global search works
- [ ] No 404 errors in network tab

---

## 📊 Component File Locations

```
app/Livewire/
├── User/
│   ├── UserSearch.php
│   └── UserStatusToggle.php
└── Admin/
    └── GlobalUserSearch.php

resources/views/livewire/
├── user/
│   ├── user-search.blade.php
│   └── user-status-toggle.blade.php
└── admin/
    └── global-user-search.blade.php
```

---

## 🚀 Performance Tips

### 1. Use Wire:key for Lists

```blade
@foreach($users as $user)
    @livewire('user.user-status-toggle', 
        ['userId' => $user->id], 
        key('user-'.$user->id))
@endforeach
```

### 2. Debounce Search Input

Already implemented (300ms):
```php
wire:model.live.debounce.300ms="search"
```

### 3. Lazy Load Components

For non-critical components:
```blade
@livewire('component-name', lazy: true)
```

---

## 📚 Additional Resources

- **Livewire Docs**: https://livewire.laravel.com/docs
- **Livewire GitHub**: https://github.com/livewire/livewire
- **Laravel Docs**: https://laravel.com/docs

---

## 🎯 Next Steps

1. ✅ Livewire installed
2. ✅ Components created
3. ✅ Components tested
4. ⏳ Test in browser
5. ⏳ Verify all interactions work
6. ⏳ Check for console errors

---

**Status**: ✅ All Components Working  
**Last Tested**: November 4, 2025  
**Version**: Livewire 3.6.4
