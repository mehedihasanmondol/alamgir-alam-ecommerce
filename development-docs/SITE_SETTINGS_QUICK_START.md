# Site Settings - Quick Start Guide

## 🚀 Quick Access

**Admin Panel URL**: `/admin/site-settings`

**Sidebar Navigation**: System → Site Settings

## 📋 What You Can Manage

### 🎨 Appearance
- ✅ Site Logo (Frontend)
- ✅ Admin Panel Logo
- ✅ Favicon
- ✅ Primary Brand Color

### 🏢 General Information
- ✅ Site Name
- ✅ Site Tagline
- ✅ Contact Email
- ✅ Contact Phone

### 📱 Social Media
- ✅ Facebook URL
- ✅ Twitter URL
- ✅ Instagram URL
- ✅ YouTube URL

### 🔍 SEO
- ✅ Meta Description
- ✅ Meta Keywords

## 🖼️ Logo Upload Instructions

### Step 1: Access Settings
1. Login to admin panel
2. Click **System** in sidebar
3. Click **Site Settings**

### Step 2: Upload Logo
1. Scroll to **Appearance Settings**
2. Find **Site Logo** section
3. Click **Choose File**
4. Select your logo image
5. Preview appears instantly
6. Click **Save Settings** at bottom

### Step 3: Verify
- Visit your frontend to see the new logo
- Logo appears in header automatically

## 📏 Recommended Sizes

| Logo Type | Recommended Size | Format |
|-----------|-----------------|--------|
| Site Logo | 200x60px | PNG, SVG |
| Admin Logo | 180x50px | PNG, SVG |
| Favicon | 32x32px | PNG, ICO |

## 🔄 Logo Behavior

### Frontend Header
```
IF logo uploaded:
    Display logo image
ELSE:
    Display site name as text
```

### Admin Header
```
IF admin logo uploaded:
    Display admin logo
ELSE:
    Display default "Admin Panel" text
```

## ⚡ Quick Commands

```bash
# Clear cache after changes
php artisan cache:clear

# Create storage link (first time only)
php artisan storage:link

# Re-seed default settings
php artisan db:seed --class=SiteSettingSeeder
```

## 🐛 Troubleshooting

### Logo not showing?
```bash
php artisan storage:link
php artisan cache:clear
```

### Can't upload images?
- Check file size (max 2MB)
- Check format (PNG, JPG, WEBP only)
- Verify storage permissions

### Settings not saving?
```bash
php artisan cache:clear
php artisan config:clear
```

## 💡 Pro Tips

1. **Use PNG with transparency** for logos
2. **Optimize images** before uploading (use TinyPNG)
3. **Test on mobile** after uploading
4. **Keep backup** of your logo files
5. **Use consistent branding** across site and admin logos

## 📞 Need Help?

Check the full guide: `SITE_LOGO_MANAGEMENT_GUIDE.md`

---

**Quick Reference Card** | Last Updated: Nov 11, 2025
