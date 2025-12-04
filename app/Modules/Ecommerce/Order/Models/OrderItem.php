<?php

namespace App\Modules\Ecommerce\Order\Models;

use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'product_sku',
        'variant_name',
        'variant_attributes',
        'price',
        'quantity',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'product_image',
    ];

    protected $casts = [
        'variant_attributes' => 'array',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product variant.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get formatted variant attributes.
     */
    public function getFormattedVariantAttributesAttribute(): string
    {
        if (empty($this->variant_attributes)) {
            return '';
        }

        $formatted = [];
        foreach ($this->variant_attributes as $key => $value) {
            // Convert value to string safely
            $stringValue = $this->convertToString($value);
            $formatted[] = ucfirst($key) . ': ' . $stringValue;
        }

        return implode(', ', $formatted);
    }

    /**
     * Convert any value to string recursively.
     */
    private function convertToString($value): string
    {
        if (is_array($value)) {
            return implode(', ', array_map([$this, 'convertToString'], $value));
        }
        
        if (is_object($value)) {
            return method_exists($value, '__toString') ? (string) $value : json_encode($value);
        }
        
        return (string) $value;
    }
}
