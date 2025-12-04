<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ecommerce\Product\Services\ProductReviewService;
use App\Modules\Ecommerce\Product\Repositories\ProductReviewRepository;
use Illuminate\Http\Request;

/**
 * ModuleName: Admin Review Controller
 * Purpose: Manage product reviews in admin panel
 * 
 * Key Methods:
 * - index(): List all reviews
 * - pending(): List pending reviews
 * - show(): View review details
 * - approve(): Approve review
 * - reject(): Reject review
 * - destroy(): Delete review
 * 
 * Dependencies:
 * - ProductReviewService
 * - ProductReviewRepository
 * 
 * @category Admin
 * @package  Review
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ReviewController extends Controller
{
    public function __construct(
        protected ProductReviewService $reviewService,
        protected ProductReviewRepository $reviewRepository
    ) {}

    /**
     * Display all reviews
     */
    public function index(Request $request)
    {
        $reviews = $this->reviewRepository->getAll($request->get('per_page', 15));

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Display pending reviews
     */
    public function pending(Request $request)
    {
        $reviews = $this->reviewService->getPendingReviews($request->get('per_page', 15));

        return view('admin.reviews.pending', compact('reviews'));
    }

    /**
     * Show review details
     */
    public function show(int $id)
    {
        $review = $this->reviewRepository->find($id);

        if (!$review) {
            return redirect()->route('admin.reviews.index')
                ->with('error', 'Review not found.');
        }

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve review
     */
    public function approve(int $id)
    {
        try {
            $this->reviewService->approveReview($id);

            return redirect()->back()
                ->with('success', 'Review approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve review: ' . $e->getMessage());
        }
    }

    /**
     * Reject review
     */
    public function reject(int $id)
    {
        try {
            $this->reviewService->rejectReview($id);

            return redirect()->back()
                ->with('success', 'Review rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reject review: ' . $e->getMessage());
        }
    }

    /**
     * Delete review
     */
    public function destroy(int $id)
    {
        try {
            $this->reviewService->deleteReview($id);

            return redirect()->route('admin.reviews.index')
                ->with('success', 'Review deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete review: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:product_reviews,id',
        ]);

        try {
            foreach ($request->review_ids as $id) {
                $this->reviewService->approveReview($id);
            }

            return redirect()->back()
                ->with('success', count($request->review_ids) . ' reviews approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve reviews: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete reviews
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:product_reviews,id',
        ]);

        try {
            foreach ($request->review_ids as $id) {
                $this->reviewService->deleteReview($id);
            }

            return redirect()->back()
                ->with('success', count($request->review_ids) . ' reviews deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete reviews: ' . $e->getMessage());
        }
    }
}
