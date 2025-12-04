<?php

namespace App\Modules\Ecommerce\Product\Models;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'is_default',
        'price',
        'sale_price',
        'cost_price',
        'stock_quantity',
        'low_stock_alert',
        'manage_stock',
        'stock_status',
        'weight',
        'length',
        'width',
        'height',
        'image',
        'media_id',
        'shipping_class',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'manage_stock' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_alert' => 'integer',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductAttribute::class,
            'product_variant_attributes',
            'product_variant_id',
            'product_attribute_id'
        )->withPivot('product_attribute_value_id');
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'product_variant_attributes',
            'product_variant_id',
            'product_attribute_value_id'
        )->withPivot('product_attribute_id')->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock')
            ->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'low_stock_alert')
            ->where('stock_quantity', '>', 0);
    }

    // Accessors
    public function getDisplayPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->sale_price) {
            return 0;
        }
        
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->manage_stock && $this->stock_quantity <= $this->low_stock_alert && $this->stock_quantity > 0;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->manage_stock && $this->stock_quantity <= 0;
    }

    /**
     * Check if stock restriction is enabled globally
     */
    public static function isStockRestrictionEnabled(): bool
    {
        return \App\Models\SiteSetting::get('enable_out_of_stock_restriction', '1') === '1';
    }

    /**
     * Check if product can be added to cart (considering global setting)
     */
    public function canAddToCart(): bool
    {
        // If restriction is disabled, always allow
        if (!self::isStockRestrictionEnabled()) {
            return true;
        }

        // If restriction is enabled, check stock
        return !$this->is_out_of_stock;
    }

    /**
     * Check if stock quantity should be displayed
     */
    public function shouldShowStock(): bool
    {
        return self::isStockRestrictionEnabled();
    }

    /**
     * Get stock display text based on global setting
     */
    public function getStockDisplayText(): ?string
    {
        if (!self::isStockRestrictionEnabled()) {
            return null; // Hide all stock info when restriction disabled
        }

        if ($this->is_out_of_stock) {
            return 'Out of Stock';
        }

        if ($this->is_low_stock) {
            return "Only {$this->stock_quantity} left";
        }

        if ($this->stock_quantity > 0 && $this->stock_quantity <= 10) {
            return "Only {$this->stock_quantity} left";
        }

        return 'In Stock';
    }

    // Methods
    public function decreaseStock(int $quantity): bool
    {
        if (!$this->manage_stock) {
            return true;
        }

        if ($this->stock_quantity < $quantity) {
            return false;
        }

        $this->decrement('stock_quantity', $quantity);
        $this->updateStockStatus();
        
        return true;
    }

    public function increaseStock(int $quantity): void
    {
        if (!$this->manage_stock) {
            return;
        }

        $this->increment('stock_quantity', $quantity);
        $this->updateStockStatus();
    }

    protected function updateStockStatus(): void
    {
        if ($this->stock_quantity <= 0) {
            $this->update(['stock_status' => 'out_of_stock']);
        } else {
            $this->update(['stock_status' => 'in_stock']);
        }
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Get variant image URL (large size)
     */
    public function getImageUrl(): ?string
    {
        if ($this->media) {
            return $this->media->large_url;
        }
        
        // Fallback to old image field
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /**
     * Get variant thumbnail URL (small size)
     */
    public function getThumbnailUrl(): ?string
    {
        if ($this->media) {
            return $this->media->small_url;
        }
        
        // Fallback to old image field
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
