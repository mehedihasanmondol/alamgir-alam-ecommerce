# Blog Post Tick Mark Management System

## Overview
Evidence-based tick mark management system for blog posts that allows administrators to mark posts with various quality and status indicators. This system provides visual badges to highlight verified, editor's choice, trending, and premium content.

## Features

### 4 Types of Tick Marks

1. **✓ Verified** (Blue Badge)
   - Indicates the post has been fact-checked and verified
   - Tracks who verified it and when
   - Supports verification notes
   - Builds trust with readers

2. **★ Editor's Choice** (Purple Badge)
   - Highlights posts selected by editorial team
   - Featured content recommendation
   - Increases visibility and engagement

3. **↗ Trending** (Red Badge)
   - Marks currently popular or viral content
   - Time-sensitive indicator
   - Drives traffic to hot topics

4. **♕ Premium** (Yellow Badge)
   - Designates high-quality or exclusive content
   - Can be used for paid/subscriber-only content
   - Premium content differentiation

## Database Structure

### New Fields Added to `blog_posts` Table

```sql
is_verified          BOOLEAN      DEFAULT false
is_editor_choice     BOOLEAN      DEFAULT false
is_trending          BOOLEAN      DEFAULT false
is_premium           BOOLEAN      DEFAULT false
verified_at          TIMESTAMP    NULLABLE
verified_by          FOREIGN KEY  NULLABLE (references users.id)
verification_notes   TEXT         NULLABLE
```

### Indexes
- `is_verified` - Fast filtering of verified posts
- `is_editor_choice` - Quick editor's choice queries
- `is_trending` - Efficient trending post retrieval
- `is_premium` - Premium content filtering

## Architecture

### 1. Model Layer (`Post.php`)

#### New Relationships
```php
public function verifier(): BelongsTo
```
Returns the user who verified the post.

#### New Scopes
```php
scopeVerified($query)        // Get only verified posts
scopeEditorChoice($query)    // Get editor's choice posts
scopeTrending($query)        // Get trending posts
scopePremium($query)         // Get premium posts
```

#### Helper Methods
```php
getActiveTickMarks(): array  // Returns all active tick marks with metadata
hasTickMarks(): bool         // Check if post has any tick marks
```

### 2. Service Layer (`TickMarkService.php`)

#### Core Methods

**Toggle Methods**
```php
toggleVerification(int $postId, ?string $notes = null): Post
toggleEditorChoice(int $postId): Post
toggleTrending(int $postId): Post
togglePremium(int $postId): Post
```

**Explicit Set/Remove**
```php
markAsVerified(int $postId, ?string $notes = null): Post
removeVerification(int $postId): Post
```

**Bulk Operations**
```php
updateTickMarks(int $postId, array $data): Post
bulkUpdateTickMarks(array $postIds, string $type, bool $value): int
clearAllTickMarks(int $postId): Post
```

**Analytics**
```php
getStatistics(): array
getPostsByTickMark(string $type, int $perPage = 10)
```

### 3. Controller Layer (`PostController.php`)

#### New Endpoints

| Method | Route | Purpose |
|--------|-------|---------|
| GET | `/admin/blog/tick-marks/stats` | Get tick mark statistics |
| POST | `/admin/blog/posts/{post}/toggle-verification` | Toggle verification |
| POST | `/admin/blog/posts/{post}/toggle-editor-choice` | Toggle editor's choice |
| POST | `/admin/blog/posts/{post}/toggle-trending` | Toggle trending |
| POST | `/admin/blog/posts/{post}/toggle-premium` | Toggle premium |
| POST | `/admin/blog/posts/bulk-update-tick-marks` | Bulk update multiple posts |

### 4. Livewire Component (`TickMarkManager.php`)

#### Features
- Real-time tick mark toggling
- Verification modal with notes
- Manage all tick marks modal
- Clear all tick marks functionality
- Event broadcasting for UI updates

#### Component Props
```php
public $postId;              // The post being managed
public $isVerified;          // Verification state
public $isEditorChoice;      // Editor's choice state
public $isTrending;          // Trending state
public $isPremium;           // Premium state
public $verificationNotes;   // Verification notes
```

#### Events
```php
'tickMarkUpdated'   // Fired when any tick mark changes
'refreshTickMarks'  // Listener to refresh component state
```

## Usage Guide

### Admin Panel Usage

#### 1. Quick Toggle (Single Click)
In the posts list, click any badge to instantly toggle that tick mark:
- Click "Verified" → Opens verification modal
- Click "Editor's Choice" → Instantly toggles
- Click "Trending" → Instantly toggles
- Click "Premium" → Instantly toggles

#### 2. Manage All (Comprehensive Control)
Click the "Manage" button to open a modal where you can:
- Toggle all tick marks at once
- Add verification notes
- See verification history
- Clear all tick marks

#### 3. Bulk Operations (API)
Use the bulk update endpoint to update multiple posts:

```javascript
// Example: Mark multiple posts as trending
fetch('/admin/blog/posts/bulk-update-tick-marks', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        post_ids: [1, 2, 3, 4, 5],
        tick_mark_type: 'trending',
        value: true
    })
});
```

### Frontend Display

#### Using the Blade Component
```blade
{{-- Display all active tick marks for a post --}}
<x-blog.tick-marks :post="$post" />
```

This will automatically render badges for all active tick marks.

#### Manual Display
```blade
@if($post->is_verified)
    <span class="badge badge-verified">✓ Verified</span>
@endif

@if($post->is_editor_choice)
    <span class="badge badge-editor">★ Editor's Choice</span>
@endif

@if($post->is_trending)
    <span class="badge badge-trending">↗ Trending</span>
@endif

@if($post->is_premium)
    <span class="badge badge-premium">♕ Premium</span>
@endif
```

### Querying Posts by Tick Marks

#### Using Scopes
```php
// Get all verified posts
$verifiedPosts = Post::verified()->get();

// Get editor's choice posts
$editorPicks = Post::editorChoice()->latest()->take(5)->get();

// Get trending posts
$trendingPosts = Post::trending()->published()->get();

// Get premium posts
$premiumPosts = Post::premium()->paginate(10);

// Combine multiple scopes
$featuredContent = Post::published()
    ->verified()
    ->editorChoice()
    ->orderBy('views_count', 'desc')
    ->take(10)
    ->get();
```

#### Using Service Methods
```php
use App\Modules\Blog\Services\TickMarkService;

$tickMarkService = app(TickMarkService::class);

// Get statistics
$stats = $tickMarkService->getStatistics();
// Returns: ['verified' => 45, 'editor_choice' => 12, 'trending' => 8, 'premium' => 15, 'total_with_marks' => 67]

// Get posts by type
$verifiedPosts = $tickMarkService->getPostsByTickMark('verified', 20);
```

## Best Practices

### 1. Verification Guidelines
- ✅ **DO** verify posts after fact-checking
- ✅ **DO** add detailed verification notes
- ✅ **DO** remove verification if information becomes outdated
- ❌ **DON'T** verify opinion pieces without disclosure
- ❌ **DON'T** verify posts without proper review

### 2. Editor's Choice Selection
- ✅ **DO** select high-quality, well-researched content
- ✅ **DO** rotate editor's choice regularly
- ✅ **DO** limit to 5-10 posts at a time
- ❌ **DON'T** overuse (reduces impact)
- ❌ **DON'T** select promotional content

### 3. Trending Management
- ✅ **DO** update trending posts daily
- ✅ **DO** base on actual metrics (views, shares)
- ✅ **DO** remove when no longer trending
- ❌ **DON'T** keep posts trending for weeks
- ❌ **DON'T** manually trend low-quality content

### 4. Premium Content
- ✅ **DO** reserve for exceptional content
- ✅ **DO** ensure premium posts deliver value
- ✅ **DO** use for subscriber-only content
- ❌ **DON'T** mark all posts as premium
- ❌ **DON'T** use as a paywall without value

## API Reference

### Get Statistics
```http
GET /admin/blog/tick-marks/stats
```

**Response:**
```json
{
    "success": true,
    "data": {
        "verified": 45,
        "editor_choice": 12,
        "trending": 8,
        "premium": 15,
        "total_with_marks": 67
    }
}
```

### Toggle Verification
```http
POST /admin/blog/posts/{post}/toggle-verification
```

**Response:**
```json
{
    "success": true,
    "message": "পোস্ট যাচাইকৃত হয়েছে",
    "data": {
        "id": 1,
        "is_verified": true,
        "verified_at": "2025-11-10T02:30:00.000000Z",
        "verified_by": 1,
        "verification_notes": "Fact-checked all claims"
    }
}
```

### Bulk Update
```http
POST /admin/blog/posts/bulk-update-tick-marks
Content-Type: application/json

{
    "post_ids": [1, 2, 3],
    "tick_mark_type": "trending",
    "value": true
}
```

**Response:**
```json
{
    "success": true,
    "message": "3টি পোস্ট আপডেট করা হয়েছে",
    "affected": 3
}
```

## Database Queries

### Find Posts with Multiple Tick Marks
```php
// Posts that are both verified AND editor's choice
$posts = Post::verified()
    ->editorChoice()
    ->get();

// Posts with ANY tick mark
$posts = Post::where(function($query) {
    $query->where('is_verified', true)
          ->orWhere('is_editor_choice', true)
          ->orWhere('is_trending', true)
          ->orWhere('is_premium', true);
})->get();
```

### Get Verification History
```php
$post = Post::with('verifier')->find($id);

if ($post->is_verified) {
    echo "Verified by: " . $post->verifier->name;
    echo "Verified at: " . $post->verified_at->format('M d, Y');
    echo "Notes: " . $post->verification_notes;
}
```

## Troubleshooting

### Issue: Tick marks not showing in admin panel
**Solution:** Clear Livewire cache
```bash
php artisan livewire:discover
php artisan view:clear
```

### Issue: Verification modal not opening
**Solution:** Ensure Alpine.js is loaded
```blade
@livewireScripts
<script src="//unpkg.com/alpinejs" defer></script>
```

### Issue: Bulk update not working
**Solution:** Check CSRF token and validation
```javascript
headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}
```

## Performance Considerations

### Indexes
All tick mark fields are indexed for fast queries:
```sql
CREATE INDEX idx_is_verified ON blog_posts(is_verified);
CREATE INDEX idx_is_editor_choice ON blog_posts(is_editor_choice);
CREATE INDEX idx_is_trending ON blog_posts(is_trending);
CREATE INDEX idx_is_premium ON blog_posts(is_premium);
```

### Caching Recommendations
```php
// Cache tick mark statistics (refresh every 5 minutes)
$stats = Cache::remember('blog_tick_mark_stats', 300, function() {
    return app(TickMarkService::class)->getStatistics();
});

// Cache trending posts (refresh every 10 minutes)
$trending = Cache::remember('blog_trending_posts', 600, function() {
    return Post::trending()->published()->take(10)->get();
});
```

## Security

### Authorization
All tick mark operations require admin authentication:
```php
Route::middleware(['auth', 'role:admin'])->group(function() {
    // Tick mark routes
});
```

### Validation
Bulk operations validate all inputs:
```php
$request->validate([
    'post_ids' => 'required|array',
    'post_ids.*' => 'exists:blog_posts,id',
    'tick_mark_type' => 'required|in:verified,editor_choice,trending,premium',
    'value' => 'required|boolean',
]);
```

## Future Enhancements

### Planned Features
- [ ] Automatic trending detection based on views/engagement
- [ ] Scheduled tick mark expiration (auto-remove after X days)
- [ ] Tick mark history/audit log
- [ ] Email notifications when posts are verified
- [ ] Public API for tick mark statistics
- [ ] Advanced analytics dashboard
- [ ] User-submitted verification requests
- [ ] Tick mark templates/presets

## Files Created/Modified

### New Files
1. `database/migrations/2025_11_10_022939_add_tick_mark_fields_to_blog_posts_table.php`
2. `app/Modules/Blog/Services/TickMarkService.php`
3. `app/Livewire/Admin/Blog/TickMarkManager.php`
4. `resources/views/livewire/admin/blog/tick-mark-manager.blade.php`
5. `resources/views/components/blog/tick-marks.blade.php`
6. `BLOG_TICK_MARK_MANAGEMENT.md`

### Modified Files
1. `app/Modules/Blog/Models/Post.php` - Added tick mark fields, scopes, and methods
2. `app/Modules/Blog/Controllers/Admin/PostController.php` - Added tick mark endpoints
3. `routes/blog.php` - Added tick mark routes
4. `resources/views/livewire/admin/blog/post-list.blade.php` - Added tick mark column

## Support

For issues or questions:
- Check the troubleshooting section above
- Review the code comments in service classes
- Test with the provided API examples

---

**Version:** 1.0.0  
**Created:** November 10, 2025  
**Status:** Production Ready ✅
