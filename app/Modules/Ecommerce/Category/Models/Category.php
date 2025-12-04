<?php

namespace App\Modules\Ecommerce\Category\Models;

use App\Traits\HasSeo;
use App\Traits\HasUniqueSlug;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ModuleName: E-commerce Category
 * Purpose: Product category management with hierarchical structure and SEO
 * 
 * Key Methods:
 * - parent(): Get parent category
 * - children(): Get child categories
 * - products(): Get products in this category
 * - ancestors(): Get all parent categories
 * - descendants(): Get all child categories recursively
 * 
 * Dependencies:
 * - HasSeo trait for SEO functionality
 * - HasUniqueSlug trait for auto-slug generation
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class Category extends Model
{
    use HasFactory, SoftDeletes, HasSeo, HasUniqueSlug;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'media_id',
        'sort_order',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
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
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the category image from media library
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Get child categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get active child categories
     */
    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    /**
     * Get products in this category (many-to-many relationship)
     */
    public function products()
    {
        return $this->belongsToMany(\App\Modules\Ecommerce\Product\Models\Product::class, 'category_product')
                    ->withTimestamps();
    }

    /**
     * Get all descendants recursively
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors->reverse();
    }

    /**
     * Get breadcrumb path
     */
    public function getBreadcrumb(): array
    {
        $breadcrumb = [];
        $ancestors = $this->ancestors();

        foreach ($ancestors as $ancestor) {
            $breadcrumb[] = [
                'label' => $ancestor->name,
                'url' => route('categories.show', $ancestor->slug),
            ];
        }

        $breadcrumb[] = [
            'label' => $this->name,
            'url' => null, // Current page, no link
        ];

        return $breadcrumb;
    }

    /**
     * Check if category has children
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Check if category is a parent (has no parent)
     */
    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Get category depth level
     */
    public function getDepth(): int
    {
        return $this->ancestors()->count();
    }

    /**
     * Scope: Get only parent categories
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope: Search categories
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('slug', 'like', "%{$search}%");
        });
    }

    /**
     * Get full category path (e.g., "Electronics > Phones > Smartphones")
     */
    public function getFullPath(): string
    {
        $path = $this->ancestors()->pluck('name')->toArray();
        $path[] = $this->name;
        
        return implode(' > ', $path);
    }

    /**
     * Get URL for this category
     */
    public function getUrl(): string
    {
        return route('categories.show', $this->slug);
    }

    /**
     * Get image URL (prioritizes media library, fallback to old image field)
     */
    public function getImageUrl(): ?string
    {
        // Priority 1: Media library large image
        if ($this->media) {
            return $this->media->large_url;
        }
        
        // Priority 2: Old image field (legacy support)
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        
        // Priority 3: Default image
        return asset('images/default-category.jpg');
    }

    /**
     * Get thumbnail URL (600px small size)
     */
    public function getThumbnailUrl(): ?string
    {
        if ($this->media) {
            return $this->media->small_url;
        }
        
        return $this->getImageUrl();
    }

    /**
     * Get medium image URL (1200px)
     */
    public function getMediumImageUrl(): ?string
    {
        if ($this->media) {
            return $this->media->medium_url;
        }
        
        return $this->getImageUrl();
    }

    /**
     * Disable automatic slug updates during editing
     * Users must manually generate slugs using the "Generate" button
     */
    public function shouldAutoUpdateSlug(): bool
    {
        return false;
    }
}
