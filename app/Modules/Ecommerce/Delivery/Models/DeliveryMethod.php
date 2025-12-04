<?php

namespace App\Modules\Ecommerce\Delivery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'estimated_days',
        'min_days',
        'max_days',
        'carrier_name',
        'carrier_code',
        'tracking_url',
        'calculation_type',
        'free_shipping_threshold',
        'min_order_amount',
        'max_order_amount',
        'max_weight',
        'is_active',
        'show_on_checkout',
        'sort_order',
        'icon',
    ];

    protected $casts = [
        'min_days' => 'integer',
        'max_days' => 'integer',
        'free_shipping_threshold' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_order_amount' => 'decimal:2',
        'max_weight' => 'decimal:2',
        'is_active' => 'boolean',
        'show_on_checkout' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the delivery rates for this method.
     */
    public function rates(): HasMany
    {
        return $this->hasMany(DeliveryRate::class);
    }

    /**
     * Get active rates for this method.
     */
    public function activeRates(): HasMany
    {
        return $this->hasMany(DeliveryRate::class)->where('is_active', true);
    }

    /**
     * Scope: Active methods only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Show on checkout.
     */
    public function scopeShowOnCheckout($query)
    {
        return $query->where('show_on_checkout', true);
    }

    /**
     * Scope: Order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Check if method is available for order.
     */
    public function isAvailableForOrder(float $orderTotal, float $orderWeight, int $itemCount): bool
    {
        // Check minimum order amount
        if ($this->min_order_amount && $orderTotal < $this->min_order_amount) {
            return false;
        }

        // Check maximum order amount
        if ($this->max_order_amount && $orderTotal > $this->max_order_amount) {
            return false;
        }

        // Check maximum weight
        if ($this->max_weight && $orderWeight > $this->max_weight) {
            return false;
        }

        return true;
    }

    /**
     * Check if order qualifies for free shipping.
     */
    public function qualifiesForFreeShipping(float $orderTotal): bool
    {
        if ($this->calculation_type === 'free') {
            return true;
        }

        if ($this->free_shipping_threshold && $orderTotal >= $this->free_shipping_threshold) {
            return true;
        }

        return false;
    }

    /**
     * Get tracking URL for a tracking number.
     */
    public function getTrackingUrl(string $trackingNumber): ?string
    {
        if (!$this->tracking_url) {
            return null;
        }

        return str_replace('{tracking_number}', $trackingNumber, $this->tracking_url);
    }

    /**
     * Get estimated delivery text.
     */
    public function getEstimatedDeliveryText(): string
    {
        if ($this->estimated_days) {
            return $this->estimated_days;
        }

        if ($this->min_days && $this->max_days) {
            return $this->min_days . '-' . $this->max_days . ' days';
        }

        if ($this->min_days) {
            return $this->min_days . '+ days';
        }

        return 'Standard delivery';
    }
}
