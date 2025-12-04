<?php

namespace App\Livewire\Admin\Product;

use App\Modules\Ecommerce\Brand\Models\Brand;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Repositories\ProductRepository;
use App\Modules\Ecommerce\Product\Services\ProductService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $brandFilter = '';
    public $typeFilter = '';
    public $statusFilter = ''; // active/inactive filter
    public $publishStatusFilter = ''; // draft/published filter
    public $perPage = 15;
    public $sortBy = 'id';
    public $sortOrder = 'desc';

    public $showFilters = false;
    public $showDeleteModal = false;
    public $productToDelete = null;
    
    // Bulk delete properties
    public $selectedProducts = [];
    public $selectAll = false;
    public $showBulkDeleteModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'brandFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'publishStatusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingBrandFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPublishStatusFilter()
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

    public function toggleFeatured($productId, ProductService $service)
    {
        $product = Product::find($productId);
        if ($product) {
            $service->toggleFeatured($product);
            $this->dispatch('product-updated');
        }
    }

    public function toggleActive($productId, ProductService $service)
    {
        $product = Product::find($productId);
        if ($product) {
            $service->toggleActive($product);
            $this->dispatch('product-updated');
        }
    }

    public function confirmDelete($productId)
    {
        $this->productToDelete = $productId;
        $this->showDeleteModal = true;
    }

    public function deleteProduct(ProductService $service)
    {
        if ($this->productToDelete) {
            $product = Product::find($this->productToDelete);
            if ($product) {
                $service->delete($product);
                session()->flash('success', 'Product deleted successfully!');
            }
        }

        $this->showDeleteModal = false;
        $this->productToDelete = null;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'categoryFilter', 'brandFilter', 'typeFilter', 'statusFilter', 'publishStatusFilter']);
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Select all products on current page
            $this->selectedProducts = Product::pluck('id')->toArray();
        } else {
            $this->selectedProducts = [];
        }
    }

    public function confirmBulkDelete()
    {
        if (count($this->selectedProducts) > 0) {
            $this->showBulkDeleteModal = true;
        }
    }

    public function bulkDelete(ProductService $service)
    {
        if (count($this->selectedProducts) > 0) {
            foreach ($this->selectedProducts as $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $service->delete($product);
                }
            }
            
            session()->flash('success', count($this->selectedProducts) . ' products deleted successfully!');
            $this->selectedProducts = [];
            $this->selectAll = false;
        }

        $this->showBulkDeleteModal = false;
    }

    public function render(ProductRepository $repository)
    {
        try {
            $filters = [
                'search' => $this->search,
                'category_id' => $this->categoryFilter,
                'brand_id' => $this->brandFilter,
                'product_type' => $this->typeFilter,
                'sort_by' => $this->sortBy,
                'sort_order' => $this->sortOrder,
            ];

            if ($this->statusFilter !== '') {
                $filters['is_active'] = $this->statusFilter === 'active';
            }

            if ($this->publishStatusFilter !== '') {
                $filters['status'] = $this->publishStatusFilter;
            }

            $products = $repository->paginate($this->perPage, $filters);
            $categories = Category::orderBy('name')->get();
            $brands = Brand::orderBy('name')->get();

            return view('livewire.admin.product.product-list', [
                'products' => $products,
                'categories' => $categories,
                'brands' => $brands,
            ]);
        } catch (\Exception $e) {
            Log::error('ProductList render error: ' . $e->getMessage());
            return view('livewire.admin.product.product-list', [
                'products' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'categories' => collect([]),
                'brands' => collect([]),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
