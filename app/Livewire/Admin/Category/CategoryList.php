<?php

namespace App\Livewire\Admin\Category;

use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Category\Services\CategoryService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $parentFilter = '';
    public $perPage = 15;
    public $sortBy = 'sort_order';
    public $sortOrder = 'asc';

    public $showFilters = false;
    public $showDeleteModal = false;
    public $categoryToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'parentFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingParentFilter()
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

    public function toggleStatus($categoryId)
    {
        $category = Category::find($categoryId);
        if ($category) {
            $category->is_active = !$category->is_active;
            $category->save();
            $this->dispatch('category-updated');
        }
    }

    public function confirmDelete($categoryId)
    {
        $this->categoryToDelete = $categoryId;
        $this->showDeleteModal = true;
    }

    public function deleteCategory(CategoryService $service)
    {
        if ($this->categoryToDelete) {
            $category = Category::find($this->categoryToDelete);
            if ($category) {
                try {
                    $service->delete($category);
                    session()->flash('success', 'Category deleted successfully!');
                } catch (\Exception $e) {
                    session()->flash('error', $e->getMessage());
                }
            }
        }

        $this->showDeleteModal = false;
        $this->categoryToDelete = null;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'parentFilter']);
        $this->resetPage();
    }

    public function render()
    {
        try {
            $query = Category::withCount('children');

            // Search filter
            if ($this->search) {
                $search = $this->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($this->statusFilter !== '') {
                $query->where('is_active', $this->statusFilter === 'active');
            }

            // Parent category filter
            if ($this->parentFilter !== '') {
                if ($this->parentFilter === 'null') {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $this->parentFilter);
                }
            }

            // Sort
            $query->orderBy($this->sortBy, $this->sortOrder);

            // Paginate
            $categories = $query->with('parent')->paginate($this->perPage);

            // Get all parent categories for filter dropdown
            $parents = Category::whereNull('parent_id')
                ->orderBy('name')
                ->get();

            // Statistics
            $statistics = [
                'total' => Category::count(),
                'active' => Category::where('is_active', true)->count(),
                'inactive' => Category::where('is_active', false)->count(),
                'parents' => Category::whereNull('parent_id')->count(),
                'with_children' => Category::has('children')->count(),
            ];

            return view('livewire.admin.category.category-list', [
                'categories' => $categories,
                'parents' => $parents,
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            Log::error('CategoryList render error: ' . $e->getMessage());
            return view('livewire.admin.category.category-list', [
                'categories' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'parents' => collect([]),
                'statistics' => [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'parents' => 0,
                    'with_children' => 0,
                ],
                'error' => $e->getMessage(),
            ]);
        }
    }
}
