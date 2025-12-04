<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Ecommerce\Product\Models\Product;

/**
 * ModuleName: Sale Offers
 * Purpose: Manage sale offer products displayed on homepage
 * 
 * Key Features:
 * - Link products to sale offers section
 * - Control display order
 * - Enable/disable offers
 * 
 * Relationships:
 * - belongsTo: Product
 * 
 * @category Models
 * @package  App\Models
 * @author   Admin
 * @created  2025-11-06
 * @updated  2025-11-06
 */
class SaleOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the product associated with this sale offer
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to get only active sale offers
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by display order
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}
