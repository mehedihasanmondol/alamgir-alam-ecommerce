<?php

namespace App\Modules\Ecommerce\Product\Models;

use App\Modules\Ecommerce\Brand\Models\Brand;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Traits\HasSeo;
use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasSeo, HasUniqueSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'brand_id',
        'product_type',
        'status',
        'external_url',
        'button_text',
        'is_featured',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views_count',
        'sales_count',
        'average_rating',
        'review_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'views_count' => 'integer',
        'sales_count' => 'integer',
        'average_rating' => 'float',
        'review_count' => 'integer',
    ];

    // Removed eager loading to prevent issues with products without variants
    // protected $with = ['defaultVariant'];

    // Relationships
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product')
                    ->withTimestamps();
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function defaultVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true);
    }

    // Grouped Products
    public function childProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_grouped', 'parent_product_id', 'child_product_id')
            ->withPivot('quantity', 'sort_order')
            ->orderBy('sort_order');
    }

    public function parentProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_grouped', 'child_product_id', 'parent_product_id');
    }

    public function saleOffer(): HasOne
    {
        return $this->hasOne(\App\Models\SaleOffer::class);
    }

    public function trendingProduct(): HasOne
    {
        return $this->hasOne(\App\Models\TrendingProduct::class);
    }

    public function bestSellerProduct(): HasOne
    {
        return $this->hasOne(\App\Models\BestSellerProduct::class);
    }

    public function newArrivalProduct(): HasOne
    {
        return $this->hasOne(\App\Models\NewArrivalProduct::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ProductQuestion::class);
    }

    public function approvedQuestions(): HasMany
    {
        return $this->hasMany(ProductQuestion::class)->where('status', 'approved');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('status', 'approved');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('product_type', $type);
    }

    // Accessors
    public function getPriceAttribute()
    {
        $variant = $this->variants->where('is_default', true)->first() ?? $this->variants->first();
        return $variant?->price ?? 0;
    }

    public function getSalePriceAttribute()
    {
        $variant = $this->variants->where('is_default', true)->first() ?? $this->variants->first();
        return $variant?->sale_price;
    }

    public function getDiscountPercentageAttribute()
    {
        $variant = $this->variants->where('is_default', true)->first() ?? $this->variants->first();
        if (!$variant || !$variant->sale_price) {
            return 0;
        }
        
        return round((($variant->price - $variant->sale_price) / $variant->price) * 100);
    }

    public function getInStockAttribute(): bool
    {
        if ($this->product_type === 'simple') {
            $variant = $this->variants->where('is_default', true)->first() ?? $this->variants->first();
            return $variant?->stock_quantity > 0;
        }
        
        return $this->variants->where('stock_quantity', '>', 0)->isNotEmpty();
    }

    public function getImageUrlAttribute(): ?string
    {
        $primaryImage = $this->images->where('is_primary', true)->first();
        if ($primaryImage) {
            return $primaryImage->image_path;
        }
        
        $firstImage = $this->images->first();
        if ($firstImage) {
            return $firstImage->image_path;
        }
        
        $variant = $this->variants->where('is_default', true)->first() ?? $this->variants->first();
        return $variant?->image;
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('is_active', true);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Methods
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementSales(int $quantity = 1): void
    {
        $this->increment('sales_count', $quantity);
    }

    public function isSimple(): bool
    {
        return $this->product_type === 'simple';
    }

    public function isVariable(): bool
    {
        return $this->product_type === 'variable';
    }

    public function isGrouped(): bool
    {
        return $this->product_type === 'grouped';
    }

    public function isAffiliate(): bool
    {
        return $this->product_type === 'affiliate';
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
     * Get primary product image
     */
    public function getPrimaryImage()
    {
        return $this->images()->where('is_primary', true)->first() 
            ?? $this->images()->first();
    }

    /**
     * Get primary image URL (large) - NEW MEDIA SYSTEM
     */
    public function getPrimaryImageUrl(): ?string
    {
        $primaryImage = $this->getPrimaryImage();
        return $primaryImage ? $primaryImage->getImageUrl() : null;
    }

    /**
     * Get primary thumbnail URL (small) - NEW MEDIA SYSTEM
     */
    public function getPrimaryThumbnailUrl(): ?string
    {
        $primaryImage = $this->getPrimaryImage();
        return $primaryImage ? $primaryImage->getThumbnailUrl() : null;
    }

    /**
     * Get primary medium image URL - NEW MEDIA SYSTEM
     */
    public function getPrimaryMediumUrl(): ?string
    {
        $primaryImage = $this->getPrimaryImage();
        return $primaryImage ? $primaryImage->getMediumUrl() : null;
    }
}
