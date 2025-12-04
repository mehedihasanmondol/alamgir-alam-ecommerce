<?php

namespace App\Livewire\Admin\Blog;

use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Services\BlogCategoryService;
use Livewire\Component;
use Livewire\WithPagination;

class BlogCategoryList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = ''; // active/inactive filter
    public $parentFilter = ''; // root/child filter
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

    public function toggleStatus($categoryId, BlogCategoryService $service)
    {
        $category = BlogCategory::find($categoryId);
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

    public function deleteCategory(BlogCategoryService $service)
    {
        if ($this->categoryToDelete) {
            try {
                $service->deleteCategory($this->categoryToDelete);
                session()->flash('success', 'Category deleted successfully!');
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to delete category: ' . $e->getMessage());
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
            $query = BlogCategory::with(['parent.parent', 'posts']);

            // Apply search filter
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            }

            // Apply status filter
            if ($this->statusFilter !== '') {
                $query->where('is_active', $this->statusFilter === 'active' ? 1 : 0);
            }

            // Apply parent filter
            if ($this->parentFilter === 'root') {
                $query->whereNull('parent_id');
            } elseif ($this->parentFilter === 'child') {
                $query->whereNotNull('parent_id');
            }

            // Apply sorting
            $query->orderBy($this->sortBy, $this->sortOrder);

            $categories = $query->paginate($this->perPage);

            // Calculate statistics
            $statistics = [
                'total' => BlogCategory::count(),
                'active' => BlogCategory::where('is_active', true)->count(),
                'inactive' => BlogCategory::where('is_active', false)->count(),
                'root' => BlogCategory::whereNull('parent_id')->count(),
            ];

            return view('livewire.admin.blog.blog-category-list', [
                'categories' => $categories,
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            \Log::error('BlogCategoryList render error: ' . $e->getMessage());
            return view('livewire.admin.blog.blog-category-list', [
                'categories' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'statistics' => [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'root' => 0,
                ],
                'error' => $e->getMessage(),
            ]);
        }
    }
}
