<?php

namespace App\Modules\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'email',
        'manager_name',
        'is_active',
        'is_default',
        'capacity',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'capacity' => 'integer',
    ];

    /**
     * Get stock movements for this warehouse
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get stock alerts for this warehouse
     */
    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class);
    }

    /**
     * Scope for active warehouses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get default warehouse
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Set as default warehouse
     */
    public function setAsDefault()
    {
        static::where('id', '!=', $this->id)->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }

    /**
     * Get current stock level for a product
     */
    public function getStockLevel($productId, $variantId = null)
    {
        $query = $this->stockMovements()
            ->where('product_id', $productId);

        if ($variantId) {
            $query->where('variant_id', $variantId);
        }

        return $query->sum('quantity');
    }
}
