# Footer Toggle Testing Guide

## What Was Fixed

### 1. Removed Invalid clearCache() Call
**Issue**: Controller was calling `FooterSetting::clearCache()` which doesn't exist
**Fix**: Removed the call from `FooterManagementController::toggleSection()`

### 2. Added Error Handling
**Issue**: Errors weren't being caught or logged properly
**Fix**: Added try-catch block with detailed error messages

### 3. Added Debug Logging
**Issue**: Hard to diagnose what's failing
**Fix**: Added console.log and better error messages

---

## Testing Steps

### 1. Check Database
Make sure the settings exist:
```sql
SELECT * FROM footer_settings WHERE `group` = 'footer_sections';
```

Expected results (5 rows):
- wellness_hub_section_enabled
- value_guarantee_section_enabled
- footer_links_section_enabled
- social_media_section_enabled
- newsletter_section_enabled

### 2. Check Browser Console
1. Open Developer Tools (F12)
2. Go to Console tab
3. Go to `/admin/footer-management`
4. Click on any tab (General Settings, Blog Posts, etc.)
5. Toggle the switch
6. Check console for:
   - "Toggling section: [section_name]"
   - Any error messages

### 3. Check Network Tab
1. Open Developer Tools (F12)
2. Go to Network tab
3. Filter by "Fetch/XHR"
4. Toggle the switch
5. Look for request to `footer-management/toggle-section`
6. Check:
   - Request payload (should have section_key and enabled)
   - Response (should be JSON with success: true)
   - Status code (should be 200, not 422 or 500)

### 4. Check Laravel Logs
If toggle still fails, check:
```bash
tail -f storage/logs/laravel.log
```

Look for:
- "Footer toggle error:"
- Validation errors
- Database errors

---

## Common Issues & Solutions

### Issue: "Failed to update section"
**Causes**:
1. CSRF token mismatch → Refresh page
2. Route not found → Run `php artisan route:clear`
3. Permission denied → Check user has `users.view` permission
4. Database error → Check `footer_settings` table exists

### Issue: Toggle switches back immediately
**Causes**:
1. JavaScript error → Check browser console
2. AJAX request failing → Check network tab
3. Validation failing → Check response in network tab

### Issue: "Validation failed"
**Causes**:
1. section_key not being sent → Check console.log output
2. enabled value not boolean → Alpine.js issue

---

## Quick Fix Commands

```bash
# Clear all caches
php artisan optimize:clear

# Clear views only
php artisan view:clear

# Check routes
php artisan route:list --name=footer

# Check database
php artisan tinker
>>> \App\Models\FooterSetting::where('group', 'footer_sections')->get();
```

---

## Expected Behavior

1. Visit `/admin/footer-management`
2. Go to "General Settings" tab
3. See "Newsletter Section" toggle card at top
4. Toggle switch ON/OFF
5. See toast: "Section enabled/disabled on footer!"
6. No console errors
7. Network request shows 200 status
8. Database value changes ('1' or '0')
9. Frontend footer shows/hides section

---

## Debugging Checklist

- [ ] Migration ran successfully
- [ ] 5 settings exist in database
- [ ] Route exists: `php artisan route:list --name=footer-management.toggle`
- [ ] CSRF token present in page source
- [ ] User has permission: `users.view`
- [ ] Browser console shows no errors
- [ ] Network tab shows 200 response
- [ ] Toast notification component loaded
- [ ] Alpine.js loaded correctly
- [ ] FooterSetting model exists
- [ ] Controller method works

---

## Test with Tinker

```php
php artisan tinker

// Check if setting exists
$setting = \App\Models\FooterSetting::where('key', 'newsletter_section_enabled')->first();
dd($setting);

// Manual toggle
\App\Models\FooterSetting::updateOrCreate(
    ['key' => 'newsletter_section_enabled'],
    ['value' => '0', 'group' => 'footer_sections', 'type' => 'boolean']
);

// Verify
$setting = \App\Models\FooterSetting::where('key', 'newsletter_section_enabled')->first();
dd($setting->value); // Should be '0'
```

---

## If Still Failing

1. Check Laravel log: `storage/logs/laravel.log`
2. Enable query log to see SQL errors
3. Check Apache/Nginx error log
4. Verify database connection
5. Try manual CURL request:

```bash
curl -X POST http://localhost/admin/footer-management/toggle-section \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: YOUR_TOKEN_HERE" \
  -d '{"section_key":"newsletter_section_enabled","enabled":true}'
```
