<?php

namespace App\Modules\Stock\Models;

use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_id',
        'warehouse_id',
        'min_quantity',
        'current_quantity',
        'status',
        'notified_at',
        'resolved_at',
        'notes',
    ];

    protected $casts = [
        'min_quantity' => 'integer',
        'current_quantity' => 'integer',
        'notified_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_NOTIFIED = 'notified';
    const STATUS_RESOLVED = 'resolved';

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
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the warehouse
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Scope for pending alerts
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for notified alerts
     */
    public function scopeNotified($query)
    {
        return $query->where('status', self::STATUS_NOTIFIED);
    }

    /**
     * Scope for resolved alerts
     */
    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    /**
     * Mark as notified
     */
    public function markAsNotified()
    {
        $this->update([
            'status' => self::STATUS_NOTIFIED,
            'notified_at' => now(),
        ]);
    }

    /**
     * Mark as resolved
     */
    public function markAsResolved($notes = null)
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now(),
            'notes' => $notes,
        ]);
    }

    /**
     * Check if stock is below minimum
     */
    public function isLowStock()
    {
        return $this->current_quantity < $this->min_quantity;
    }
}
