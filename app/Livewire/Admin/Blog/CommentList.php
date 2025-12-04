<?php

namespace App\Livewire\Admin\Blog;

use App\Modules\Blog\Models\Comment;
use Livewire\Component;
use Livewire\WithPagination;

class CommentList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $postFilter = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $perPage = 15;
    
    public $showFilters = false;
    public $showDeleteModal = false;
    public $showViewModal = false;
    public $selectedComment = null;
    public $commentToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortOrder' => ['except' => 'desc'],
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

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'postFilter']);
        $this->resetPage();
    }

    public function approveComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $comment->status = 'approved';
        $comment->approved_at = now();
        $comment->approved_by = auth()->id();
        $comment->save();

        session()->flash('success', 'Comment approved successfully!');
    }

    public function markAsSpam($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $comment->status = 'spam';
        $comment->save();

        session()->flash('success', 'Comment marked as spam!');
    }

    public function confirmDelete($commentId)
    {
        $this->commentToDelete = $commentId;
        $this->showDeleteModal = true;
    }

    public function deleteComment()
    {
        if ($this->commentToDelete) {
            try {
                Comment::findOrFail($this->commentToDelete)->delete();
                session()->flash('success', 'Comment deleted permanently!');
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to delete comment: ' . $e->getMessage());
            }
        }

        $this->showDeleteModal = false;
        $this->commentToDelete = null;
    }

    public function viewComment($commentId)
    {
        $this->selectedComment = Comment::with(['post', 'user'])->find($commentId);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedComment = null;
    }

    public function render()
    {
        try {
            $query = Comment::with(['post', 'user']);

            // Search filter
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('content', 'like', '%' . $this->search . '%')
                        ->orWhere('guest_name', 'like', '%' . $this->search . '%')
                        ->orWhere('guest_email', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('post', function ($postQuery) {
                            $postQuery->where('title', 'like', '%' . $this->search . '%');
                        });
                });
            }

            // Status filter
            if ($this->statusFilter !== '') {
                $query->where('status', $this->statusFilter);
            }

            // Post filter
            if ($this->postFilter) {
                $query->where('blog_post_id', $this->postFilter);
            }

            // Sorting
            $query->orderBy($this->sortBy, $this->sortOrder);

            $comments = $query->paginate($this->perPage);

            // Get statistics
            $stats = [
                'total' => Comment::count(),
                'pending' => Comment::where('status', 'pending')->count(),
                'approved' => Comment::where('status', 'approved')->count(),
                'spam' => Comment::where('status', 'spam')->count(),
            ];

            return view('livewire.admin.blog.comment-list', [
                'comments' => $comments,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            \Log::error('CommentList render error: ' . $e->getMessage());
            return view('livewire.admin.blog.comment-list', [
                'comments' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'stats' => [
                    'total' => 0,
                    'pending' => 0,
                    'approved' => 0,
                    'spam' => 0,
                ],
                'error' => $e->getMessage(),
            ]);
        }
    }
}
