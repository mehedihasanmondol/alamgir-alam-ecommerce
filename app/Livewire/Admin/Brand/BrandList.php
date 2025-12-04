<?php

namespace App\Livewire\Admin\Brand;

use App\Modules\Ecommerce\Brand\Models\Brand;
use App\Modules\Ecommerce\Brand\Repositories\BrandRepository;
use App\Modules\Ecommerce\Brand\Services\BrandService;
use Livewire\Component;
use Livewire\WithPagination;

class BrandList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = ''; // active/inactive filter
    public $featuredFilter = ''; // featured filter
    public $perPage = 15;
    public $sortBy = 'id';
    public $sortOrder = 'desc';

    public $showFilters = false;
    public $showDeleteModal = false;
    public $brandToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'featuredFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingFeaturedFilter()
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

    public function toggleStatus($brandId, BrandService $service)
    {
        $brand = Brand::find($brandId);
        if ($brand) {
            $service->toggleStatus($brand);
            $this->dispatch('brand-updated');
        }
    }

    public function toggleFeatured($brandId, BrandService $service)
    {
        $brand = Brand::find($brandId);
        if ($brand) {
            $service->toggleFeatured($brand);
            $this->dispatch('brand-updated');
        }
    }

    public function confirmDelete($brandId)
    {
        $this->brandToDelete = $brandId;
        $this->showDeleteModal = true;
    }

    public function deleteBrand(BrandService $service)
    {
        if ($this->brandToDelete) {
            $brand = Brand::find($this->brandToDelete);
            if ($brand) {
                $service->delete($brand);
                session()->flash('success', 'Brand deleted successfully!');
            }
        }

        $this->showDeleteModal = false;
        $this->brandToDelete = null;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'featuredFilter']);
        $this->resetPage();
    }

    public function render(BrandRepository $repository, BrandService $service)
    {
        try {
            $filters = [
                'search' => $this->search,
                'sort_by' => $this->sortBy,
                'sort_order' => $this->sortOrder,
            ];

            if ($this->statusFilter !== '') {
                $filters['is_active'] = $this->statusFilter === 'active' ? '1' : '0';
            }

            if ($this->featuredFilter !== '') {
                $filters['is_featured'] = $this->featuredFilter === 'featured' ? '1' : '0';
            }

            $brands = $repository->paginate($this->perPage, $filters);
            $statistics = $service->getStatistics();

            return view('livewire.admin.brand.brand-list', [
                'brands' => $brands,
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            \Log::error('BrandList render error: ' . $e->getMessage());
            return view('livewire.admin.brand.brand-list', [
                'brands' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'statistics' => [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'featured' => 0,
                ],
                'error' => $e->getMessage(),
            ]);
        }
    }
}
