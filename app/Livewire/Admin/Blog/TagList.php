<?php

namespace App\Livewire\Admin\Blog;

use App\Modules\Blog\Models\Tag;
use App\Modules\Blog\Services\TagService;
use Livewire\Component;
use Livewire\WithPagination;

class TagList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = ''; // active/inactive filter
    public $perPage = 15;
    public $sortBy = 'name';
    public $sortOrder = 'asc';

    public $showFilters = false;
    public $showDeleteModal = false;
    public $tagToDelete = null;

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

    public function toggleStatus($tagId, TagService $service)
    {
        $tag = Tag::find($tagId);
        if ($tag) {
            $tag->is_active = !$tag->is_active;
            $tag->save();
            $this->dispatch('tag-updated');
        }
    }

    public function confirmDelete($tagId)
    {
        $this->tagToDelete = $tagId;
        $this->showDeleteModal = true;
    }

    public function deleteTag(TagService $service)
    {
        if ($this->tagToDelete) {
            try {
                $service->deleteTag($this->tagToDelete);
                session()->flash('success', 'Tag deleted successfully!');
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to delete tag: ' . $e->getMessage());
            }
        }

        $this->showDeleteModal = false;
        $this->tagToDelete = null;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter']);
        $this->resetPage();
    }

    public function render()
    {
        try {
            $query = Tag::withCount('posts');

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

            // Apply sorting
            $query->orderBy($this->sortBy, $this->sortOrder);

            $tags = $query->paginate($this->perPage);

            // Calculate statistics
            $statistics = [
                'total' => Tag::count(),
                'active' => Tag::where('is_active', true)->count(),
                'inactive' => Tag::where('is_active', false)->count(),
                'used' => Tag::has('posts')->count(),
            ];

            return view('livewire.admin.blog.tag-list', [
                'tags' => $tags,
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            \Log::error('TagList render error: ' . $e->getMessage());
            return view('livewire.admin.blog.tag-list', [
                'tags' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'statistics' => [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'used' => 0,
                ],
                'error' => $e->getMessage(),
            ]);
        }
    }
}
