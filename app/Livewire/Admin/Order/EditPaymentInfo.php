<?php

namespace App\Livewire\Admin\Order;

use App\Modules\Ecommerce\Order\Models\Order;
use Livewire\Component;

class EditPaymentInfo extends Component
{
    public Order $order;
    public $payment_method;
    public $payment_status;
    public $transaction_id;
    public $isEditing = false;
    public $isSaving = false;

    protected $rules = [
        'payment_method' => 'required|in:cod,bkash,nagad,rocket,card,bank_transfer,online',
        'payment_status' => 'required|in:pending,paid,failed,refunded,partially_paid',
        'transaction_id' => 'nullable|string|max:255',
    ];

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->payment_method = $order->payment_method;
        $this->payment_status = $order->payment_status;
        $this->transaction_id = $order->transaction_id;
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        
        if (!$this->isEditing) {
            $this->payment_method = $this->order->payment_method;
            $this->payment_status = $this->order->payment_status;
            $this->transaction_id = $this->order->transaction_id;
            $this->resetValidation();
        }
    }

    public function save()
    {
        $this->validate();
        
        $this->isSaving = true;

        try {
            $this->order->update([
                'payment_method' => $this->payment_method,
                'payment_status' => $this->payment_status,
                'transaction_id' => $this->transaction_id,
            ]);

            // Update paid_at timestamp if status changed to paid
            if ($this->payment_status === 'paid' && !$this->order->paid_at) {
                $this->order->update(['paid_at' => now()]);
            }

            $this->isEditing = false;
            $this->isSaving = false;
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Payment information updated successfully!'
            ]);
        } catch (\Exception $e) {
            $this->isSaving = false;
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update payment information: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.order.edit-payment-info');
    }
}
