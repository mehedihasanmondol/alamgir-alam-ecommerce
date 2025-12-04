<?php

namespace App\Livewire\Admin\Order;

use App\Modules\Ecommerce\Order\Models\Order;
use Livewire\Component;

class EditAddresses extends Component
{
    public Order $order;
    
    // Shipping Address
    public $shipping_name;
    public $shipping_email;
    public $shipping_phone;
    public $shipping_address;
    
    // Billing Address
    public $billing_name;
    public $billing_email;
    public $billing_phone;
    public $billing_address;
    
    public $sameAsShipping = false;
    public $isEditing = false;
    public $isSaving = false;

    protected $rules = [
        'shipping_name' => 'required|string|max:255',
        'shipping_email' => 'nullable|email|max:255',
        'shipping_phone' => 'required|string|max:20',
        'shipping_address' => 'required|string|max:500',
        
        'billing_name' => 'nullable|string|max:255',
        'billing_email' => 'nullable|email|max:255',
        'billing_phone' => 'nullable|string|max:20',
        'billing_address' => 'nullable|string|max:500',
    ];

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->loadAddresses();
    }

    public function loadAddresses()
    {
        // Shipping Address - use customer data as fallback
        $this->shipping_name = $this->order->shipping_name ?? $this->order->customer_name ?? '';
        $this->shipping_email = $this->order->shipping_email ?? $this->order->customer_email ?? '';
        $this->shipping_phone = $this->order->shipping_phone ?? $this->order->customer_phone ?? '';
        $this->shipping_address = $this->order->shipping_address ?? '';
        
        // Billing Address
        $this->billing_name = $this->order->billing_name ?? '';
        $this->billing_email = $this->order->billing_email ?? '';
        $this->billing_phone = $this->order->billing_phone ?? '';
        $this->billing_address = $this->order->billing_address ?? '';
        
        // Check if billing is same as shipping (if billing fields are empty)
        if (empty($this->billing_name) && empty($this->billing_address)) {
            $this->sameAsShipping = true;
        }
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        
        if (!$this->isEditing) {
            $this->loadAddresses();
            $this->resetValidation();
        }
    }

    public function updatedSameAsShipping($value)
    {
        if ($value) {
            $this->copyShippingToBilling();
        } else {
            // Clear billing fields when unchecked
            $this->billing_name = '';
            $this->billing_email = '';
            $this->billing_phone = '';
            $this->billing_address = '';
        }
    }

    public function copyShippingToBilling()
    {
        $this->billing_name = $this->shipping_name;
        $this->billing_email = $this->shipping_email;
        $this->billing_phone = $this->shipping_phone;
        $this->billing_address = $this->shipping_address;
    }

    public function save()
    {
        $this->validate();
        
        $this->isSaving = true;

        try {
            $this->order->update([
                'shipping_name' => $this->shipping_name,
                'shipping_email' => $this->shipping_email,
                'shipping_phone' => $this->shipping_phone,
                'shipping_address' => $this->shipping_address,
                
                'billing_name' => $this->billing_name,
                'billing_email' => $this->billing_email,
                'billing_phone' => $this->billing_phone,
                'billing_address' => $this->billing_address,
            ]);

            $this->isEditing = false;
            $this->isSaving = false;
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Addresses updated successfully!'
            ]);

        } catch (\Exception $e) {
            $this->isSaving = false;
            
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update addresses: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.order.edit-addresses');
    }
}
