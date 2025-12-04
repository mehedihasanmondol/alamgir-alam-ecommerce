# WordPress/WooCommerce Migration Guide

## 📋 Overview

This guide covers the complete migration process from WordPress/WooCommerce to your Laravel platform. The migration system is designed to be:

- ✅ **Safe**: Idempotent migrations (can run multiple times safely)
- ✅ **Complete**: Migrates posts, products, images, categories, tags, and authors
- ✅ **SEO-Friendly**: Preserves slugs, meta data, and permalinks
- ✅ **Automated**: Downloads and replaces image URLs automatically
- ✅ **Flexible**: Customizable options for different scenarios

---

## 🚀 Quick Start

### Prerequisites

1. **WooCommerce API Credentials** (for product migration)
   - Go to: WordPress Admin → WooCommerce → Settings → Advanced → REST API
   - Click "Add Key"
   - Description: "Laravel Migration"
   - User: Select admin user
   - Permissions: **Read**
   - Copy the **Consumer Key** and **Consumer Secret**

2. **Storage Setup**
   ```bash
   # Ensure storage is linked
   php artisan storage:link
   
   # Create wordpress directory
   mkdir -p storage/app/public/wordpress
   ```

3. **Environment Variables** (Optional)
   Add to `.env`:
   ```env
   WORDPRESS_DOMAIN=https://prokriti.org
   WOOCOMMERCE_KEY=ck_xxxxxxxxxxxxxxxxxxxxx
   WOOCOMMERCE_SECRET=cs_xxxxxxxxxxxxxxxxxxxxx
   ```

---

## 🎯 Migration Methods

### Method 1: Using Seeder (Recommended)

```bash
php artisan db:seed --class=WordPressMigrationSeeder
```

**Prompts you will see:**
1. Confirm WordPress domain
2. Enter WooCommerce credentials (if not in .env)
3. Choose migration options

### Method 2: Using Artisan Command (Advanced)

**Full Migration:**
```bash
php artisan wordpress:migrate \
  --domain=https://prokriti.org \
  --wc-key=ck_xxxxxxx \
  --wc-secret=cs_xxxxxxx
```

**Blog Posts Only:**
```bash
php artisan wordpress:migrate \
  --domain=https://prokriti.org \
  --only-posts
```

**Products Only:**
```bash
php artisan wordpress:migrate \
  --domain=https://prokriti.org \
  --wc-key=ck_xxxxxxx \
  --wc-secret=cs_xxxxxxx \
  --only-products
```

**Skip Images (Testing):**
```bash
php artisan wordpress:migrate \
  --domain=https://prokriti.org \
  --skip-images
```

**Custom Batch Size:**
```bash
php artisan wordpress:migrate \
  --domain=https://prokriti.org \
  --batch-size=50 \
  --start-from=1
```

---

## 📊 What Gets Migrated?

### ✅ Blog Posts
- Title, content, excerpt
- Author (mapped to Laravel users)
- Categories (many-to-many relationship)
- Tags (many-to-many relationship)
- Featured image
- All images in content (downloaded and URLs replaced)
- Publish date and status
- SEO meta (title, description, keywords)
- Slug (preserved for SEO)
- Comment settings

### ✅ WooCommerce Products
- Product name, description, short description
- Product type (simple, variable, grouped, affiliate)
- Brand (created if doesn't exist)
- Categories (many-to-many relationship)
- Product images (all gallery images)
- Product variants with:
  - SKU
  - Price and sale price
  - Stock quantity and status
  - Dimensions (weight, length, width, height)
  - Variant image
- External URL and button text (for affiliate products)
- Publish status
- Featured status
- SEO meta data

### ✅ Categories & Tags
- **Blog Categories**: Mapped to `blog_categories` table
- **Product Categories**: Mapped to `categories` table (with parent relationship)
- **Tags**: Mapped to `blog_tags` table
- All include SEO meta data

### ✅ Authors/Users
- Username → Name
- WordPress ID → Laravel User ID mapping
- Random secure passwords (must reset after migration)
- Email format: `{username}@migrated.local` (WordPress REST API doesn't expose real emails)

### ✅ Images
- **Download Location**: `storage/app/public/wordpress/YYYY/MM/filename.ext`
- **Media Records**: Created in `media` table for all images
- **URL Replacement**: 
  - Old: `https://prokriti.org/wp-content/uploads/2024/08/image.jpg`
  - New: `https://your-domain.com/storage/wordpress/2024/08/image.jpg`
- **Image Types**:
  - Featured images (posts and products)
  - Gallery images (products)
  - Content images (inside post/product descriptions)

---

## 🔧 Command Options

```bash
php artisan wordpress:migrate --help
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `--domain` | string | `https://prokriti.org` | WordPress site domain |
| `--wc-key` | string | - | WooCommerce Consumer Key |
| `--wc-secret` | string | - | WooCommerce Consumer Secret |
| `--only-posts` | flag | false | Migrate only blog posts |
| `--only-products` | flag | false | Migrate only products |
| `--skip-images` | flag | false | Skip image download (for testing) |
| `--batch-size` | int | 10 | Items to process per batch |
| `--start-from` | int | 1 | Page number to start from (resume) |

---

## 📝 Migration Process Flow

### 1. Authors/Users Migration
```
WordPress API → Fetch Users
              ↓
    Create/Update Laravel Users
              ↓
    Build User ID Mapping
```

### 2. Categories & Tags Migration
```
WordPress API → Fetch Categories & Tags
              ↓
    Create Blog Categories
              ↓
    Create Product Categories
              ↓
    Create Tags
              ↓
    Build Category/Tag Mappings
```

### 3. Blog Posts Migration
```
For Each Post:
  ├─ Download Featured Image
  ├─ Find & Download Content Images
  ├─ Replace Image URLs
  ├─ Create Post Record
  ├─ Attach Categories (many-to-many)
  └─ Attach Tags (many-to-many)
```

### 4. Products Migration
```
For Each Product:
  ├─ Create/Find Brand
  ├─ Download Product Images
  ├─ Find & Download Description Images
  ├─ Replace Image URLs
  ├─ Create Product Record
  ├─ Create Default Variant
  ├─ Attach Categories (many-to-many)
  ├─ Attach Product Images
  └─ If Variable:
      └─ Fetch & Create All Variations
```

---

## 🎨 Image Handling

### Download Strategy
1. Check if image already downloaded (cached mapping)
2. Download image via HTTP
3. Generate SEO-friendly filename
4. Store in `storage/app/public/wordpress/YYYY/MM/`
5. Create Media record
6. Cache URL → Media ID mapping

### URL Replacement
**Before:**
```html
<img src="https://prokriti.org/wp-content/uploads/2024/08/health-product.jpg" alt="Health Product">
```

**After:**
```html
<img src="https://your-domain.com/storage/wordpress/2024/08/health-product.jpg" alt="Health Product">
```

### Storage Structure
```
storage/app/public/
└── wordpress/
    ├── 2024/
    │   ├── 01/
    │   │   ├── image1.jpg
    │   │   ├── image2.png
    │   │   └── image3.webp
    │   ├── 02/
    │   └── ...
    └── 2025/
        ├── 01/
        └── ...
```

---

## ⚠️ Important Notes

### 1. User Emails
**Problem**: WordPress REST API doesn't expose user email addresses.

**Solution**: Migrated users get emails like: `{username}@migrated.local`

**Action Required**:
```bash
# After migration, update user emails manually in admin panel
# Or use Laravel tinker:
php artisan tinker
>>> User::where('email', 'like', '%@migrated.local')->get();
```

### 2. WooCommerce Authentication
**Required**: Consumer Key and Consumer Secret with **Read** permissions.

**If credentials missing**: Product migration will be skipped.

### 3. Idempotent Migrations
The migration uses `updateOrCreate` which means:
- ✅ Running multiple times is safe
- ✅ Existing records will be updated
- ✅ No duplicates will be created
- ⚠️ Local changes may be overwritten

**Recommendation**: Run migration to a fresh database first, then manually merge if needed.

### 4. Large Migrations
For sites with 1000+ posts/products:

```bash
# Use smaller batch sizes to avoid memory issues
php artisan wordpress:migrate --batch-size=5

# Resume from specific page if interrupted
php artisan wordpress:migrate --start-from=10
```

### 5. Image Download Timeouts
If images fail to download:
- Check image URLs are accessible
- Increase timeout in command (edit `Http::timeout(30)` to higher value)
- Run with `--skip-images` first, then download images separately

---

## 🔍 Troubleshooting

### Error: "Failed to fetch users from WordPress"
**Cause**: WordPress REST API is disabled or blocked.

**Solution**:
```bash
# Test WordPress API
curl https://prokriti.org/wp-json/wp/v2/users

# If returns error, check WordPress settings
# Or use custom endpoint
```

### Error: "WooCommerce API request failed: Unauthorized"
**Cause**: Invalid or missing WooCommerce credentials.

**Solution**:
1. Verify Consumer Key and Secret
2. Ensure permissions are set to "Read"
3. Test with curl:
```bash
curl -u "ck_xxx:cs_xxx" https://prokriti.org/wp-json/wc/v3/products
```

### Error: "Failed to download image"
**Cause**: Image URL is 404, blocked, or timeout.

**Solution**:
- Check image exists: `curl -I {image-url}`
- Verify no hotlink protection
- Increase timeout in command code

### Error: "Class 'Media' not found"
**Cause**: Media model doesn't exist.

**Solution**:
Check if you have `App\Models\Media` model. If not, the migration needs adjustment to use direct file storage instead of Media model.

### Warning: "Failed to migrate post ID X"
**Cause**: Individual post has invalid data or missing required fields.

**Action**: Migration continues with other posts. Review specific post manually.

---

## 📈 Performance Tips

### 1. Batch Processing
```bash
# Small batches = More reliable, slower
php artisan wordpress:migrate --batch-size=5

# Large batches = Faster, more memory usage
php artisan wordpress:migrate --batch-size=50
```

### 2. Resume Capability
```bash
# If migration stops at page 8, resume from there
php artisan wordpress:migrate --start-from=8
```

### 3. Separate Migrations
```bash
# Day 1: Migrate posts
php artisan wordpress:migrate --only-posts

# Day 2: Migrate products
php artisan wordpress:migrate --only-products
```

### 4. Test First
```bash
# Test without downloading images (very fast)
php artisan wordpress:migrate --skip-images --batch-size=3

# Review results, then run full migration
php artisan wordpress:migrate
```

---

## ✅ Post-Migration Checklist

- [ ] Review migrated posts in `/admin/blog/posts`
- [ ] Review migrated products in `/admin/products`
- [ ] Check images in `storage/app/public/wordpress/`
- [ ] Verify image URLs in content are working
- [ ] Update user email addresses
- [ ] Reset user passwords
- [ ] Review and adjust category assignments
- [ ] Verify tag assignments
- [ ] Check SEO meta data (titles, descriptions)
- [ ] Test product variants and pricing
- [ ] Verify product images and galleries
- [ ] Check external product links (affiliate products)
- [ ] Review slugs and permalinks
- [ ] Test 301 redirects from old URLs (if needed)
- [ ] Clear all caches: `php artisan optimize:clear`
- [ ] Reindex search (if you have search functionality)

---

## 🔄 Re-running Migration

The migration is **idempotent**, meaning you can run it multiple times:

```bash
# This will update existing records, not duplicate them
php artisan db:seed --class=WordPressMigrationSeeder
```

**What happens:**
- Existing posts/products matched by `slug` will be **updated**
- New posts/products will be **created**
- Images already downloaded will be **skipped** (cached)
- Categories/tags will be **synced** (not duplicated)

**Use Cases:**
- WordPress content was updated after initial migration
- Initial migration failed partway through
- Want to sync new content from WordPress

---

## 🛡️ Safety Measures

### 1. Database Transaction
The entire migration runs in a transaction:
- ✅ If any error occurs, entire migration rolls back
- ✅ Database stays consistent
- ❌ No partial data

### 2. Error Handling
Individual item failures don't stop migration:
```
✓ Post 1 migrated
✓ Post 2 migrated
⚠ Post 3 failed: Invalid data
✓ Post 4 migrated
```

### 3. Dry Run (Testing)
```bash
# Test migration without downloading images
php artisan wordpress:migrate --skip-images --batch-size=2
```

---

## 📞 Support

If you encounter issues:

1. **Check Logs**: `storage/logs/laravel.log`
2. **Enable Debug**: Set `APP_DEBUG=true` in `.env`
3. **Test API**: Use curl to verify WordPress/WooCommerce APIs
4. **Review Command**: `php artisan wordpress:migrate --help`
5. **Check Documentation**: This file

---

## 🎯 Example: Complete Migration

```bash
# Step 1: Set up environment
cp .env.example .env
# Edit .env and add:
# WORDPRESS_DOMAIN=https://prokriti.org
# WOOCOMMERCE_KEY=ck_xxxxx
# WOOCOMMERCE_SECRET=cs_xxxxx

# Step 2: Ensure storage is ready
php artisan storage:link
mkdir -p storage/app/public/wordpress

# Step 3: Test connection (skip images)
php artisan wordpress:migrate --skip-images --batch-size=2

# Step 4: Review test results
# - Check admin panel
# - Verify data looks correct

# Step 5: Run full migration
php artisan wordpress:migrate --batch-size=10

# Step 6: Post-migration tasks
# - Update user emails
# - Clear caches
# - Test frontend

# Step 7: Celebrate! 🎉
```

---

## 📝 Migration Log Example

```
🚀 Starting WordPress Migration from: https://prokriti.org

✓ Migrating WordPress Authors
  Found 3 authors
    ✓ Migrated: Rabiul → User ID: 1
    ✓ Migrated: Admin → User ID: 2
    ✓ Migrated: Author → User ID: 3

✓ Migrating Categories & Tags
  📁 Migrating Blog Categories...
    ✓ Health Tips → ID: 1
    ✓ Wellness → ID: 2
  🏷️  Migrating Tags...
    ✓ Nutrition → ID: 1
    ✓ Fitness → ID: 2
  📦 Migrating Product Categories...
    ✓ Supplements → ID: 1
    ✓ Vitamins → ID: 2

📝 Migrating Blog Posts...
  ========== 10/10 [############################] 100%
  ✓ Page 1 completed (10 posts so far)
  ========== 5/5 [############################] 100%
  ✓ Page 2 completed (15 posts so far)
✅ Migrated 15 blog posts

🛒 Migrating WooCommerce Products...
  ========== 10/10 [############################] 100%
  ✓ Page 1 completed (10 products so far)
✅ Migrated 10 products

✅ Migration completed successfully!

📊 Migration Statistics:
+----------------------+-------+
| Item                 | Count |
+----------------------+-------+
| Users Migrated       | 3     |
| Blog Categories      | 2     |
| Product Categories   | 2     |
| Tags                 | 2     |
| Images Downloaded    | 45    |
| Total Posts          | 15    |
| Total Products       | 10    |
+----------------------+-------+
```

---

**Last Updated**: November 28, 2025  
**Version**: 1.0.0  
**Status**: Production Ready ✅
