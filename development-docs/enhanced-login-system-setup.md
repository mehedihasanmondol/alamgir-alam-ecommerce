# Enhanced Login System Setup Guide

## Overview
This document guides you through setting up the enhanced multi-step login system with social authentication (Google, Facebook) and comprehensive settings management.

---

## Features Implemented

### 1. Multi-Step Login Flow
- **Step 1**: User enters email or mobile number
- **Step 2**: 
  - **Existing users**: Enter password to login
  - **New users**: Enter name and create password to register
- Mobile number validation (10-15 digits)
- Email validation
- Default user role: `customer`

### 2. Social Login Integration
- Google OAuth2
- Facebook OAuth2
- Apple Sign In (optional, disabled by default)
- Auto-registration for new social users
- Auto-linking social accounts to existing email accounts

### 3. Admin Settings
New settings available in **Site Settings > Login** group:
- `login_page_title` - Right side title (text)
- `login_page_content` - Right side content (TinyMCE)
- `enable_google_login` - Enable/disable Google login (boolean)
- `enable_facebook_login` - Enable/disable Facebook login (boolean)
- `enable_apple_login` - Enable/disable Apple login (boolean, default: off)
- `login_terms_conditions` - Terms & conditions text (TinyMCE)
- `login_help_enabled` - Show/hide help link (boolean)
- `login_help_url` - Help page URL (text)
- `login_help_text` - Help link text (text)

---

## Setup Instructions

### Step 1: Run Database Seeder
Update the site settings with new login configurations:

```bash
php artisan db:seed --class=SiteSettingSeeder
```

### Step 2: Configure Environment Variables
Add the following to your `.env` file:

```env
# Google OAuth
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URL="${APP_URL}/login/google/callback"

# Facebook OAuth
FACEBOOK_CLIENT_ID=your-facebook-app-id
FACEBOOK_CLIENT_SECRET=your-facebook-app-secret
FACEBOOK_REDIRECT_URL="${APP_URL}/login/facebook/callback"

# Apple Sign In (Optional)
APPLE_CLIENT_ID=your-apple-client-id
APPLE_CLIENT_SECRET=your-apple-client-secret
APPLE_REDIRECT_URL="${APP_URL}/login/apple/callback"
```

### Step 3: Clear Application Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## Social Login Setup

### Google OAuth Setup

1. **Go to Google Cloud Console**: https://console.cloud.google.com/
2. **Create a new project** or select existing
3. **Enable Google+ API**:
   - Navigate to "APIs & Services" > "Library"
   - Search for "Google+ API"
   - Click "Enable"
4. **Create OAuth 2.0 Credentials**:
   - Go to "APIs & Services" > "Credentials"
   - Click "Create Credentials" > "OAuth client ID"
   - Application type: "Web application"
   - Add authorized redirect URIs:
     - `http://localhost:8000/login/google/callback` (for local)
     - `https://yourdomain.com/login/google/callback` (for production)
5. **Copy credentials** to `.env`:
   - Client ID → `GOOGLE_CLIENT_ID`
   - Client Secret → `GOOGLE_CLIENT_SECRET`

### Facebook OAuth Setup

1. **Go to Facebook Developers**: https://developers.facebook.com/
2. **Create a new app** or select existing
3. **Add Facebook Login product**:
   - Dashboard > Add Product > Facebook Login > Set Up
4. **Configure OAuth Settings**:
   - Settings > Basic > Copy App ID and App Secret
   - Settings > Facebook Login > Valid OAuth Redirect URIs:
     - `http://localhost:8000/login/facebook/callback` (for local)
     - `https://yourdomain.com/login/facebook/callback` (for production)
5. **Copy credentials** to `.env`:
   - App ID → `FACEBOOK_CLIENT_ID`
   - App Secret → `FACEBOOK_CLIENT_SECRET`

### Apple Sign In Setup (Optional)

1. **Go to Apple Developer**: https://developer.apple.com/
2. **Create App ID**:
   - Certificates, Identifiers & Profiles > Identifiers
   - Register a new App ID
   - Enable "Sign in with Apple"
3. **Create Service ID**:
   - Create a new Services ID
   - Configure "Sign in with Apple"
   - Add return URLs (callback URLs)
4. **Create Key**:
   - Create a new Key with "Sign in with Apple" enabled
   - Download the key file (`.p8`)
5. **Copy credentials** to `.env`

---

## Admin Configuration

### 1. Enable/Disable Social Login Providers
Navigate to **Admin Panel > Settings > Site Settings > Login**:
- Toggle "Enable Google Login"
- Toggle "Enable Facebook Login"
- Toggle "Enable Apple Login" (disabled by default)

### 2. Customize Login Page Content
Edit the following settings:
- **Login Page Title**: Title for right side section
- **Login Page Content**: Rich HTML content (features, benefits)
- **Terms & Conditions Text**: Legal disclaimer with links
- **Help Link**: Configure help URL and text

### 3. Terms & Conditions Setup
The default terms text includes:
```html
<p>By continuing, you've read and agree to our 
<a href="/terms-and-conditions">Terms and Conditions</a> and 
<a href="/privacy-policy">Privacy Policy</a>.</p>
```

**Update URLs** to match your actual pages or create:
- `/terms-and-conditions` page
- `/privacy-policy` page

---

## Validation Rules

### Email/Mobile Validation
- **Email**: Must be valid email format
- **Mobile**: Must be 10-15 digits, numbers only
- Both are unique per user

### Password Requirements
- **Login**: Minimum 6 characters
- **Registration**: Minimum 8 characters + confirmation required

### Name Requirement
- Required for new user registration
- Maximum 255 characters

---

## User Roles

### Default Role Assignment
- **Manual Registration**: `customer` role
- **Social Login**: `customer` role
- **Email Verified**: Automatically verified for social login

### Role-Based Redirects
After successful login:
- **Customer**: → `/my-account/profile`
- **Admin/Author**: → `/admin/dashboard`
- **Default**: → `/` (homepage)

---

## Files Created/Modified

### New Files
1. `app/Http/Controllers/Auth/SocialLoginController.php`
2. `app/Livewire/Auth/MultiStepLogin.php`
3. `resources/views/livewire/auth/multi-step-login.blade.php`
4. `development-docs/enhanced-login-system-setup.md`

### Modified Files
1. `database/seeders/SiteSettingSeeder.php` - Added login settings
2. `resources/views/auth/login.blade.php` - Integrated Livewire component
3. `routes/web.php` - Added social login routes
4. `config/services.php` - Added social provider configs

---

## Testing Checklist

### Manual Registration Flow
- [ ] Enter valid email → Check user exists → Login with password
- [ ] Enter valid email → New user → Enter name + password → Register
- [ ] Enter valid mobile → Check user exists → Login with password
- [ ] Enter valid mobile → New user → Enter name + password → Register

### Social Login Flow
- [ ] Google login → Existing user → Link account
- [ ] Google login → New user → Auto-register
- [ ] Facebook login → Existing user → Link account
- [ ] Facebook login → New user → Auto-register

### Validation Testing
- [ ] Invalid email format → Error message
- [ ] Invalid mobile (letters) → Error message
- [ ] Short mobile (< 10 digits) → Error message
- [ ] Long mobile (> 15 digits) → Error message
- [ ] Wrong password → Error message
- [ ] Password mismatch (registration) → Error message
- [ ] Password too short (< 8 chars registration) → Error message

### Settings Testing
- [ ] Disable Google login → Button hidden
- [ ] Disable Facebook login → Button hidden
- [ ] Disable Apple login → Button hidden (default)
- [ ] Update terms & conditions → Changes appear
- [ ] Disable help link → Link hidden
- [ ] Change help URL → Link updates

---

## Security Considerations

### Password Security
- Passwords are hashed using Laravel's default bcrypt
- Social login users get random 32-character passwords
- Minimum password requirements enforced

### Session Management
- Sessions regenerated after login
- CSRF protection enabled
- "Keep me signed in" uses Laravel's remember token

### Data Validation
- All inputs validated server-side
- Email uniqueness enforced at database level
- Mobile uniqueness enforced at database level

---

## Troubleshooting

### Issue: Social Login Not Working
**Solution**: 
1. Verify credentials in `.env`
2. Check callback URLs match in provider settings
3. Ensure `laravel/socialite` is installed: `composer require laravel/socialite`
4. Clear config cache: `php artisan config:clear`

### Issue: "User already exists" Error
**Solution**: 
- System automatically links social accounts to existing emails
- If error persists, check database for duplicate entries

### Issue: Settings Not Appearing
**Solution**:
1. Run seeder: `php artisan db:seed --class=SiteSettingSeeder`
2. Clear cache: `php artisan cache:clear`
3. Check `site_settings` table for entries

### Issue: Validation Not Working
**Solution**:
- Livewire validation runs on submit
- Check browser console for JavaScript errors
- Ensure Livewire assets are published

---

## Next Steps

### Recommended Enhancements
1. **Email Verification**: Add email verification for manual registrations
2. **Password Reset**: Implement forgot password functionality
3. **SMS Verification**: Add OTP verification for mobile registrations
4. **Two-Factor Auth**: Add 2FA support for enhanced security

### Pages to Create
1. `/help/login` - Login help page
2. `/terms-and-conditions` - Terms page
3. `/privacy-policy` - Privacy policy page
4. `/forgot-password` - Password reset page

---

## Support

For issues or questions:
- Check Laravel documentation: https://laravel.com/docs
- Check Socialite documentation: https://laravel.com/docs/socialite
- Review this documentation
- Check application logs: `storage/logs/laravel.log`

---

**Last Updated**: 2025-11-19
**Version**: 1.0
