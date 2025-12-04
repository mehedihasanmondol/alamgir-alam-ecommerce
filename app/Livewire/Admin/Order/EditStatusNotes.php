<?php

namespace App\Livewire\Admin\Order;

use App\Modules\Ecommerce\Order\Models\Order;
use Livewire\Component;

class EditStatusNotes extends Component
{
    public Order $order;
    public $status;
    public $admin_notes;
    public $isEditing = false;
    public $isSaving = false;

    protected $rules = [
        'status' => 'required|in:pending,processing,confirmed,shipped,delivered,cancelled,refunded,on_hold',
        'admin_notes' => 'nullable|string|max:2000',
    ];

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->status = $order->status;
        $this->admin_notes = $order->admin_notes;
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        
        if (!$this->isEditing) {
            $this->status = $this->order->status;
            $this->admin_notes = $this->order->admin_notes;
            $this->resetValidation();
        }
    }

    public function save()
    {
        $this->validate();
        
        $this->isSaving = true;

        try {
            $oldStatus = $this->order->status;
            
            $this->order->update([
                'status' => $this->status,
                'admin_notes' => $this->admin_notes,
            ]);
            
            // Create status history if status changed
            if ($oldStatus !== $this->status) {
                $this->order->statusHistories()->create([
                    'old_status' => $oldStatus,
                    'new_status' => $this->status,
                    'user_id' => auth()->id(),
                    'notes' => 'Status updated via order edit',
                ]);
                
                // Update timestamps based on status
                if ($this->status === 'shipped' && !$this->order->shipped_at) {
                    $this->order->update(['shipped_at' => now()]);
                }
                if ($this->status === 'delivered' && !$this->order->delivered_at) {
                    $this->order->update(['delivered_at' => now()]);
                }
                if ($this->status === 'cancelled' && !$this->order->cancelled_at) {
                    $this->order->update(['cancelled_at' => now()]);
                }
            }

            $this->isEditing = false;
            $this->isSaving = false;
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Status & notes updated successfully!'
            ]);
            
            $this->dispatch('orderUpdated');
        } catch (\Exception $e) {
            $this->isSaving = false;
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update status: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.order.edit-status-notes');
    }
}
