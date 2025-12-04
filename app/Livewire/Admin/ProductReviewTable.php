<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use App\Modules\Ecommerce\Product\Models\ProductReview;
use App\Modules\Ecommerce\Product\Services\ProductReviewService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * ModuleName: Product Review Table Livewire Component
 * Purpose: Admin table for managing product reviews with filters and pagination
 * 
 * Key Methods:
 * - render(): Display reviews with filters
 * - approve(): Approve a review
 * - reject(): Reject a review
 * - deleteReview(): Delete a review
 * 
 * Dependencies:
 * - ProductReviewService
 * 
 * @category Livewire
 * @package  Admin
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ProductReviewTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $ratingFilter = '';
    public $verifiedFilter = '';
    public $perPage = 15;
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';

    public $showFilters = false;
    public $showDeleteModal = false;
    public $showViewModal = false;
    public $reviewToDelete = null;
    public $selectedReview = null;
    public $enableReviews = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'ratingFilter' => ['except' => ''],
        'verifiedFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->enableReviews = SiteSetting::get('enable_product_reviews', '1') === '1';
    }

    public function toggleEnableReviews()
    {
        $this->enableReviews = !$this->enableReviews;
        SiteSetting::set('enable_product_reviews', $this->enableReviews ? '1' : '0');
        
        session()->flash('success', 'Product reviews ' . ($this->enableReviews ? 'enabled' : 'disabled') . ' successfully!');
    }

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

    public function updatingVerifiedFilter()
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

    public function approve($reviewId, ProductReviewService $service)
    {
        try {
            $service->approveReview($reviewId);
            
            // Close modal if open
            if ($this->showViewModal) {
                $this->closeViewModal();
            }
            
            session()->flash('success', 'Review approved successfully!');
            
            // Refresh the component to show updated data
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve review: ' . $e->getMessage());
        }
    }

    public function reject($reviewId, ProductReviewService $service)
    {
        try {
            $service->rejectReview($reviewId);
            
            // Close modal if open
            if ($this->showViewModal) {
                $this->closeViewModal();
            }
            
            session()->flash('success', 'Review rejected successfully!');
            
            // Refresh the component to show updated data
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reject review: ' . $e->getMessage());
        }
    }

    public function viewReview($reviewId)
    {
        $this->selectedReview = ProductReview::with(['product', 'user', 'order', 'approver'])->find($reviewId);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedReview = null;
    }

    public function confirmDelete($reviewId)
    {
        $this->reviewToDelete = $reviewId;
        $this->showDeleteModal = true;
    }

    public function deleteReview(ProductReviewService $service)
    {
        if ($this->reviewToDelete) {
            try {
                $service->deleteReview($this->reviewToDelete);
                session()->flash('success', 'Review deleted successfully!');
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to delete review: ' . $e->getMessage());
            }
        }

        $this->showDeleteModal = false;
        $this->reviewToDelete = null;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'ratingFilter', 'verifiedFilter']);
        $this->resetPage();
    }

    public function render()
    {
        try {
            $query = ProductReview::with(['product', 'user', 'order']);

            // Search filter
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('review', 'like', '%' . $this->search . '%')
                      ->orWhere('title', 'like', '%' . $this->search . '%')
                      ->orWhere('reviewer_name', 'like', '%' . $this->search . '%')
                      ->orWhereHas('product', function($productQuery) {
                          $productQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            }

            // Status filter
            if ($this->statusFilter !== '') {
                $query->where('status', $this->statusFilter);
            }

            // Rating filter
            if ($this->ratingFilter !== '') {
                $query->where('rating', $this->ratingFilter);
            }

            // Verified purchase filter
            if ($this->verifiedFilter !== '') {
                $query->where('is_verified_purchase', $this->verifiedFilter === '1');
            }

            // Sorting
            $query->orderBy($this->sortBy, $this->sortOrder);

            $reviews = $query->paginate($this->perPage);

            // Get statistics
            $stats = [
                'total' => ProductReview::count(),
                'pending' => ProductReview::where('status', 'pending')->count(),
                'approved' => ProductReview::where('status', 'approved')->count(),
                'rejected' => ProductReview::where('status', 'rejected')->count(),
                'average_rating' => round(ProductReview::where('status', 'approved')->avg('rating') ?? 0, 1),
            ];

            return view('livewire.admin.product-review-table', [
                'reviews' => $reviews,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            \Log::error('ProductReviewTable render error: ' . $e->getMessage());
            return view('livewire.admin.product-review-table', [
                'reviews' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'stats' => [
                    'total' => 0,
                    'pending' => 0,
                    'approved' => 0,
                    'rejected' => 0,
                    'average_rating' => 0,
                ],
                'error' => $e->getMessage(),
            ]);
        }
    }
}
