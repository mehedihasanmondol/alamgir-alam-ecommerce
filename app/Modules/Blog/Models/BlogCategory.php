<?php

namespace App\Modules\Blog\Models;

use App\Models\Media;
use App\Traits\HasSeo;
use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ModuleName: Blog
 * Purpose: Represents a blog category with hierarchical structure
 * 
 * Key Methods:
 * - parent(): Get parent category
 * - children(): Get child categories
 * - posts(): Get posts in this category
 * - allPosts(): Get all posts including from child categories
 * - scopeActive(): Query only active categories
 * - scopeRoots(): Query only root categories
 * 
 * Dependencies:
 * - Post model
 * - HasSeo trait
 * - HasUniqueSlug trait
 * 
 * @category Blog
 * @package  App\Modules\Blog\Models
 * @author   AI Assistant
 * @created  2025-11-07
 * @updated  2025-11-07
 */
class BlogCategory extends Model
{
    use HasFactory, SoftDeletes, HasSeo, HasUniqueSlug;

    protected $table = 'blog_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'media_id',        // NEW: Media library support
        'image_path',      // Legacy support
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'parent_id');
    }

    /**
     * Get child categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(BlogCategory::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get posts in this category (many-to-many)
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'blog_post_category', 'blog_category_id', 'blog_post_id')
            ->withTimestamps();
    }

    /**
     * Get published posts count
     */
    public function getPublishedPostsCountAttribute(): int
    {
        return $this->posts()->published()->count();
    }

    /**
     * Get all posts including from child categories
     */
    public function allPosts()
    {
        $categoryIds = $this->children->pluck('id')->push($this->id);
        return Post::whereHas('categories', function($query) use ($categoryIds) {
            $query->whereIn('blog_categories.id', $categoryIds);
        });
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only root categories (no parent)
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the slug source field name
     */
    public function getSlugSourceField(): string
    {
        return 'name';
    }

    /**
     * Get tables to check for unique slug
     */
    public function getSlugUniqueTables(): array
    {
        return ['blog_categories'];
    }

    /**
     * Disable automatic slug updates during editing
     * Users must manually generate slugs using the "Generate" button
     */
    public function shouldAutoUpdateSlug(): bool
    {
        return false;
    }

    /**
     * Get the media (for media library system)
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Get category image URL - supports both media library and legacy paths
     */
    public function getImageUrl(): ?string
    {
        // Priority 1: Use media library if available
        if ($this->media_id && $this->media) {
            return $this->media->large_url;
        }
        
        // Priority 2: Fallback to legacy image_path
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        
        return null;
    }

    /**
     * Get category thumbnail URL
     */
    public function getThumbnailUrl(): ?string
    {
        // Priority 1: Use media library if available
        if ($this->media_id && $this->media) {
            return $this->media->small_url;
        }
        
        // Priority 2: Fallback to legacy image_path
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        
        return null;
    }

    /**
     * Get category medium size image URL
     */
    public function getMediumUrl(): ?string
    {
        // Priority 1: Use media library if available
        if ($this->media_id && $this->media) {
            return $this->media->medium_url;
        }
        
        // Priority 2: Fallback to legacy image_path
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        
        return null;
    }

    /**
     * Get the full hierarchy path (e.g., "Grandparent > Parent > Category")
     */
    public function getHierarchyPath(string $separator = ' > '): string
    {
        $path = [];
        $current = $this;
        
        // Build path from current category up to root
        while ($current) {
            array_unshift($path, $current->name);
            $current = $current->parent;
        }
        
        return implode($separator, $path);
    }

    /**
     * Get the parent hierarchy path only (without current category)
     */
    public function getParentHierarchyPath(string $separator = ' > '): ?string
    {
        if (!$this->parent) {
            return null;
        }
        
        return $this->parent->getHierarchyPath($separator);
    }

    /**
     * Get all ancestor categories (parent, grandparent, etc.)
     */
    public function getAncestors(): array
    {
        $ancestors = [];
        $current = $this->parent;
        
        while ($current) {
            $ancestors[] = $current;
            $current = $current->parent;
        }
        
        return array_reverse($ancestors);
    }

    /**
     * Get depth level in hierarchy (0 for root, 1 for first level child, etc.)
     */
    public function getDepthLevel(): int
    {
        $depth = 0;
        $current = $this->parent;
        
        while ($current) {
            $depth++;
            $current = $current->parent;
        }
        
        return $depth;
    }
}
