<?php

namespace App\Livewire\Admin\Order;

use App\Modules\Ecommerce\Order\Models\Order;
use App\Modules\Ecommerce\Delivery\Models\DeliveryZone;
use App\Modules\Ecommerce\Delivery\Models\DeliveryMethod;
use App\Modules\Ecommerce\Delivery\Models\DeliveryRate;
use Livewire\Component;
use Livewire\WithPagination;

class OrderListWithDelivery extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $deliveryStatusFilter = '';
    public $deliveryMethodFilter = '';
    public $deliveryZoneFilter = '';
    public $perPage = 15;
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $showFilters = false;

    // For delivery assignment modal
    public $selectedOrderId = null;
    public $showDeliveryModal = false;
    public $assignDeliveryZoneId = null;
    public $assignDeliveryMethodId = null;
    public $trackingNumber = '';
    public $estimatedDelivery = '';
    public $carrierName = '';
    public $availableRates = [];
    public $selectedRate = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'deliveryStatusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDeliveryStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingAssignDeliveryZoneId()
    {
        $this->loadAvailableRates();
    }

    public function updatingAssignDeliveryMethodId()
    {
        $this->loadAvailableRates();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortOrder = 'asc';
        }
    }

    public function openDeliveryModal($orderId)
    {
        $this->selectedOrderId = $orderId;
        $this->showDeliveryModal = true;
        
        // Load existing delivery info if available
        $order = Order::find($orderId);
        if ($order) {
            $this->assignDeliveryZoneId = $order->delivery_zone_id;
            $this->assignDeliveryMethodId = $order->delivery_method_id;
            $this->trackingNumber = $order->tracking_number;
            $this->estimatedDelivery = $order->estimated_delivery ? $order->estimated_delivery->format('Y-m-d') : '';
            $this->carrierName = $order->carrier;
            
            if ($this->assignDeliveryZoneId && $this->assignDeliveryMethodId) {
                $this->loadAvailableRates();
            }
        }
    }

    public function loadAvailableRates()
    {
        if ($this->assignDeliveryZoneId && $this->assignDeliveryMethodId) {
            $this->availableRates = DeliveryRate::where('delivery_zone_id', $this->assignDeliveryZoneId)
                ->where('delivery_method_id', $this->assignDeliveryMethodId)
                ->where('is_active', true)
                ->get();
            
            if ($this->availableRates->count() > 0) {
                $this->selectedRate = $this->availableRates->first()->id;
            }
        } else {
            $this->availableRates = [];
            $this->selectedRate = null;
        }
    }

    public function assignDelivery()
    {
        $this->validate([
            'assignDeliveryZoneId' => 'required|exists:delivery_zones,id',
            'assignDeliveryMethodId' => 'required|exists:delivery_methods,id',
            'selectedRate' => 'required|exists:delivery_rates,id',
            'trackingNumber' => 'nullable|string|max:255',
            'estimatedDelivery' => 'nullable|date',
            'carrierName' => 'nullable|string|max:255',
        ]);

        $order = Order::find($this->selectedOrderId);
        $zone = DeliveryZone::find($this->assignDeliveryZoneId);
        $method = DeliveryMethod::find($this->assignDeliveryMethodId);
        $rate = DeliveryRate::find($this->selectedRate);

        if ($rate) {
            // Calculate total shipping cost
            $shippingCost = $rate->base_rate + $rate->handling_fee + $rate->insurance_fee + $rate->cod_fee;
            
            $order->update([
                'delivery_zone_id' => $this->assignDeliveryZoneId,
                'delivery_method_id' => $this->assignDeliveryMethodId,
                'delivery_zone_name' => $zone->name,
                'delivery_method_name' => $method->name,
                'tracking_number' => $this->trackingNumber,
                'estimated_delivery' => $this->estimatedDelivery,
                'carrier' => $this->carrierName ?: $method->carrier_name,
                'base_shipping_cost' => $rate->base_rate,
                'handling_fee' => $rate->handling_fee,
                'insurance_fee' => $rate->insurance_fee,
                'cod_fee' => $rate->cod_fee,
                'shipping_cost' => $shippingCost,
                'delivery_status' => 'assigned',
            ]);

            // Recalculate total amount
            $order->total_amount = $order->subtotal + $order->tax_amount + $shippingCost - $order->discount_amount;
            $order->save();

            $this->dispatch('delivery-assigned', [
                'message' => 'Delivery assigned successfully!',
                'orderId' => $order->id,
            ]);

            $this->closeDeliveryModal();
        } else {
            $this->dispatch('delivery-error', [
                'message' => 'No rate found for this zone and method combination.',
            ]);
        }
    }

    public function updateDeliveryStatus($orderId, $status)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->delivery_status = $status;
            
            // Update timestamps based on status
            switch ($status) {
                case 'picked_up':
                    $order->picked_up_at = now();
                    break;
                case 'in_transit':
                    $order->in_transit_at = now();
                    break;
                case 'out_for_delivery':
                    $order->out_for_delivery_at = now();
                    break;
                case 'delivered':
                    $order->delivered_at = now();
                    $order->status = 'completed';
                    break;
            }
            
            $order->save();

            $this->dispatch('status-updated', [
                'message' => 'Delivery status updated successfully!',
                'orderId' => $order->id,
            ]);
        }
    }

    public function closeDeliveryModal()
    {
        $this->showDeliveryModal = false;
        $this->reset([
            'selectedOrderId', 
            'assignDeliveryZoneId', 
            'assignDeliveryMethodId', 
            'trackingNumber', 
            'estimatedDelivery', 
            'carrierName',
            'availableRates',
            'selectedRate'
        ]);
    }

    public function clearFilters()
    {
        $this->reset([
            'search', 
            'statusFilter', 
            'deliveryStatusFilter', 
            'deliveryMethodFilter', 
            'deliveryZoneFilter'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::with(['user', 'items.product', 'deliveryZone', 'deliveryMethod']);

        // Search
        if ($this->search) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        // Delivery status filter
        if ($this->deliveryStatusFilter !== '') {
            $query->where('delivery_status', $this->deliveryStatusFilter);
        }

        // Delivery method filter
        if ($this->deliveryMethodFilter !== '') {
            $query->where('delivery_method_id', $this->deliveryMethodFilter);
        }

        // Delivery zone filter
        if ($this->deliveryZoneFilter !== '') {
            $query->where('delivery_zone_id', $this->deliveryZoneFilter);
        }

        // Sort
        $query->orderBy($this->sortBy, $this->sortOrder);

        // Paginate
        $orders = $query->paginate($this->perPage);

        // Get filter options
        $zones = DeliveryZone::where('is_active', true)->orderBy('name')->get();
        $methods = DeliveryMethod::where('is_active', true)->orderBy('name')->get();

        // Statistics
        $statistics = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'completed')->count(),
            'awaiting_delivery' => Order::whereNull('delivery_method_id')->count(),
            'in_transit' => Order::where('delivery_status', 'in_transit')->count(),
        ];

        return view('livewire.admin.order.order-list-with-delivery', [
            'orders' => $orders,
            'zones' => $zones,
            'methods' => $methods,
            'statistics' => $statistics,
        ]);
    }
}
