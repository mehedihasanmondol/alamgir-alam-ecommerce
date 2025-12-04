<?php

namespace App\Livewire\Product;

use App\Modules\Ecommerce\Product\Services\ProductReviewService;
use Livewire\Component;

/**
 * ModuleName: Review List Livewire Component
 * Purpose: Display and manage product reviews with real-time interactions
 * 
 * Key Methods:
 * - render(): Display reviews with load more
 * - voteHelpful(): Vote review as helpful
 * - filterByRating(): Filter reviews by rating
 * 
 * Dependencies:
 * - ProductReviewService
 * 
 * @category Livewire
 * @package  Product
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ReviewList extends Component
{
    public $productId;
    public $sortBy = 'recent'; // recent, helpful, highest, lowest
    public $filterRating = null; // null or 1-5
    public $perLoad = 10;
    public $offset = 0;
    public $totalCount = 0;
    public $averageRating = 0;
    public $ratingDistribution = [];

    protected $queryString = ['sortBy', 'filterRating'];
    protected $listeners = ['review-submitted' => '$refresh'];

    public function mount($productId)
    {
        $this->productId = $productId;
    }

    public function updatingSortBy()
    {
        $this->offset = 0;
    }

    public function updatingFilterRating()
    {
        $this->offset = 0;
    }

    public function loadMore()
    {
        $this->offset += $this->perLoad;
    }

    public function filterByRating(?int $rating)
    {
        $this->filterRating = $rating;
        $this->offset = 0;
    }

    public function voteHelpful(int $id, bool $isHelpful = true)
    {
        app(ProductReviewService::class)->voteHelpful($id, $isHelpful);
        $this->dispatch('vote-updated');
    }

    public function render()
    {
        $reviewService = app(ProductReviewService::class);
        
        // Get total count
        $this->totalCount = $reviewService->getReviewCountByProduct($this->productId, $this->filterRating);
        
        // Get average rating and distribution
        $this->averageRating = $reviewService->getAverageRating($this->productId);
        $this->ratingDistribution = $reviewService->getRatingDistribution($this->productId);
        
        // Get reviews with limit and offset (cumulative for load more)
        $limit = $this->offset + $this->perLoad;
        $reviews = $reviewService->getReviewsByProduct(
            $this->productId, 
            $limit,
            0, // Always start from 0 to get all previous reviews
            $this->sortBy,
            $this->filterRating
        );

        return view('livewire.product.review-list', [
            'reviews' => $reviews,
            'hasMore' => $limit < $this->totalCount,
            'loadedCount' => min($limit, $this->totalCount),
        ]);
    }
}
