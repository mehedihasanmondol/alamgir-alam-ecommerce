<?php

namespace App\Livewire\Admin\Order;

use App\Modules\Ecommerce\Order\Models\Order;
use App\Modules\Ecommerce\Delivery\Models\DeliveryZone;
use App\Modules\Ecommerce\Delivery\Models\DeliveryMethod;
use App\Modules\Ecommerce\Delivery\Models\DeliveryRate;
use Livewire\Component;

class EditDeliveryInfo extends Component
{
    public Order $order;
    public $delivery_zone_id;
    public $delivery_method_id;
    public $delivery_status;
    public $estimated_delivery;
    public $tracking_number;
    public $carrier;
    public $shipped_at;
    public $delivered_at;
    public $isEditing = false;
    public $isSaving = false;
    public $availableMethods = [];
    public $deliveryRate = null;
    public $shippingCostPreview = null;

    protected $rules = [
        'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
        'delivery_method_id' => 'nullable|exists:delivery_methods,id',
        'delivery_status' => 'nullable|in:pending,assigned,picked_up,in_transit,out_for_delivery,delivered,failed,returned',
        'estimated_delivery' => 'nullable|date',
        'tracking_number' => 'nullable|string|max:255',
        'carrier' => 'nullable|string|max:255',
        'shipped_at' => 'nullable|date',
        'delivered_at' => 'nullable|date|after_or_equal:shipped_at',
    ];

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->delivery_zone_id = $order->delivery_zone_id;
        $this->delivery_method_id = $order->delivery_method_id;
        $this->delivery_status = $order->delivery_status;
        $this->estimated_delivery = $order->estimated_delivery ? $order->estimated_delivery->format('Y-m-d') : null;
        $this->tracking_number = $order->tracking_number;
        $this->carrier = $order->carrier;
        $this->shipped_at = $order->shipped_at ? $order->shipped_at->format('Y-m-d\TH:i') : null;
        $this->delivered_at = $order->delivered_at ? $order->delivered_at->format('Y-m-d\TH:i') : null;
        
        if ($this->delivery_zone_id) {
            $this->loadAvailableMethods();
        }
        
        if ($this->delivery_zone_id && $this->delivery_method_id) {
            $this->loadDeliveryRate();
        }
    }

    public function updatedDeliveryZoneId($value)
    {
        $this->delivery_method_id = null;
        $this->availableMethods = [];
        $this->deliveryRate = null;
        $this->shippingCostPreview = null;
        
        if ($value) {
            $this->loadAvailableMethods();
        }
    }

    public function updatedDeliveryMethodId($value)
    {
        $this->deliveryRate = null;
        $this->shippingCostPreview = null;
        
        if ($value && $this->delivery_zone_id) {
            $this->loadDeliveryRate();
        }
    }

    public function loadAvailableMethods()
    {
        $this->availableMethods = DeliveryMethod::whereHas('rates', function ($query) {
            $query->where('delivery_zone_id', $this->delivery_zone_id)
                  ->where('is_active', true);
        })->where('is_active', true)->get();
    }

    public function loadDeliveryRate()
    {
        $this->deliveryRate = DeliveryRate::where('delivery_zone_id', $this->delivery_zone_id)
            ->where('delivery_method_id', $this->delivery_method_id)
            ->where('is_active', true)
            ->first();
            
        if ($this->deliveryRate) {
            $isCod = $this->order->payment_method === 'cod';
            $this->shippingCostPreview = $this->deliveryRate->calculateCost(
                $this->order->subtotal,
                0,
                $this->order->items->sum('quantity'),
                $isCod
            );
        }
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        
        if (!$this->isEditing) {
            $this->delivery_zone_id = $this->order->delivery_zone_id;
            $this->delivery_method_id = $this->order->delivery_method_id;
            $this->delivery_status = $this->order->delivery_status;
            $this->estimated_delivery = $this->order->estimated_delivery ? $this->order->estimated_delivery->format('Y-m-d') : null;
            $this->tracking_number = $this->order->tracking_number;
            $this->carrier = $this->order->carrier;
            $this->shipped_at = $this->order->shipped_at ? $this->order->shipped_at->format('Y-m-d\TH:i') : null;
            $this->delivered_at = $this->order->delivered_at ? $this->order->delivered_at->format('Y-m-d\TH:i') : null;
            $this->resetValidation();
        }
    }

    public function save()
    {
        $this->validate();
        
        $this->isSaving = true;

        try {
            $updateData = [
                'delivery_zone_id' => $this->delivery_zone_id,
                'delivery_method_id' => $this->delivery_method_id,
                'delivery_status' => $this->delivery_status,
                'estimated_delivery' => $this->estimated_delivery,
                'tracking_number' => $this->tracking_number,
                'carrier' => $this->carrier,
                'shipped_at' => $this->shipped_at,
                'delivered_at' => $this->delivered_at,
            ];
            
            // Auto-update delivery status based on dates
            if ($this->delivered_at && !$this->order->delivered_at) {
                $updateData['delivery_status'] = 'delivered';
            } elseif ($this->shipped_at && !$this->order->shipped_at) {
                $updateData['delivery_status'] = 'in_transit';
            }
            
            // Update shipping cost if delivery changed
            if ($this->shippingCostPreview !== null) {
                $isCod = $this->order->payment_method === 'cod';
                $breakdown = $this->deliveryRate->getCostBreakdown(
                    $this->order->subtotal,
                    0,
                    $this->order->items->sum('quantity'),
                    $isCod
                );
                
                $updateData['shipping_cost'] = $this->shippingCostPreview;
                $updateData['base_shipping_cost'] = $breakdown['base_rate'];
                $updateData['handling_fee'] = $breakdown['handling_fee'];
                $updateData['insurance_fee'] = $breakdown['insurance_fee'];
                $updateData['cod_fee'] = $breakdown['cod_fee'];
                
                // Recalculate total
                $updateData['total_amount'] = $this->order->subtotal + $this->shippingCostPreview - $this->order->discount_amount;
            }

            $this->order->update($updateData);

            $this->isEditing = false;
            $this->isSaving = false;
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Delivery information updated successfully!'
            ]);
            
            $this->dispatch('orderUpdated');
        } catch (\Exception $e) {
            $this->isSaving = false;
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update delivery information: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        $zones = DeliveryZone::where('is_active', true)->orderBy('name')->get();
        
        return view('livewire.admin.order.edit-delivery-info', [
            'zones' => $zones,
        ]);
    }
}
