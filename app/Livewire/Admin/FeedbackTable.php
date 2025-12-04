<?php

namespace App\Livewire\Admin;

use App\Models\Feedback;
use App\Services\FeedbackService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Admin Feedback Table Livewire Component
 * 
 * Manages feedback with filtering, sorting, and bulk actions
 * Similar to ProductReviewTable functionality
 * 
 * @category Livewire
 * @package  Admin
 * @author   Windsurf AI
 * @created  2025-11-25
 */
class FeedbackTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $ratingFilter = '';
    public $featuredFilter = '';
    public $perPage = 15;
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';

    public $showFilters = false;
    public $showDeleteModal = false;
    public $showViewModal = false;
    public $feedbackToDelete = null;
    public $selectedFeedback = null;

    // Bulk actions
    public $selectedItems = [];
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'ratingFilter' => ['except' => ''],
        'featuredFilter' => ['except' => ''],
    ];

    protected $listeners = [
        'feedback-deleted' => '$refresh',
        'feedback-approved' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingRatingFilter()
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

    public function approve($id)
    {
        $feedback = Feedback::findOrFail($id);
        app(FeedbackService::class)->approveFeedback($feedback);
        
        // Close modal if open
        $this->showViewModal = false;
        $this->selectedFeedback = null;
        
        session()->flash('success', 'Feedback approved successfully!');
        $this->dispatch('feedback-approved');
        $this->dispatch('show-toast', [
            'message' => 'Feedback approved successfully!',
            'type' => 'success'
        ]);
    }

    public function reject($id)
    {
        $feedback = Feedback::findOrFail($id);
        app(FeedbackService::class)->rejectFeedback($feedback);
        
        // Close modal if open
        $this->showViewModal = false;
        $this->selectedFeedback = null;
        
        session()->flash('success', 'Feedback rejected successfully!');
        $this->dispatch('show-toast', [
            'message' => 'Feedback rejected successfully!',
            'type' => 'success'
        ]);
    }

    public function toggleFeatured($id)
    {
        $feedback = Feedback::findOrFail($id);
        app(FeedbackService::class)->toggleFeatured($feedback);
        
        $message = $feedback->fresh()->is_featured 
            ? 'Feedback marked as featured!' 
            : 'Feedback removed from featured!';
        
        session()->flash('success', $message);
    }

    public function confirmDelete($id)
    {
        $this->feedbackToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteFeedback()
    {
        if ($this->feedbackToDelete) {
            $feedback = Feedback::findOrFail($this->feedbackToDelete);
            $feedback->delete();
            
            session()->flash('success', 'Feedback deleted successfully!');
            $this->dispatch('feedback-deleted');
        }
        
        $this->showDeleteModal = false;
        $this->feedbackToDelete = null;
    }

    public function viewFeedback($id)
    {
        $this->selectedFeedback = Feedback::with('user', 'approver')->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedFeedback = null;
    }

    public function bulkApprove()
    {
        if (empty($this->selectedItems)) {
            return;
        }

        foreach ($this->selectedItems as $id) {
            $feedback = Feedback::find($id);
            if ($feedback) {
                app(FeedbackService::class)->approveFeedback($feedback);
            }
        }

        session()->flash('success', count($this->selectedItems) . ' feedback items approved!');
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function bulkReject()
    {
        if (empty($this->selectedItems)) {
            return;
        }

        foreach ($this->selectedItems as $id) {
            $feedback = Feedback::find($id);
            if ($feedback) {
                app(FeedbackService::class)->rejectFeedback($feedback);
            }
        }

        session()->flash('success', count($this->selectedItems) . ' feedback items rejected!');
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function bulkDelete()
    {
        if (empty($this->selectedItems)) {
            return;
        }

        Feedback::whereIn('id', $this->selectedItems)->delete();

        session()->flash('success', count($this->selectedItems) . ' feedback items deleted!');
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function render()
    {
        $query = Feedback::with('user', 'approver');

        // Apply search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_mobile', 'like', '%' . $this->search . '%')
                  ->orWhere('feedback', 'like', '%' . $this->search . '%');
            });
        }

        // Apply filters
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->ratingFilter) {
            $query->byRating($this->ratingFilter);
        }

        if ($this->featuredFilter === 'featured') {
            $query->where('is_featured', true);
        } elseif ($this->featuredFilter === 'not-featured') {
            $query->where('is_featured', false);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortOrder);

        $feedback = $query->paginate($this->perPage);

        // Get stats
        $stats = [
            'total' => Feedback::count(),
            'pending' => Feedback::pending()->count(),
            'approved' => Feedback::approved()->count(),
            'rejected' => Feedback::rejected()->count(),
            'featured' => Feedback::featured()->count(),
        ];

        return view('livewire.admin.feedback-table', [
            'feedback' => $feedback,
            'stats' => $stats,
        ]);
    }
}
