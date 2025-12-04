<?php

namespace App\Livewire\Admin\Order;

use App\Modules\Ecommerce\Order\Models\Order;
use Livewire\Component;

class EditShippingTracking extends Component
{
    public Order $order;
    public $tracking_number;
    public $carrier;
    public $shipped_at;
    public $delivered_at;
    public $isEditing = false;
    public $isSaving = false;

    protected $rules = [
        'tracking_number' => 'nullable|string|max:255',
        'carrier' => 'nullable|string|max:255',
        'shipped_at' => 'nullable|date',
        'delivered_at' => 'nullable|date|after_or_equal:shipped_at',
    ];

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->tracking_number = $order->tracking_number;
        $this->carrier = $order->carrier;
        $this->shipped_at = $order->shipped_at ? $order->shipped_at->format('Y-m-d\TH:i') : null;
        $this->delivered_at = $order->delivered_at ? $order->delivered_at->format('Y-m-d\TH:i') : null;
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        
        if (!$this->isEditing) {
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

            $this->order->update($updateData);

            $this->isEditing = false;
            $this->isSaving = false;
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Shipping & tracking information updated successfully!'
            ]);
            
            $this->dispatch('orderUpdated');
        } catch (\Exception $e) {
            $this->isSaving = false;
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update shipping information: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.order.edit-shipping-tracking');
    }
}
