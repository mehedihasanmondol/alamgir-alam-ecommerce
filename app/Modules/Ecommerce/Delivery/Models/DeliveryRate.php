<?php

namespace App\Modules\Ecommerce\Delivery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_zone_id',
        'delivery_method_id',
        'base_rate',
        'weight_from',
        'weight_to',
        'rate_per_kg',
        'price_from',
        'price_to',
        'rate_percentage',
        'item_from',
        'item_to',
        'rate_per_item',
        'handling_fee',
        'insurance_fee',
        'cod_fee',
        'is_active',
    ];

    protected $casts = [
        'base_rate' => 'decimal:2',
        'weight_from' => 'decimal:2',
        'weight_to' => 'decimal:2',
        'rate_per_kg' => 'decimal:2',
        'price_from' => 'decimal:2',
        'price_to' => 'decimal:2',
        'rate_percentage' => 'decimal:2',
        'item_from' => 'integer',
        'item_to' => 'integer',
        'rate_per_item' => 'decimal:2',
        'handling_fee' => 'decimal:2',
        'insurance_fee' => 'decimal:2',
        'cod_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the delivery zone.
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }

    /**
     * Get the delivery method.
     */
    public function method(): BelongsTo
    {
        return $this->belongsTo(DeliveryMethod::class, 'delivery_method_id');
    }

    /**
     * Scope: Active rates only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: For specific zone and method.
     */
    public function scopeForZoneAndMethod($query, int $zoneId, int $methodId)
    {
        return $query->where('delivery_zone_id', $zoneId)
                     ->where('delivery_method_id', $methodId);
    }

    /**
     * Calculate shipping cost based on order details.
     */
    public function calculateCost(float $orderTotal, float $orderWeight, int $itemCount, bool $isCod = false): float
    {
        $cost = $this->base_rate;

        // Get calculation type from method
        $calculationType = $this->method->calculation_type;

        switch ($calculationType) {
            case 'weight_based':
                if ($this->matchesWeight($orderWeight)) {
                    $cost += $orderWeight * ($this->rate_per_kg ?? 0);
                }
                break;

            case 'price_based':
                if ($this->matchesPrice($orderTotal)) {
                    $cost += $orderTotal * (($this->rate_percentage ?? 0) / 100);
                }
                break;

            case 'item_based':
                if ($this->matchesItemCount($itemCount)) {
                    $cost += $itemCount * ($this->rate_per_item ?? 0);
                }
                break;

            case 'free':
                return 0;

            case 'flat_rate':
            default:
                // Use base_rate only
                break;
        }

        // Add additional fees
        $cost += $this->handling_fee;
        $cost += $this->insurance_fee;

        // Add COD fee if applicable
        if ($isCod) {
            $cost += $this->cod_fee;
        }

        return round($cost, 2);
    }

    /**
     * Check if weight matches this rate's range.
     */
    protected function matchesWeight(float $weight): bool
    {
        if ($this->weight_from !== null && $weight < $this->weight_from) {
            return false;
        }

        if ($this->weight_to !== null && $weight > $this->weight_to) {
            return false;
        }

        return true;
    }

    /**
     * Check if price matches this rate's range.
     */
    protected function matchesPrice(float $price): bool
    {
        if ($this->price_from !== null && $price < $this->price_from) {
            return false;
        }

        if ($this->price_to !== null && $price > $this->price_to) {
            return false;
        }

        return true;
    }

    /**
     * Check if item count matches this rate's range.
     */
    protected function matchesItemCount(int $count): bool
    {
        if ($this->item_from !== null && $count < $this->item_from) {
            return false;
        }

        if ($this->item_to !== null && $count > $this->item_to) {
            return false;
        }

        return true;
    }

    /**
     * Get cost breakdown.
     */
    public function getCostBreakdown(float $orderTotal, float $orderWeight, int $itemCount, bool $isCod = false): array
    {
        return [
            'base_rate' => $this->base_rate,
            'handling_fee' => $this->handling_fee,
            'insurance_fee' => $this->insurance_fee,
            'cod_fee' => $isCod ? $this->cod_fee : 0,
            'total' => $this->calculateCost($orderTotal, $orderWeight, $itemCount, $isCod),
        ];
    }
}
