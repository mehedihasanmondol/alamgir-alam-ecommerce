<?php

namespace App\Livewire\Admin\Coupon;

use App\Models\Coupon;
use App\Services\CouponService;
use Livewire\Component;
use Livewire\WithPagination;

class CouponIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $typeFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;

    public $showDeleteModal = false;
    public $couponToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'typeFilter' => ['except' => 'all'],
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

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmDelete($couponId)
    {
        $this->couponToDelete = $couponId;
        $this->showDeleteModal = true;
    }

    public function deleteCoupon(CouponService $couponService)
    {
        if ($this->couponToDelete) {
            $coupon = Coupon::find($this->couponToDelete);
            
            if ($coupon) {
                $couponService->delete($coupon);
                session()->flash('success', 'Coupon deleted successfully.');
            }
        }

        $this->showDeleteModal = false;
        $this->couponToDelete = null;
    }

    public function toggleStatus($couponId)
    {
        $coupon = Coupon::find($couponId);
        
        if ($coupon) {
            $coupon->update(['is_active' => !$coupon->is_active]);
            session()->flash('success', 'Coupon status updated successfully.');
        }
    }

    public function render()
    {
        $query = Coupon::query();

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter === 'active') {
            $query->active();
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        } elseif ($this->statusFilter === 'expired') {
            $query->expired();
        } elseif ($this->statusFilter === 'upcoming') {
            $query->where('starts_at', '>', now());
        }

        // Type filter
        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $coupons = $query->paginate($this->perPage);

        return view('livewire.admin.coupon.coupon-index', [
            'coupons' => $coupons,
        ]);
    }
}
