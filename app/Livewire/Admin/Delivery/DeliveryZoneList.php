<?php

namespace App\Livewire\Admin\Delivery;

use App\Modules\Ecommerce\Delivery\Models\DeliveryZone;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * ModuleName: Delivery Management
 * Purpose: Livewire component for delivery zones list with search, filters, and pagination
 * 
 * Key Methods:
 * - updatingSearch(): Reset pagination when search changes
 * - updatingStatusFilter(): Reset pagination when status filter changes
 * - updatingPerPage(): Reset pagination when per page changes
 * - sortByColumn(): Sort by column
 * - toggleStatus(): Toggle zone active status
 * - clearFilters(): Clear all filters
 * 
 * Dependencies:
 * - DeliveryZone Model
 * 
 * @author AI Assistant
 * @date 2025-11-10
 */
class DeliveryZoneList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 15;
    public $sortBy = 'sort_order';
    public $sortOrder = 'asc';

    public $showFilters = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
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

    public function toggleStatus($zoneId)
    {
        $zone = DeliveryZone::find($zoneId);
        if ($zone) {
            $zone->is_active = !$zone->is_active;
            $zone->save();
            $this->dispatch('zone-updated');
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter']);
        $this->resetPage();
    }

    public function render()
    {
        try {
            $query = DeliveryZone::withCount('rates');

            // Search filter
            if ($this->search) {
                $search = $this->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($this->statusFilter !== '') {
                $query->where('is_active', $this->statusFilter === 'active');
            }

            // Sort
            $query->orderBy($this->sortBy, $this->sortOrder);

            // Paginate
            $zones = $query->paginate($this->perPage);

            // Statistics
            $statistics = [
                'total' => DeliveryZone::count(),
                'active' => DeliveryZone::where('is_active', true)->count(),
                'inactive' => DeliveryZone::where('is_active', false)->count(),
                'total_rates' => DeliveryZone::withCount('rates')->get()->sum('rates_count'),
                'with_rates' => DeliveryZone::has('rates')->count(),
            ];

            return view('livewire.admin.delivery.delivery-zone-list', [
                'zones' => $zones,
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            \Log::error('DeliveryZoneList render error: ' . $e->getMessage());
            return view('livewire.admin.delivery.delivery-zone-list', [
                'zones' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'statistics' => [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'total_rates' => 0,
                    'with_rates' => 0,
                ],
                'error' => $e->getMessage(),
            ]);
        }
    }
}
