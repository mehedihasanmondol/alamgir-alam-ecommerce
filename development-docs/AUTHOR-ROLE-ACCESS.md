# 📝 Author Role - Access & Permissions

**Role:** Author  
**Purpose:** Blog content writers and contributors  
**Total Permissions:** 15  
**Last Updated:** November 17, 2025  

---

## ✅ What Authors CAN Access

### 1. Blog Posts Management
- ✅ **View all posts** (`posts.view`)
  - Route: `/admin/blog/posts`
  - Can see list of all blog posts
  
- ✅ **Create new posts** (`posts.create`)
  - Route: `/admin/blog/posts/create`
  - Can write and create new blog posts
  
- ✅ **Edit posts** (`posts.edit`)
  - Route: `/admin/blog/posts/{id}/edit`
  - Can edit existing blog posts
  
- ✅ **Publish posts** (`posts.publish`)
  - Can publish draft posts
  
- ✅ **Upload images** (`posts.upload-image`)
  - Can upload images via TinyMCE editor

### 2. Tick Marks Management
- ✅ **Manage tick marks** (`posts.tick-marks`)
  - Toggle verification badge
  - Toggle editor's choice badge
  - Toggle trending badge
  - Toggle premium badge
  - View tick mark statistics

### 3. Blog Categories (Read-Only)
- ✅ **View categories** (`blog-categories.view`)
  - Route: `/admin/blog/categories`
  - Can browse and view all blog categories
  - **Cannot create/edit/delete categories**

### 4. Blog Tags (Read-Only)
- ✅ **View tags** (`blog-tags.view`)
  - Route: `/admin/blog/tags`
  - Can browse and view all blog tags
  - **Cannot create/edit/delete tags**

### 5. Blog Comments Management
- ✅ **View comments** (`blog-comments.view`)
  - Route: `/admin/blog/comments`
  - Can see all comments on blog posts
  
- ✅ **Approve comments** (`blog-comments.approve`)
  - Can approve pending comments
  
- ✅ **Delete comments** (`blog-comments.delete`)
  - Can remove inappropriate comments

---

## ❌ What Authors CANNOT Access

### Blog Management
- ❌ **Delete posts** - Cannot delete blog posts
- ❌ **Create categories** - Cannot add new categories
- ❌ **Edit categories** - Cannot modify categories
- ❌ **Delete categories** - Cannot remove categories
- ❌ **Create tags** - Cannot add new tags
- ❌ **Edit tags** - Cannot modify tags
- ❌ **Delete tags** - Cannot remove tags

### Other Modules
- ❌ **User Management** - No access to users and roles
- ❌ **Product Management** - No access to products, categories, brands
- ❌ **Order Management** - No access to orders and customers
- ❌ **Delivery Management** - No access to delivery settings
- ❌ **Stock Management** - No access to inventory
- ❌ **Content Management** - No access to homepage settings
- ❌ **Finance** - No access to payments and reports
- ❌ **System Settings** - No access to site settings

---

## 🎯 Typical Author Workflow

### Writing a Blog Post

1. **Login to Admin Panel**
   - URL: `/admin/dashboard`
   - Sees "Dashboard" and "Blog" menu only

2. **View Posts**
   - Click "Blog" → "Posts"
   - Route: `/admin/blog/posts`
   - Can see all existing posts

3. **Create New Post**
   - Click "Create Post"
   - Route: `/admin/blog/posts/create`
   - Write content with TinyMCE editor
   - Upload images within the editor
   - Select existing categories and tags
   - **Cannot create new categories or tags**

4. **Add Tick Marks (Optional)**
   - Toggle verification badge
   - Toggle editor's choice
   - Toggle trending
   - Toggle premium

5. **Publish Post**
   - Save as draft or publish immediately
   - Route: POST `/admin/blog/posts/{id}/publish`

6. **Manage Comments**
   - View comments on your posts
   - Approve pending comments
   - Delete spam/inappropriate comments

---

## 📊 Menu Visibility for Authors

### Visible Menu Items
```
Admin Panel Sidebar:
├── 📊 Dashboard
└── 📝 Blog
    ├── Posts         ← Can view, create, edit
    ├── Categories    ← Can only view (read-only)
    ├── Tags          ← Can only view (read-only)
    └── Comments      ← Can view, approve, delete
```

### Hidden Menu Items
- User Management
- E-commerce (Products, Orders, etc.)
- Delivery & Shipping
- Inventory (Stock, Warehouses)
- Content (Homepage, Banners)
- Finance (Payments, Reports)
- System Settings

---

## 🔐 Permission List

| # | Permission Slug | Name | Access Level |
|---|----------------|------|--------------|
| 1 | `posts.view` | View Posts | Full |
| 2 | `posts.create` | Create Posts | Full |
| 3 | `posts.edit` | Edit Posts | Full |
| 4 | `posts.publish` | Publish Posts | Full |
| 5 | `posts.upload-image` | Upload Images | Full |
| 6 | `posts.tick-marks` | Manage Tick Marks | Full |
| 7 | `posts.toggle-verification` | Toggle Verification | Full |
| 8 | `posts.toggle-editor-choice` | Toggle Editor Choice | Full |
| 9 | `posts.toggle-trending` | Toggle Trending | Full |
| 10 | `posts.toggle-premium` | Toggle Premium | Full |
| 11 | `blog-categories.view` | View Categories | Read-Only |
| 12 | `blog-tags.view` | View Tags | Read-Only |
| 13 | `blog-comments.view` | View Comments | Full |
| 14 | `blog-comments.approve` | Approve Comments | Full |
| 15 | `blog-comments.delete` | Delete Comments | Full |

**Total: 15 Permissions**

---

## ✅ Verification Checklist

Test your author account:

### Should Work ✅
- [ ] Can access `/admin/dashboard`
- [ ] Can access `/admin/blog/posts`
- [ ] Can access `/admin/blog/posts/create`
- [ ] Can access `/admin/blog/posts/{id}/edit`
- [ ] Can access `/admin/blog/categories` (view only)
- [ ] Can access `/admin/blog/tags` (view only)
- [ ] Can access `/admin/blog/comments`
- [ ] Can upload images in post editor
- [ ] Can toggle tick marks on posts
- [ ] Can approve/delete comments

### Should NOT Work ❌
- [ ] Cannot access `/admin/users`
- [ ] Cannot access `/admin/products`
- [ ] Cannot access `/admin/orders`
- [ ] Cannot access `/admin/settings`
- [ ] Cannot delete blog posts
- [ ] Cannot create/edit categories
- [ ] Cannot create/edit tags

---

## 🚨 Common Issues & Solutions

### Issue: "403 Unauthorized" on Blog Pages
**Solution:** 
1. Routes updated to use permission-based middleware ✅
2. Run: `php artisan route:clear`
3. Run: `php artisan optimize:clear`
4. Refresh browser (hard refresh: Ctrl+F5)

### Issue: Can See Menu Items but Cannot Access
**Solution:**
1. Re-run seeder: `php artisan db:seed --class=RolePermissionSeeder`
2. Clear caches
3. Re-login to admin panel

### Issue: Need to Create Categories/Tags
**Solution:**
- Authors cannot create categories/tags
- Contact admin or content editor to create them
- Or upgrade role to "Content Editor" for full blog access

---

## 🆚 Author vs Content Editor

| Feature | Author | Content Editor |
|---------|--------|----------------|
| Create Posts | ✅ | ✅ |
| Edit Posts | ✅ | ✅ |
| Delete Posts | ❌ | ✅ |
| View Categories | ✅ | ✅ |
| Create Categories | ❌ | ✅ |
| Edit Categories | ❌ | ✅ |
| Delete Categories | ❌ | ✅ |
| View Tags | ✅ | ✅ |
| Create Tags | ❌ | ✅ |
| Edit Tags | ❌ | ✅ |
| Delete Tags | ❌ | ✅ |
| Manage Comments | ✅ | ✅ |
| Manage Homepage | ❌ | ✅ |
| Manage Banners | ❌ | ✅ |

**Recommendation:** Use Author role for guest writers, Content Editor for full-time content team.

---

## 📚 Related Documentation

- **Full Permission System:** `PERMISSION-SYSTEM-DOCUMENTATION.md`
- **Quick Summary:** `PERMISSION-SYSTEM-SUMMARY.md`
- **Seeder File:** `database/seeders/RolePermissionSeeder.php`

---

**Status:** ✅ Fully Functional  
**Fixed:** November 17, 2025  
**Issue Resolved:** Authors can now access blog management without 403 errors
