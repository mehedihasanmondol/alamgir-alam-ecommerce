# Social Auth Database Configuration Fix

## Problem Solved
1. **Group Issue**: Social auth settings moved from separate `social_auth` group into `login` group
2. **Config Issue**: Fixed `client_id` being empty when using database settings

## How It Works Now

### Service Provider Approach
Created `SocialAuthServiceProvider` that:
- Boots after the application starts
- Reads social auth settings from database using `SiteSetting::get()`
- Falls back to `.env` values if database settings are empty
- Dynamically configures Laravel Socialite with correct credentials

### Flow:
```
1. App boots → SocialAuthServiceProvider loads
2. Provider reads database settings (e.g., google_client_id)
3. If database setting exists → Use it
4. If empty → Fallback to .env GOOGLE_CLIENT_ID
5. Update config('services.google.client_id') dynamically
6. Socialite uses the updated config
```

## Admin Access

All social auth settings now in **Admin Panel > Site Settings > Login**:
- Google Client ID (order 10)
- Google Client Secret (order 11)
- Google Redirect URL (order 12)
- Facebook App ID (order 13)
- Facebook App Secret (order 14)
- Facebook Redirect URL (order 15)
- Apple Service ID (order 16)
- Apple Client Secret (order 17)
- Apple Redirect URL (order 18)

## Testing Steps

### Step 1: Remove .env Credentials (Optional)
You can remove these from `.env` to test database-only config:
```env
# GOOGLE_CLIENT_ID=...
# GOOGLE_CLIENT_SECRET=...
# GOOGLE_REDIRECT_URL=...
```

### Step 2: Add Credentials in Admin
1. Login to admin panel
2. Go to **Site Settings > Login**
3. Scroll to social auth fields
4. Enter your credentials:
   - **Google Client ID**: Your OAuth 2.0 Client ID
   - **Google Client Secret**: Your OAuth Client Secret
   - **Google Redirect URL**: Leave empty for auto (or enter custom)
5. Click **Save**

### Step 3: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### Step 4: Test Social Login
1. Go to login page
2. Click "Sign in with Google"
3. Should redirect to Google OAuth (not show error)
4. Complete OAuth flow
5. Should redirect back and login successfully

## Debugging

### Check if Settings Are Saved
```php
// In tinker: php artisan tinker
\App\Models\SiteSetting::get('google_client_id');
\App\Models\SiteSetting::get('google_client_secret');
```

### Check Config at Runtime
```php
// In tinker after app boot
config('services.google.client_id');
config('services.google.client_secret');
```

### Check Logs
If social login fails, check:
```
storage/logs/laravel.log
```

Look for:
- `SocialAuthServiceProvider: Could not configure socialite` - DB not ready
- Any Socialite errors

## Files Changed

### Created:
- `app/Providers/SocialAuthServiceProvider.php` - Dynamic config provider

### Modified:
- `bootstrap/providers.php` - Registered new provider
- `database/seeders/SiteSettingSeeder.php` - Moved settings to login group
- `config/services.php` - Reverted to simple .env config (provider handles DB)

## Benefits

1. **No Config Cache Issues**: Service provider runs after boot, not during config cache
2. **Database Priority**: Database settings override .env automatically
3. **Graceful Fallback**: Falls back to .env if database empty
4. **Admin Friendly**: All settings in one group (Login)
5. **Error Handling**: Silently fails during migrations when DB not ready

## Migration Notes

If you had credentials in `.env`:
- ✅ They still work as fallback
- ✅ Database settings take priority when filled
- ✅ You can keep both for redundancy

If you only use database:
- ✅ Remove .env credentials
- ✅ All settings managed via admin panel
- ✅ Clear cache after saving settings

## Security

- Database settings should be encrypted at rest
- Only admin users can access settings
- Settings cached for performance
- No credentials exposed in frontend

## Troubleshooting

### Issue: Still getting "Missing required parameter: client_id"

**Solution 1**: Clear all caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
```

**Solution 2**: Check if settings saved
```bash
php artisan tinker
>>> \App\Models\SiteSetting::where('key', 'like', 'google_%')->get()
```

**Solution 3**: Check service provider loaded
```bash
php artisan about
# Look for SocialAuthServiceProvider in providers list
```

### Issue: Settings show in admin but don't work

**Cause**: Cache not cleared after saving  
**Solution**: Always run `php artisan config:clear` after updating settings

### Issue: Redirect URL mismatch

**Check**:
1. Database setting `google_redirect_url`
2. If empty, should use: `{APP_URL}/login/google/callback`
3. Must match exactly in Google Cloud Console

---

**Last Updated**: 2025-11-19  
**Status**: Production Ready ✅
