<?php

namespace App\Modules\Ecommerce\Brand\Models;

use App\Traits\HasSeo;
use App\Traits\HasUniqueSlug;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ModuleName: E-commerce Brand
 * Purpose: Product brand management with SEO
 * 
 * Key Methods:
 * - products(): Get products for this brand
 * - getLogoUrl(): Get logo URL
 * - getUrl(): Get brand URL
 * 
 * Dependencies:
 * - HasSeo trait for SEO functionality
 * - HasUniqueSlug trait for auto-slug generation
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class Brand extends Model
{
    use HasFactory, SoftDeletes, HasSeo, HasUniqueSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'media_id',
        'website',
        'email',
        'phone',
        'sort_order',
        'is_active',
        'is_featured',
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
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the brand logo from media library
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Get products for this brand
     */
    public function products()
    {
        return $this->hasMany(\App\Modules\Ecommerce\Product\Models\Product::class, 'brand_id');
    }

    /**
     * Scope: Get only active brands
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get only featured brands
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope: Search brands
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
     * Get URL for this brand
     */
    public function getUrl(): string
    {
        return route('brands.show', $this->slug);
    }

    /**
     * Get logo URL (large size)
     */
    public function getLogoUrl(): ?string
    {
        if ($this->media) {
            return $this->media->large_url;
        }
        
        // Fallback to old logo field
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    /**
     * Get thumbnail URL (small size)
     */
    public function getThumbnailUrl(): ?string
    {
        if ($this->media) {
            return $this->media->small_url;
        }
        
        // Fallback to old logo field
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    /**
     * Get medium logo URL
     */
    public function getMediumLogoUrl(): ?string
    {
        if ($this->media) {
            return $this->media->medium_url;
        }
        
        // Fallback to old logo field
        return $this->logo ? asset('storage/' . $this->logo) : null;
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
     * Get website URL with protocol
     */
    public function getWebsiteUrl(): ?string
    {
        if (!$this->website) {
            return null;
        }

        // Add https:// if no protocol specified
        if (!preg_match("~^(?:f|ht)tps?://~i", $this->website)) {
            return 'https://' . $this->website;
        }

        return $this->website;
    }
}
