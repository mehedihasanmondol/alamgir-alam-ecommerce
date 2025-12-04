<?php

namespace App\Modules\Blog\Models;

use App\Models\User;
use App\Models\Media;
use App\Modules\Blog\Models\TickMark;
use App\Traits\HasSeo;
use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * ModuleName: Blog
 * Purpose: Represents a blog post with full CMS features
 * 
 * Key Methods:
 * - author(): Get post author
 * - category(): Get post category
 * - tags(): Get post tags
 * - comments(): Get post comments
 * - incrementViews(): Increment view count
 * - calculateReadingTime(): Calculate reading time
 * - isPublished(): Check if post is published
 * - scopePublished(): Query only published posts
 * - scopeFeatured(): Query only featured posts
 * 
 * Dependencies:
 * - User model
 * - BlogCategory model
 * - Tag model
 * - Comment model
 * - HasSeo trait
 * - HasUniqueSlug trait
 * 
 * @category Blog
 * @package  App\Modules\Blog\Models
 * @author   AI Assistant
 * @created  2025-11-07
 * @updated  2025-11-07
 */
class Post extends Model
{
    use HasFactory, SoftDeletes, HasSeo, HasUniqueSlug;

    protected $table = 'blog_posts';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'author_id',
        'blog_category_id',
        'media_id',
        'featured_image',
        'featured_image_alt',
        'youtube_url',
        'status',
        'published_at',
        'scheduled_at',
        'views_count',
        'reading_time',
        'is_featured',
        'allow_comments',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'views_count' => 'integer',
        'reading_time' => 'integer',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
    ];

    protected $appends = ['reading_time_text'];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                // Use Bangla-compatible slug generation
                $post->slug = generate_slug($post->title);
                
                // Fallback to Laravel's Str::slug if generate_slug returns empty
                if (empty($post->slug)) {
                    $post->slug = Str::slug($post->title);
                }
            }
            
            // Calculate reading time
            if ($post->content) {
                $post->reading_time = $post->calculateReadingTime($post->content);
            }
        });

        static::updating(function ($post) {
            // Recalculate reading time if content changed
            if ($post->isDirty('content')) {
                $post->reading_time = $post->calculateReadingTime($post->content);
            }
        });
    }

    /**
     * Get the author of the post
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the featured image media
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Get the user who verified the post
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the category of the post (legacy - kept for backward compatibility)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /**
     * Get the categories associated with the post (many-to-many)
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_post_category', 'blog_post_id', 'blog_category_id')
            ->withTimestamps();
    }

    /**
     * Get the tags associated with the post
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'blog_post_tag', 'blog_post_id', 'blog_tag_id')
            ->withTimestamps();
    }

    /**
     * Get the comments for the post
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'blog_post_id');
    }

    /**
     * Get only approved comments
     */
    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'blog_post_id')
            ->where('status', 'approved')
            ->whereNull('parent_id')
            ->latest();
    }

    /**
     * Get the tick marks associated with the post
     */
    public function tickMarks(): BelongsToMany
    {
        return $this->belongsToMany(
            TickMark::class,
            'blog_post_tick_mark',
            'blog_post_id',
            'blog_tick_mark_id'
        )
        ->withPivot(['added_by', 'notes'])
        ->withTimestamps()
        ->orderBy('sort_order');
    }

    /**
     * Get only active tick marks
     */
    public function activeTickMarks(): BelongsToMany
    {
        return $this->tickMarks()->where('is_active', true);
    }

    /**
     * Get the products associated with the post (Shop This Article)
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Modules\Ecommerce\Product\Models\Product::class,
            'blog_post_product',
            'blog_post_id',
            'product_id'
        )
        ->withPivot(['sort_order'])
        ->withTimestamps()
        ->orderBy('blog_post_product.sort_order');
    }

    /**
     * Increment views count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Calculate reading time based on content
     * Average reading speed: 200 words per minute
     */
    public function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / 200);
        return max(1, $minutes); // Minimum 1 minute
    }

    /**
     * Get reading time as text
     */
    public function getReadingTimeTextAttribute(): string
    {
        if ($this->reading_time < 1) {
            return 'Less than a minute';
        }
        
        return $this->reading_time . ' min read';
    }

    /**
     * Get YouTube video ID from URL
     */
    public function getYoutubeVideoIdAttribute(): ?string
    {
        if (!$this->youtube_url) {
            return null;
        }

        // Extract video ID from various YouTube URL formats
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
        
        if (preg_match($pattern, $this->youtube_url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Get YouTube embed URL
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $videoId = $this->youtube_video_id;
        
        if (!$videoId) {
            return null;
        }

        return "https://www.youtube.com/embed/{$videoId}";
    }

    /**
     * Check if post is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && 
               $this->published_at && 
               $this->published_at->isPast();
    }

    /**
     * Check if post is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled' && 
               $this->scheduled_at && 
               $this->scheduled_at->isFuture();
    }

    /**
     * Scope to get only published posts (excludes unlisted posts from frontend lists)
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Check if post is unlisted
     */
    public function isUnlisted(): bool
    {
        return $this->status === 'unlisted';
    }

    /**
     * Scope to get only featured posts
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get posts by tick mark
     */
    public function scopeWithTickMark($query, $tickMarkSlug)
    {
        return $query->whereHas('tickMarks', function ($q) use ($tickMarkSlug) {
            $q->where('slug', $tickMarkSlug);
        });
    }

    /**
     * Scope to get posts by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('blog_category_id', $categoryId);
    }

    /**
     * Scope to get posts by tag
     */
    public function scopeByTag($query, $tagId)
    {
        return $query->whereHas('tags', function ($q) use ($tagId) {
            $q->where('blog_tags.id', $tagId);
        });
    }

    /**
     * Scope to get posts by author
     */
    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    /**
     * Scope to search posts
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('excerpt', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to get popular posts (by views)
     */
    public function scopePopular($query, $limit = 5)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    /**
     * Scope to get recent posts
     */
    public function scopeRecent($query, $limit = 5)
    {
        return $query->latest('published_at')->limit($limit);
    }

    /**
     * Get related posts based on category and tags
     */
    public function relatedPosts($limit = 3)
    {
        return static::published()
            ->where('id', '!=', $this->id)
            ->where(function ($query) {
                $query->where('blog_category_id', $this->blog_category_id)
                    ->orWhereHas('tags', function ($q) {
                        $q->whereIn('blog_tags.id', $this->tags->pluck('id'));
                    });
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Get the slug source field name
     */
    public function getSlugSourceField(): string
    {
        return 'title';
    }

    /**
     * Get tables to check for unique slug
     */
    public function getSlugUniqueTables(): array
    {
        return ['blog_posts', 'products'];
    }

    /**
     * Get all active tick marks for this post
     */
    public function getActiveTickMarks(): array
    {
        return $this->tickMarks->map(function ($tickMark) {
            return [
                'id' => $tickMark->id,
                'type' => $tickMark->slug,
                'label' => $tickMark->label,
                'icon' => $tickMark->icon,
                'bg_color' => $tickMark->bg_color,
                'text_color' => $tickMark->text_color,
            ];
        })->toArray();
    }

    /**
     * Check if post has any tick marks (legacy or custom)
     */
    public function hasTickMarks(): bool
    {
        return $this->tickMarks()->exists();
    }

    /**
     * Check if post has a specific tick mark
     */
    public function hasTickMark(int|string $tickMarkIdOrSlug): bool
    {
        if (is_numeric($tickMarkIdOrSlug)) {
            return $this->tickMarks()->where('blog_tick_marks.id', $tickMarkIdOrSlug)->exists();
        }
        
        return $this->tickMarks()->where('blog_tick_marks.slug', $tickMarkIdOrSlug)->exists();
    }

    /**
     * Attach a tick mark to this post
     */
    public function attachTickMark(int $tickMarkId, ?string $notes = null): void
    {
        $this->tickMarks()->syncWithoutDetaching([
            $tickMarkId => [
                'added_by' => auth()->id(),
                'notes' => $notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Detach a tick mark from this post
     */
    public function detachTickMark(int $tickMarkId): void
    {
        $this->tickMarks()->detach($tickMarkId);
    }

    /**
     * Sync tick marks for this post
     */
    public function syncTickMarks(array $tickMarkIds): void
    {
        $syncData = [];
        foreach ($tickMarkIds as $tickMarkId) {
            $syncData[$tickMarkId] = [
                'added_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        $this->tickMarks()->sync($syncData);
    }
}
