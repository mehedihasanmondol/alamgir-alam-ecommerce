# Blog Post Tick Mark Management System

## Overview
A comprehensive system for managing blog post quality indicators (tick marks/badges) with support for multiple selection and dynamic creation of custom tick marks.

## Features

### ✅ Core Features
- **Multiple Tick Mark Selection**: Assign multiple tick marks to a single blog post
- **Dynamic Tick Mark Creation**: Create new custom tick marks on-the-fly
- **System & Custom Tick Marks**: Pre-defined system tick marks + user-created custom ones
- **Visual Badge System**: Color-coded badges with icons
- **Livewire Integration**: Real-time UI updates without page refresh
- **Bulk Operations**: Manage tick marks across multiple posts

### 📦 Pre-installed Tick Marks
1. **Verified** (Blue) - Content has been verified and is trustworthy
2. **Editor's Choice** (Purple) - Featured by editorial team
3. **Trending** (Red) - Currently trending content
4. **Premium** (Yellow) - Premium content for subscribers
5. **Hot** (Orange) - Hot and popular content
6. **New** (Green) - Recently published content

## Installation Steps

### 1. Run Migrations
```bash
php artisan migrate
```

This will create:
- `blog_tick_marks` table - Stores tick mark definitions
- `blog_post_tick_mark` pivot table - Links posts to tick marks

### 2. Seed Default Tick Marks
```bash
php artisan db:seed --class=BlogTickMarkSeeder
```

### 3. Clear Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Usage Guide

### For Administrators

#### Managing Tick Marks (Admin Panel)
1. Navigate to **Admin → Blog → Tick Marks**
2. View all available tick marks with post counts
3. Create new custom tick marks
4. Edit existing custom tick marks (system tick marks are protected)
5. Toggle active/inactive status
6. Delete custom tick marks (if not in use)

#### Creating a New Tick Mark
1. Click **"Create New Tick Mark"**
2. Fill in the form:
   - **Name**: Internal name (e.g., "Hot")
   - **Label**: Display label shown to users
   - **Description**: Brief description
   - **Icon**: Choose from available icons
   - **Color**: Select color scheme
   - **Sort Order**: Display order (lower = first)
   - **Active**: Enable/disable

#### Assigning Tick Marks to Posts

**Method 1: Post Edit Page**
1. Edit any blog post
2. Find the "Quality Indicators" section
3. Click **"Manage"** button
4. Select/deselect tick marks
5. Click **"Save Changes"**

**Method 2: Quick Create**
1. Click **"Create New"** in the tick mark manager
2. Fill in the form
3. Click **"Create & Add"** - automatically adds to current post

### For Developers

#### Using in Blade Templates

**Display Post Tick Marks:**
```blade
@foreach($post->tickMarks as $tickMark)
    <span class="{{ $tickMark->bg_color }} {{ $tickMark->text_color }} px-2 py-1 rounded">
        {{ $tickMark->label }}
    </span>
@endforeach
```

**Using Livewire Component:**
```blade
<livewire:admin.blog.post-tick-mark-manager :postId="$post->id" />
```

#### Using in Controllers

**Get all tick marks for a post:**
```php
$tickMarks = $post->tickMarks;
$activeTickMarks = $post->activeTickMarks;
```

**Check if post has a specific tick mark:**
```php
if ($post->hasTickMark('verified')) {
    // Post is verified
}
```

**Attach a tick mark:**
```php
$post->attachTickMark($tickMarkId, 'Optional notes');
```

**Sync multiple tick marks:**
```php
$post->syncTickMarks([1, 2, 3]); // Tick mark IDs
```

#### Using the Service Layer

```php
use App\Modules\Blog\Services\TickMarkService;

// Inject in controller
public function __construct(TickMarkService $tickMarkService)
{
    $this->tickMarkService = $tickMarkService;
}

// Get all available tick marks
$tickMarks = $this->tickMarkService->getAllTickMarks();

// Attach tick mark to post
$this->tickMarkService->attachTickMark($postId, $tickMarkId, 'Notes');

// Sync tick marks
$this->tickMarkService->syncTickMarks($postId, [1, 2, 3]);

// Bulk operations
$this->tickMarkService->bulkAttachTickMark([1, 2, 3], $tickMarkId);
```

## Database Structure

### blog_tick_marks Table
- `id` - Primary key
- `name` - Internal name
- `slug` - URL-friendly slug
- `label` - Display label
- `description` - Description
- `icon` - Icon name
- `color` - Base color
- `bg_color` - Background Tailwind class
- `text_color` - Text Tailwind class
- `is_active` - Active status
- `is_system` - System protected flag
- `sort_order` - Display order

### blog_post_tick_mark Pivot Table
- `id` - Primary key
- `blog_post_id` - Foreign key to posts
- `blog_tick_mark_id` - Foreign key to tick marks
- `added_by` - User who added it
- `notes` - Optional notes
- `timestamps`

## API Endpoints

### Admin Routes (Prefix: `/admin/blog/tick-marks`)
- `GET /` - List all tick marks
- `GET /create` - Show create form
- `POST /` - Store new tick mark
- `GET /{id}/edit` - Show edit form
- `PUT /{id}` - Update tick mark
- `DELETE /{id}` - Delete tick mark
- `PATCH /{id}/toggle-active` - Toggle active status
- `POST /update-sort-order` - Update sort order

## Available Icons
- `check-circle` - Checkmark in circle
- `star` - Star icon
- `trending-up` - Trending arrow
- `crown` - Crown icon
- `flame` - Flame icon
- `sparkles` - Sparkles icon
- `badge-check` - Badge with check
- `lightning-bolt` - Lightning bolt

## Available Colors
- Blue, Purple, Red, Yellow, Green, Orange, Pink, Indigo, Gray

## Best Practices

1. **System Tick Marks**: Don't delete or modify system tick marks (Verified, Editor's Choice, Trending, Premium)
2. **Naming**: Use clear, descriptive names for custom tick marks
3. **Colors**: Choose colors that match your brand and are distinguishable
4. **Sort Order**: Keep frequently used tick marks at lower sort orders
5. **Active Status**: Deactivate instead of deleting tick marks that are temporarily not needed

## Troubleshooting

### Tick marks not showing?
- Check if tick mark is active
- Verify post has tick marks assigned
- Clear cache: `php artisan cache:clear`

### Can't delete a tick mark?
- System tick marks cannot be deleted
- Check if tick mark is assigned to posts
- Deactivate instead of deleting

### Livewire component not updating?
- Check browser console for errors
- Verify Livewire is properly installed
- Run: `php artisan livewire:discover`

## Migration Rollback

If you need to rollback:
```bash
php artisan migrate:rollback --step=2
```

This will remove the tick mark tables.

## Support

For issues or questions, refer to the project documentation or contact the development team.
