<?php

namespace App\Livewire\Admin\Blog;

use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\Post;
use App\Modules\Blog\Services\PostService;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class PostList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';
    public $authorFilter = '';
    public $featuredFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 15;

    public $showFilters = false;
    public $showDeleteModal = false;
    public $postToDelete = null;
    
    // Bulk delete properties
    public $selectedPosts = [];
    public $selectAll = false;
    public $showBulkDeleteModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'authorFilter' => ['except' => ''],
        'featuredFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingAuthorFilter()
    {
        $this->resetPage();
    }

    public function updatingFeaturedFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function toggleFeatured($postId, PostService $service)
    {
        $post = Post::find($postId);
        if ($post) {
            $service->toggleFeatured($post);
            $this->dispatch('post-updated');
        }
    }

    public function confirmDelete($postId)
    {
        $this->postToDelete = $postId;
        $this->showDeleteModal = true;
    }

    public function deletePost(PostService $service)
    {
        if ($this->postToDelete) {
            $post = Post::find($this->postToDelete);
            if ($post) {
                $service->deletePost($post->id);
                session()->flash('success', 'Post deleted successfully!');
            }
        }

        $this->showDeleteModal = false;
        $this->postToDelete = null;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'categoryFilter', 'authorFilter', 'featuredFilter', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Select all posts on current page
            $this->selectedPosts = Post::pluck('id')->toArray();
        } else {
            $this->selectedPosts = [];
        }
    }

    public function confirmBulkDelete()
    {
        if (count($this->selectedPosts) > 0) {
            $this->showBulkDeleteModal = true;
        }
    }

    public function bulkDelete(PostService $service)
    {
        if (count($this->selectedPosts) > 0) {
            foreach ($this->selectedPosts as $postId) {
                $post = Post::find($postId);
                if ($post) {
                    $service->deletePost($postId);
                }
            }
            
            session()->flash('success', count($this->selectedPosts) . ' posts deleted successfully!');
            $this->selectedPosts = [];
            $this->selectAll = false;
        }

        $this->showBulkDeleteModal = false;
    }

    public function render()
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFilter,
            'category_id' => $this->categoryFilter,
            'author_id' => $this->authorFilter,
            'featured' => $this->featuredFilter,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== '' && $value !== null;
        });

        $posts = Post::with(['author', 'categories', 'tags'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%')
                      ->orWhere('excerpt', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->categoryFilter, function($query) {
                $query->whereHas('categories', function($q) {
                    $q->where('blog_categories.id', $this->categoryFilter);
                });
            })
            ->when($this->authorFilter, function($query) {
                $query->where('author_id', $this->authorFilter);
            })
            ->when($this->featuredFilter !== '', function($query) {
                $query->where('is_featured', (bool) $this->featuredFilter);
            })
            ->when($this->dateFrom, function($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->latest('created_at')
            ->paginate($this->perPage);

        $categories = BlogCategory::active()->ordered()->get();
        $authors = User::orderBy('name')->get();
        
        // Get counts
        $counts = [
            'all' => Post::count(),
            'published' => Post::where('status', 'published')->count(),
            'draft' => Post::where('status', 'draft')->count(),
            'scheduled' => Post::where('status', 'scheduled')->count(),
            'unlisted' => Post::where('status', 'unlisted')->count(),
        ];

        return view('livewire.admin.blog.post-list', [
            'posts' => $posts,
            'categories' => $categories,
            'authors' => $authors,
            'counts' => $counts,
        ]);
    }
}
