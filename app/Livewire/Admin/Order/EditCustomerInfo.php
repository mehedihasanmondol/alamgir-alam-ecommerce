<?php

namespace App\Livewire\Admin\Order;

use App\Modules\Ecommerce\Order\Models\Order;
use Livewire\Component;

class EditCustomerInfo extends Component
{
    public Order $order;
    public $customer_name;
    public $customer_email;
    public $customer_phone;
    public $customer_notes;
    public $isEditing = false;
    public $isSaving = false;

    protected $rules = [
        'customer_name' => 'required|string|max:255',
        'customer_email' => 'nullable|email|max:255',
        'customer_phone' => 'required|string|max:20',
        'customer_notes' => 'nullable|string|max:1000',
    ];

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->customer_name = $order->customer_name;
        $this->customer_email = $order->customer_email;
        $this->customer_phone = $order->customer_phone;
        $this->customer_notes = $order->customer_notes;
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        
        if (!$this->isEditing) {
            // Reset to original values if canceling
            $this->customer_name = $this->order->customer_name;
            $this->customer_email = $this->order->customer_email;
            $this->customer_phone = $this->order->customer_phone;
            $this->customer_notes = $this->order->customer_notes;
            $this->resetValidation();
        }
    }

    public function save()
    {
        $this->validate();
        
        $this->isSaving = true;

        try {
            $this->order->update([
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_phone' => $this->customer_phone,
                'customer_notes' => $this->customer_notes,
            ]);

            $this->isEditing = false;
            $this->isSaving = false;
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Customer information updated successfully!'
            ]);
        } catch (\Exception $e) {
            $this->isSaving = false;
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update customer information: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.order.edit-customer-info');
    }
}
