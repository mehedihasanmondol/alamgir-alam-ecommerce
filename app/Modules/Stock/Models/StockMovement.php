<?php

namespace App\Modules\Stock\Models;

use App\Models\User;
use App\Modules\Ecommerce\Order\Models\Order;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'warehouse_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'cost_per_unit',
        'total_cost',
        'reference_type',
        'reference_id',
        'note',
        'supplier_id',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'cost_per_unit' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    const TYPE_PURCHASE = 'purchase';
    const TYPE_SALE = 'sale';
    const TYPE_RETURN = 'return';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_DAMAGED = 'damaged';
    const TYPE_TRANSFER = 'transfer';

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get the warehouse
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user who performed this movement
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the referenced model (polymorphic)
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Get movement direction (in/out)
     */
    public function getDirectionAttribute()
    {
        return in_array($this->type, [self::TYPE_PURCHASE, self::TYPE_RETURN]) ? 'in' : 'out';
    }

    /**
     * Check if movement increases stock
     */
    public function isStockIncrease()
    {
        return $this->quantity > 0;
    }

    /**
     * Check if movement decreases stock
     */
    public function isStockDecrease()
    {
        return $this->quantity < 0;
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific warehouse
     */
    public function scopeInWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    /**
     * Scope for specific product
     */
    public function scopeForProduct($query, $productId, $variantId = null)
    {
        $query->where('product_id', $productId);

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        }

        return $query;
    }

    /**
     * Scope for stock increases (purchase, return)
     */
    public function scopeStockIn($query)
    {
        return $query->whereIn('type', [self::TYPE_PURCHASE, self::TYPE_RETURN]);
    }

    /**
     * Scope for stock decreases (sale, damaged, transfer out)
     */
    public function scopeStockOut($query)
    {
        return $query->whereIn('type', [self::TYPE_SALE, self::TYPE_DAMAGED, self::TYPE_TRANSFER]);
    }
}
