<?php

namespace App\Livewire\Checkout;

use App\Modules\Ecommerce\Delivery\Models\DeliveryZone;
use App\Modules\Ecommerce\Delivery\Models\DeliveryMethod;
use App\Modules\Ecommerce\Delivery\Models\DeliveryRate;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeliverySelector extends Component
{
    public $selectedZoneId;
    public $selectedMethodId;
    public $selectedRateId;
    public $deliveryZones;
    public $deliveryMethods = [];
    public $deliveryRate;
    public $shippingCost = 0;
    public $cartTotal = 0;
    public $cartWeight = 0;
    public $itemCount = 0;

    public function mount($cartTotal = 0, $cartWeight = 0, $itemCount = 0)
    {
        $this->cartTotal = $cartTotal;
        $this->cartWeight = $cartWeight;
        $this->itemCount = $itemCount;

        // Load from session first (from cart)
        if (session()->has('delivery_zone_id')) {
            $this->selectedZoneId = session('delivery_zone_id');
            $this->selectedMethodId = session('delivery_method_id');
            $this->selectedRateId = session('delivery_rate_id');
        }
        // Load user's default delivery preferences if logged in and no session
        elseif (Auth::check() && Auth::user()->default_delivery_zone_id) {
            $this->selectedZoneId = Auth::user()->default_delivery_zone_id;
            $this->selectedMethodId = Auth::user()->default_delivery_method_id;
        }
        
        if ($this->selectedZoneId) {
            $this->loadDeliveryMethods();
            if ($this->selectedMethodId) {
                $this->calculateShipping();
            }
        }
    }

    public function updatedSelectedZoneId($value)
    {
        $this->selectedMethodId = null;
        $this->selectedRateId = null;
        $this->deliveryMethods = [];
        $this->shippingCost = 0;
        
        if ($value) {
            $this->loadDeliveryMethods();
        }
        
        $this->saveToSession();
    }

    public function updatedSelectedMethodId($value)
    {
        $this->selectedRateId = null;
        $this->calculateShipping();
        $this->saveToSession();
    }

    public function loadDeliveryMethods()
    {
        if (!$this->selectedZoneId) {
            return;
        }

        $zone = DeliveryZone::find($this->selectedZoneId);
        
        if ($zone) {
            // Get all methods for this zone first
            $allMethods = DeliveryMethod::with(['rates' => function ($query) use ($zone) {
                $query->where('delivery_zone_id', $zone->id)
                      ->where('is_active', true);
            }])
            ->whereHas('rates', function ($query) use ($zone) {
                $query->where('delivery_zone_id', $zone->id)
                      ->where('is_active', true);
            })
            ->where('is_active', true)
            ->where('show_on_checkout', true)
            ->ordered()
            ->get();
            
            // Filter by cart requirements
            $this->deliveryMethods = $allMethods->filter(function ($method) {
                // If no restrictions, allow the method
                if (!$method->min_order_amount && !$method->max_order_amount && !$method->max_weight) {
                    return true;
                }
                
                return $method->isAvailableForOrder($this->cartTotal, $this->cartWeight, $this->itemCount);
            });
            
            // Auto-select first delivery method if available
            if ($this->deliveryMethods->isNotEmpty() && !$this->selectedMethodId) {
                $this->selectedMethodId = $this->deliveryMethods->first()->id;
                $this->calculateShipping();
            }
        }
    }

    public function calculateShipping()
    {
        if (!$this->selectedZoneId || !$this->selectedMethodId) {
            $this->shippingCost = 0;
            $this->deliveryRate = null;
            $this->dispatch('shipping-updated', shippingCost: 0);
            return;
        }

        $rate = DeliveryRate::where('delivery_zone_id', $this->selectedZoneId)
            ->where('delivery_method_id', $this->selectedMethodId)
            ->where('is_active', true)
            ->first();

        if ($rate) {
            $this->selectedRateId = $rate->id;
            $this->deliveryRate = $rate;
            
            // Calculate total shipping cost
            $this->shippingCost = $rate->base_rate + $rate->handling_fee + $rate->insurance_fee + $rate->cod_fee;
            
            // Check for free shipping
            $method = DeliveryMethod::find($this->selectedMethodId);
            if ($method && $method->qualifiesForFreeShipping($this->cartTotal)) {
                $this->shippingCost = 0;
            }
            
            $this->dispatch('shipping-updated', shippingCost: $this->shippingCost);
        } else {
            $this->shippingCost = 0;
            $this->deliveryRate = null;
            $this->dispatch('shipping-updated', shippingCost: 0);
        }
    }

    public function saveToSession()
    {
        session([
            'delivery_zone_id' => $this->selectedZoneId,
            'delivery_method_id' => $this->selectedMethodId,
            'delivery_rate_id' => $this->selectedRateId,
            'shipping_cost' => $this->shippingCost,
        ]);
    }

    public function render()
    {
        $this->deliveryZones = DeliveryZone::active()->ordered()->get();
        
        return view('livewire.checkout.delivery-selector');
    }
}
