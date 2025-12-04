<?php

namespace App\Modules\Ecommerce\Delivery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'countries',
        'states',
        'cities',
        'postal_codes',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'countries' => 'array',
        'states' => 'array',
        'cities' => 'array',
        'postal_codes' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the delivery rates for this zone.
     */
    public function rates(): HasMany
    {
        return $this->hasMany(DeliveryRate::class);
    }

    /**
     * Get active rates for this zone.
     */
    public function activeRates(): HasMany
    {
        return $this->hasMany(DeliveryRate::class)->where('is_active', true);
    }

    /**
     * Scope: Active zones only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Check if zone covers a specific location.
     */
    public function coversLocation(string $country = null, string $state = null, string $city = null, string $postalCode = null): bool
    {
        // If no filters are set, zone covers all locations
        if (empty($this->countries) && empty($this->states) && empty($this->cities) && empty($this->postal_codes)) {
            return true;
        }

        // Check country
        if (!empty($this->countries) && $country) {
            if (!in_array($country, $this->countries)) {
                return false;
            }
        }

        // Check state
        if (!empty($this->states) && $state) {
            if (!in_array($state, $this->states)) {
                return false;
            }
        }

        // Check city
        if (!empty($this->cities) && $city) {
            if (!in_array($city, $this->cities)) {
                return false;
            }
        }

        // Check postal code
        if (!empty($this->postal_codes) && $postalCode) {
            if (!in_array($postalCode, $this->postal_codes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get available delivery methods for this zone.
     */
    public function getAvailableMethodsAttribute()
    {
        return DeliveryMethod::whereHas('rates', function ($query) {
            $query->where('delivery_zone_id', $this->id)
                  ->where('is_active', true);
        })->where('is_active', true)
          ->ordered()
          ->get();
    }
}
