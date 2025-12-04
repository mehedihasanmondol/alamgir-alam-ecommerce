# Unlisted Blog Post Status Implementation

## Overview
Implemented a new 'Unlisted' status for blog posts that allows posts to be viewable via direct link but hidden from all frontend lists (blog index, category pages, tag pages, search results, featured posts, popular posts, etc.).

## Implementation Date
November 24, 2025

---

## Changes Made

### 1. Database Migration
**File:** `database/migrations/2025_11_24_000001_add_unlisted_status_to_blog_posts.php`

- Modified `blog_posts` table status enum to include 'unlisted'
- Status options: `draft`, `published`, `scheduled`, `unlisted`
- Migration includes rollback functionality that converts unlisted posts to draft

### 2. Validation Rules
**Files:**
- `app/Modules/Blog/Requests/StorePostRequest.php`
- `app/Modules/Blog/Requests/UpdatePostRequest.php`

- Updated status validation to accept 'unlisted' as valid option
- Validation rule: `'status' => 'required|in:draft,published,scheduled,unlisted'`

### 3. Post Model
**File:** `app/Modules/Blog/Models/Post.php`

- Added `isUnlisted()` helper method to check if post is unlisted
- Existing `scopePublished()` already excludes unlisted posts (only selects 'published' status)
- Updated comments to clarify unlisted behavior

### 4. Post Repository
**File:** `app/Modules/Blog/Repositories/PostRepository.php`

- Updated `getCountByStatus()` to include unlisted count
- Returns count for all statuses: all, published, draft, scheduled, unlisted

### 5. Post Service
**File:** `app/Modules/Blog/Services/PostService.php`

- Updated `getPostBySlug()` comment to clarify that unlisted posts are viewable
- Views are only incremented for published posts, not unlisted posts

### 6. Admin Panel - Posts Index
**File:** `resources/views/admin/blog/posts/index.blade.php`

**Changes:**
- Added 5th stats card for "Unlisted" posts with orange color scheme
- Changed grid from 4 columns to 5 columns: `grid-cols-1 md:grid-cols-5`
- Added "Unlisted" option to status filter dropdown
- Updated status badge display logic to show orange badge for unlisted posts
- Status badge colors:
  - Published: Green
  - Draft: Yellow
  - Scheduled: Purple
  - Unlisted: Orange (with eye-off icon)

### 7. Admin Panel - Create Post
**File:** `resources/views/admin/blog/posts/create.blade.php`

- Added "Unlisted" option to status dropdown
- Added helpful description: "Post is viewable via direct link but won't appear in any frontend lists"

### 8. Admin Panel - Edit Post
**File:** `resources/views/admin/blog/posts/edit.blade.php`

- Added "Unlisted" option to status dropdown
- Added helpful description: "Post is viewable via direct link but won't appear in any frontend lists"

### 9. Frontend Blog Controller
**File:** `app/Modules/Blog/Controllers/Frontend/BlogController.php`

- No changes needed - already filters by `status = 'published'` on line 55
- This automatically excludes unlisted posts from frontend lists
- Individual post view (`show()` method) works for all statuses including unlisted

---

## Behavior Summary

### Unlisted Posts ARE:
✅ Viewable via direct URL (e.g., `/blog/post-slug`)
✅ Accessible to anyone with the link
✅ Indexed in admin panel with orange badge
✅ Filterable in admin panel
✅ Counted in admin stats

### Unlisted Posts ARE NOT:
❌ Shown in blog index page
❌ Shown in category archive pages
❌ Shown in tag archive pages
❌ Shown in search results
❌ Shown in featured posts sidebar
❌ Shown in popular posts sidebar
❌ Shown in related posts section
❌ View count incremented when visited

---

## Use Cases

1. **Preview Links**: Share posts with specific people before publishing
2. **Private Content**: Content accessible only to those with the link
3. **Testing**: Test posts in production without public visibility
4. **Exclusive Content**: Share special content via email/social media without listing publicly
5. **Temporary Removal**: Hide posts from lists without deleting or drafting

---

## Admin Interface

### Stats Card
- **Location**: Admin Blog Posts Index
- **Color**: Orange
- **Icon**: Eye-off (hidden/unlisted indicator)
- **Position**: 5th card after Total, Published, Drafts, Scheduled

### Filter
- **Location**: Advanced Filters section
- **Label**: "Unlisted"
- **Behavior**: Shows only unlisted posts when selected

### Status Badge
- **Color**: Orange background with orange text
- **Text**: "Unlisted"
- **Location**: Posts table status column

---

## Technical Notes

1. **Database**: Uses MySQL ENUM type for status field
2. **Scopes**: Existing `published()` scope automatically excludes unlisted
3. **Repository Pattern**: All frontend queries use repository methods that filter by published status
4. **Service Layer**: Business logic properly handles unlisted status
5. **Views Tracking**: Unlisted posts don't increment view count to avoid analytics pollution

---

## Testing Checklist

- [x] Migration runs successfully
- [ ] Can create new post with unlisted status
- [ ] Can edit post and change to unlisted status
- [ ] Unlisted posts appear in admin panel
- [ ] Unlisted filter works in admin panel
- [ ] Unlisted count shows correctly in stats
- [ ] Unlisted posts viewable via direct URL
- [ ] Unlisted posts NOT shown in blog index
- [ ] Unlisted posts NOT shown in category pages
- [ ] Unlisted posts NOT shown in tag pages
- [ ] Unlisted posts NOT shown in search results
- [ ] Unlisted posts NOT shown in featured posts
- [ ] Unlisted posts NOT shown in popular posts
- [ ] Views not incremented for unlisted posts

---

## Files Modified

1. `database/migrations/2025_11_24_000001_add_unlisted_status_to_blog_posts.php` (NEW)
2. `app/Modules/Blog/Requests/StorePostRequest.php`
3. `app/Modules/Blog/Requests/UpdatePostRequest.php`
4. `app/Modules/Blog/Models/Post.php`
5. `app/Modules/Blog/Repositories/PostRepository.php`
6. `app/Modules/Blog/Services/PostService.php`
7. `resources/views/admin/blog/posts/index.blade.php`
8. `resources/views/admin/blog/posts/create.blade.php`
9. `resources/views/admin/blog/posts/edit.blade.php`

---

## Migration Command

```bash
php artisan migrate --path=database/migrations/2025_11_24_000001_add_unlisted_status_to_blog_posts.php
```

## Rollback Command

```bash
php artisan migrate:rollback --path=database/migrations/2025_11_24_000001_add_unlisted_status_to_blog_posts.php
```

**Note**: Rollback will convert all unlisted posts to draft status before removing the enum value.
