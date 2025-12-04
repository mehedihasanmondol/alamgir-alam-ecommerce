<?php

namespace App\Livewire\Admin\Delivery;

use App\Modules\Ecommerce\Delivery\Models\DeliveryRate;
use App\Modules\Ecommerce\Delivery\Models\DeliveryZone;
use App\Modules\Ecommerce\Delivery\Models\DeliveryMethod;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * ModuleName: Delivery Management
 * Purpose: Livewire component for delivery rates list with search, filters, and pagination
 * 
 * Key Methods:
 * - updatingSearch(): Reset pagination when search changes
 * - updatingStatusFilter(): Reset pagination when status filter changes
 * - updatingZoneFilter(): Reset pagination when zone filter changes
 * - updatingMethodFilter(): Reset pagination when method filter changes
 * - updatingPerPage(): Reset pagination when per page changes
 * - sortByColumn(): Sort by column
 * - toggleStatus(): Toggle rate active status
 * - clearFilters(): Clear all filters
 * 
 * Dependencies:
 * - DeliveryRate Model
 * - DeliveryZone Model
 * - DeliveryMethod Model
 * 
 * @author AI Assistant
 * @date 2025-11-10
 */
class DeliveryRateList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $zoneFilter = '';
    public $methodFilter = '';
    public $perPage = 15;
    public $sortBy = 'base_rate';
    public $sortOrder = 'asc';

    public $showFilters = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'zoneFilter' => ['except' => ''],
        'methodFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingZoneFilter()
    {
        $this->resetPage();
    }

    public function updatingMethodFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
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

    public function toggleStatus($rateId)
    {
        $rate = DeliveryRate::find($rateId);
        if ($rate) {
            $rate->is_active = !$rate->is_active;
            $rate->save();
            $this->dispatch('rate-updated');
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'zoneFilter', 'methodFilter']);
        $this->resetPage();
    }

    public function render()
    {
        try {
            $query = DeliveryRate::with(['zone', 'method']);

            // Search filter
            if ($this->search) {
                $search = $this->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('zone', function($zq) use ($search) {
                        $zq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('method', function($mq) use ($search) {
                        $mq->where('name', 'like', "%{$search}%");
                    });
                });
            }

            // Status filter
            if ($this->statusFilter !== '') {
                $query->where('is_active', $this->statusFilter === 'active');
            }

            // Zone filter
            if ($this->zoneFilter !== '') {
                $query->where('delivery_zone_id', $this->zoneFilter);
            }

            // Method filter
            if ($this->methodFilter !== '') {
                $query->where('delivery_method_id', $this->methodFilter);
            }

            // Sort
            $query->orderBy($this->sortBy, $this->sortOrder);

            // Paginate
            $rates = $query->paginate($this->perPage);

            // Get zones and methods for filters
            $zones = DeliveryZone::orderBy('name')->get();
            $methods = DeliveryMethod::orderBy('name')->get();

            // Statistics
            $statistics = [
                'total' => DeliveryRate::count(),
                'active' => DeliveryRate::where('is_active', true)->count(),
                'inactive' => DeliveryRate::where('is_active', false)->count(),
                'avg_base_rate' => DeliveryRate::avg('base_rate') ?? 0,
                'total_zones' => DeliveryZone::count(),
            ];

            return view('livewire.admin.delivery.delivery-rate-list', [
                'rates' => $rates,
                'zones' => $zones,
                'methods' => $methods,
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            \Log::error('DeliveryRateList render error: ' . $e->getMessage());
            return view('livewire.admin.delivery.delivery-rate-list', [
                'rates' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'zones' => collect([]),
                'methods' => collect([]),
                'statistics' => [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'avg_base_rate' => 0,
                    'total_zones' => 0,
                ],
                'error' => $e->getMessage(),
            ]);
        }
    }
}
