<?php

namespace App\Modules\Ecommerce\Delivery\Repositories;

use App\Modules\Ecommerce\Delivery\Models\DeliveryZone;
use App\Modules\Ecommerce\Delivery\Models\DeliveryMethod;
use App\Modules\Ecommerce\Delivery\Models\DeliveryRate;
use Illuminate\Database\Eloquent\Collection;

class DeliveryRepository
{
    /**
     * Get all active delivery zones.
     */
    public function getActiveZones(): Collection
    {
        return DeliveryZone::active()->ordered()->get();
    }

    /**
     * Get all active delivery methods.
     */
    public function getActiveMethods(): Collection
    {
        return DeliveryMethod::active()->ordered()->get();
    }

    /**
     * Get delivery zone by ID.
     */
    public function getZoneById(int $id): ?DeliveryZone
    {
        return DeliveryZone::find($id);
    }

    /**
     * Get delivery method by ID.
     */
    public function getMethodById(int $id): ?DeliveryMethod
    {
        return DeliveryMethod::find($id);
    }

    /**
     * Find zone by location.
     */
    public function findZoneByLocation(string $country = null, string $state = null, string $city = null, string $postalCode = null): ?DeliveryZone
    {
        $zones = $this->getActiveZones();

        foreach ($zones as $zone) {
            if ($zone->coversLocation($country, $state, $city, $postalCode)) {
                return $zone;
            }
        }

        return null;
    }

    /**
     * Get available methods for a zone.
     */
    public function getMethodsForZone(int $zoneId, float $orderTotal = 0, float $orderWeight = 0, int $itemCount = 0): Collection
    {
        return DeliveryMethod::whereHas('rates', function ($query) use ($zoneId) {
            $query->where('delivery_zone_id', $zoneId)
                  ->where('is_active', true);
        })
        ->active()
        ->showOnCheckout()
        ->ordered()
        ->get()
        ->filter(function ($method) use ($orderTotal, $orderWeight, $itemCount) {
            return $method->isAvailableForOrder($orderTotal, $orderWeight, $itemCount);
        });
    }

    /**
     * Get rate for zone and method.
     */
    public function getRate(int $zoneId, int $methodId, float $orderTotal = 0, float $orderWeight = 0, int $itemCount = 0): ?DeliveryRate
    {
        $rates = DeliveryRate::forZoneAndMethod($zoneId, $methodId)
            ->active()
            ->with(['zone', 'method'])
            ->get();

        // Find the best matching rate based on order details
        foreach ($rates as $rate) {
            $method = $rate->method;

            switch ($method->calculation_type) {
                case 'weight_based':
                    if ($rate->matchesWeight($orderWeight)) {
                        return $rate;
                    }
                    break;

                case 'price_based':
                    if ($rate->matchesPrice($orderTotal)) {
                        return $rate;
                    }
                    break;

                case 'item_based':
                    if ($rate->matchesItemCount($itemCount)) {
                        return $rate;
                    }
                    break;

                default:
                    return $rate; // Return first rate for flat_rate or free
            }
        }

        // Return first rate if no specific match found
        return $rates->first();
    }

    /**
     * Get all zones with pagination.
     */
    public function getAllZonesPaginated(int $perPage = 15)
    {
        return DeliveryZone::orderBy('sort_order')->orderBy('name')->paginate($perPage);
    }

    /**
     * Get all methods with pagination.
     */
    public function getAllMethodsPaginated(int $perPage = 15)
    {
        return DeliveryMethod::orderBy('sort_order')->orderBy('name')->paginate($perPage);
    }

    /**
     * Get all rates with pagination.
     */
    public function getAllRatesPaginated(int $perPage = 15)
    {
        return DeliveryRate::with(['zone', 'method'])
            ->orderBy('delivery_zone_id')
            ->orderBy('delivery_method_id')
            ->paginate($perPage);
    }

    /**
     * Create delivery zone.
     */
    public function createZone(array $data): DeliveryZone
    {
        return DeliveryZone::create($data);
    }

    /**
     * Update delivery zone.
     */
    public function updateZone(int $id, array $data): bool
    {
        $zone = $this->getZoneById($id);
        return $zone ? $zone->update($data) : false;
    }

    /**
     * Delete delivery zone.
     */
    public function deleteZone(int $id): bool
    {
        $zone = $this->getZoneById($id);
        return $zone ? $zone->delete() : false;
    }

    /**
     * Create delivery method.
     */
    public function createMethod(array $data): DeliveryMethod
    {
        return DeliveryMethod::create($data);
    }

    /**
     * Update delivery method.
     */
    public function updateMethod(int $id, array $data): bool
    {
        $method = $this->getMethodById($id);
        return $method ? $method->update($data) : false;
    }

    /**
     * Delete delivery method.
     */
    public function deleteMethod(int $id): bool
    {
        $method = $this->getMethodById($id);
        return $method ? $method->delete() : false;
    }

    /**
     * Create delivery rate.
     */
    public function createRate(array $data): DeliveryRate
    {
        return DeliveryRate::create($data);
    }

    /**
     * Update delivery rate.
     */
    public function updateRate(int $id, array $data): bool
    {
        $rate = DeliveryRate::find($id);
        return $rate ? $rate->update($data) : false;
    }

    /**
     * Delete delivery rate.
     */
    public function deleteRate(int $id): bool
    {
        $rate = DeliveryRate::find($id);
        return $rate ? $rate->delete() : false;
    }
}
