# 🚀 WordPress Migration - Quick Start

## ⚡ Fastest Way to Migrate

### Step 1: Get WooCommerce API Keys

1. Go to WordPress Admin: `https://prokriti.org/wp-admin`
2. Navigate to: **WooCommerce** → **Settings** → **Advanced** → **REST API**
3. Click **"Add Key"**
4. Set:
   - **Description**: Laravel Migration
   - **User**: Select admin user  
   - **Permissions**: **Read**
5. Click **"Generate API Key"**
6. **Copy** the Consumer Key and Consumer Secret

### Step 2: Add to .env File

```env
WORDPRESS_DOMAIN=https://prokriti.org
WOOCOMMERCE_KEY=ck_xxxxxxxxxxxxxxxxxxxxx
WOOCOMMERCE_SECRET=cs_xxxxxxxxxxxxxxxxxxxxx
```

### Step 3: Run Migration

```bash
# Ensure storage is linked
php artisan storage:link

# Run migration seeder
php artisan db:seed --class=WordPressMigrationSeeder
```

That's it! ✨

---

## 🎯 What Happens

The migration will:

1. ✅ **Fetch** all users, categories, tags from WordPress
2. ✅ **Download** all images to `/storage/wordpress`
3. ✅ **Migrate** all blog posts with categories, tags, and authors
4. ✅ **Migrate** all WooCommerce products with variants and images
5. ✅ **Replace** all WordPress image URLs with new Laravel URLs
6. ✅ **Preserve** SEO meta data, slugs, and permalinks

---

## 📊 Expected Results

### Blog Posts
- **Location**: `/admin/blog/posts`
- **Includes**: Title, content, featured image, categories, tags, SEO meta
- **Images**: Downloaded to `storage/app/public/wordpress/YYYY/MM/`

### Products
- **Location**: `/admin/products`
- **Includes**: Name, description, images, variants, SKU, price, stock
- **Images**: Gallery images and variant images

### Images
- **Storage**: `storage/app/public/wordpress/`
- **Structure**: Organized by year/month (2024/01/, 2024/02/, etc.)
- **URL Format**: `https://your-domain.com/storage/wordpress/2024/08/image.jpg`

---

## ⚠️ Important Notes

### 1. User Emails
Migrated users will have emails like: `username@migrated.local`

**Action**: Update emails manually in admin panel after migration.

### 2. Migration is Safe
- Runs in database transaction (rolls back on error)
- Idempotent (can run multiple times safely)
- Uses `updateOrCreate` (no duplicates)

### 3. Large Sites
For sites with 1000+ posts/products:
```bash
php artisan wordpress:migrate --batch-size=5
```

---

## 🔧 Customization Options

### Migrate Only Blog Posts
```bash
php artisan wordpress:migrate --only-posts
```

### Migrate Only Products
```bash
php artisan wordpress:migrate \
  --wc-key=ck_xxx \
  --wc-secret=cs_xxx \
  --only-products
```

### Skip Images (Testing)
```bash
php artisan wordpress:migrate --skip-images
```

### Custom Batch Size
```bash
php artisan wordpress:migrate --batch-size=50
```

### Resume from Specific Page
```bash
php artisan wordpress:migrate --start-from=10
```

---

## 🐛 Troubleshooting

### Error: "WooCommerce API request failed: Unauthorized"
**Fix**: Verify WooCommerce API keys in `.env`

### Error: "Failed to download image"
**Fix**: Check image URLs are accessible. Run with `--skip-images` to test without images first.

### Error: "Failed to fetch users from WordPress"
**Fix**: Verify WordPress REST API is enabled at `https://prokriti.org/wp-json`

---

## ✅ Post-Migration Tasks

- [ ] Review posts in `/admin/blog/posts`
- [ ] Review products in `/admin/products`
- [ ] Check images in `storage/app/public/wordpress/`
- [ ] Update user email addresses
- [ ] Clear caches: `php artisan optimize:clear`
- [ ] Test frontend display

---

## 📚 Full Documentation

For complete details, see: `development-docs/WORDPRESS_MIGRATION_GUIDE.md`

---

**Need Help?**

1. Check logs: `storage/logs/laravel.log`
2. Enable debug: `APP_DEBUG=true` in `.env`
3. Run help: `php artisan wordpress:migrate --help`

---

**Last Updated**: November 28, 2025  
**Status**: Production Ready ✅
