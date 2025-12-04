<?php

namespace App\Livewire\Order;

use App\Modules\Ecommerce\Order\Models\Order;
use App\Modules\Ecommerce\Order\Services\OrderStatusService;
use Livewire\Component;

class OrderStatusUpdater extends Component
{
    public Order $order;
    public string $newStatus = '';
    public string $notes = '';
    public bool $notifyCustomer = true;
    public string $trackingNumber = '';
    public string $carrier = '';
    public array $availableStatuses = [];
    public bool $showNoteForm = false;
    public string $pendingStatus = '';

    protected $rules = [
        'newStatus' => 'required|string',
        'notes' => 'nullable|string|max:1000',
        'notifyCustomer' => 'boolean',
        'trackingNumber' => 'nullable|string|max:100',
        'carrier' => 'nullable|string|max:100',
    ];

    public function mount(Order $order, array $availableStatuses)
    {
        $this->order = $order;
        $this->availableStatuses = $availableStatuses;
        $this->newStatus = $order->status;
        $this->trackingNumber = $order->tracking_number ?? '';
        $this->carrier = $order->carrier ?? '';
    }

    public function updatedNewStatus($value)
    {
        // When status changes, show note form
        if ($value !== $this->order->status) {
            $this->pendingStatus = $value;
            $this->showNoteForm = true;
        } else {
            $this->showNoteForm = false;
            $this->pendingStatus = '';
        }
    }

    public function updateStatus()
    {
        $this->validate();

        try {
            $statusService = app(OrderStatusService::class);

            // Update tracking info if provided
            if (!empty($this->trackingNumber)) {
                $this->order->update([
                    'tracking_number' => $this->trackingNumber,
                    'carrier' => $this->carrier,
                ]);
            }

            // Update status
            $statusService->updateStatus(
                $this->order,
                $this->newStatus,
                $this->notes,
                $this->notifyCustomer
            );

            // Flash success message and redirect to refresh page
            session()->flash('success', 'Order status updated successfully.');
            
            return redirect()->route('admin.orders.show', $this->order);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update status: ' . $e->getMessage());
            
            return redirect()->route('admin.orders.show', $this->order);
        }
    }

    public function skipNote()
    {
        // Update status without note
        $this->notes = '';
        $this->updateStatus();
    }

    public function cancelStatusChange()
    {
        // Revert to original status
        $this->newStatus = $this->order->status;
        $this->showNoteForm = false;
        $this->pendingStatus = '';
        $this->notes = '';
    }

    public function render()
    {
        return view('livewire.order.order-status-updater');
    }
}
