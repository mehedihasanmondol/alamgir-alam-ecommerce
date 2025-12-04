<?php

namespace App\Livewire\Admin\Delivery;

use App\Modules\Ecommerce\Delivery\Models\DeliveryMethod;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * ModuleName: Delivery Management
 * Purpose: Livewire component for delivery methods list with search, filters, and pagination
 * 
 * Key Methods:
 * - updatingSearch(): Reset pagination when search changes
 * - updatingStatusFilter(): Reset pagination when status filter changes
 * - updatingTypeFilter(): Reset pagination when type filter changes
 * - updatingPerPage(): Reset pagination when per page changes
 * - sortByColumn(): Sort by column
 * - toggleStatus(): Toggle method active status
 * - clearFilters(): Clear all filters
 * 
 * Dependencies:
 * - DeliveryMethod Model
 * 
 * @author AI Assistant
 * @date 2025-11-10
 */
class DeliveryMethodList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $perPage = 15;
    public $sortBy = 'name';
    public $sortOrder = 'asc';

    public $showFilters = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
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

    public function toggleStatus($methodId)
    {
        $method = DeliveryMethod::find($methodId);
        if ($method) {
            $method->is_active = !$method->is_active;
            $method->save();
            $this->dispatch('method-updated');
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'typeFilter']);
        $this->resetPage();
    }

    public function render()
    {
        try {
            $query = DeliveryMethod::withCount('rates');

            // Search filter
            if ($this->search) {
                $search = $this->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('carrier_name', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($this->statusFilter !== '') {
                $query->where('is_active', $this->statusFilter === 'active');
            }

            // Type filter
            if ($this->typeFilter !== '') {
                $query->where('calculation_type', $this->typeFilter);
            }

            // Sort
            $query->orderBy($this->sortBy, $this->sortOrder);

            // Paginate
            $methods = $query->paginate($this->perPage);

            // Get available types
            $types = [
                'flat_rate' => 'Flat Rate',
                'weight_based' => 'Weight Based',
                'price_based' => 'Price Based',
                'item_based' => 'Item Based',
                'free' => 'Free Shipping',
            ];

            // Statistics
            $statistics = [
                'total' => DeliveryMethod::count(),
                'active' => DeliveryMethod::where('is_active', true)->count(),
                'inactive' => DeliveryMethod::where('is_active', false)->count(),
                'free_shipping' => DeliveryMethod::where('calculation_type', 'free')->count(),
                'with_rates' => DeliveryMethod::has('rates')->count(),
            ];

            return view('livewire.admin.delivery.delivery-method-list', [
                'methods' => $methods,
                'types' => $types,
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            \Log::error('DeliveryMethodList render error: ' . $e->getMessage());
            return view('livewire.admin.delivery.delivery-method-list', [
                'methods' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'types' => [],
                'statistics' => [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'free_shipping' => 0,
                    'with_rates' => 0,
                ],
                'error' => $e->getMessage(),
            ]);
        }
    }
}
