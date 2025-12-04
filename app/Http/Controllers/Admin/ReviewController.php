<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Ecommerce\Product\Services\ProductReviewService;
use App\Modules\Ecommerce\Product\Models\ProductReview;
use Illuminate\Http\Request;

/**
 * ModuleName: Admin Review Controller
 * Purpose: Handle admin moderation of product reviews
 * 
 * Key Methods:
 * - index(): List all reviews
 * - pending(): List pending reviews
 * - show(): Show review details
 * - approve(): Approve review
 * - reject(): Reject review
 * - destroy(): Delete review
 * - bulkApprove(): Bulk approve reviews
 * - bulkDelete(): Bulk delete reviews
 * 
 * Dependencies:
 * - ProductReviewService
 * 
 * @category Admin
 * @package  Controllers
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ReviewController extends Controller
{
    public function __construct(
        protected ProductReviewService $reviewService
    ) {}

    /**
     * Display a listing of reviews
     * Data is handled by Livewire component
     */
    public function index()
    {
        return view('admin.reviews.index');
    }

    /**
     * Display pending reviews
     */
    public function pending()
    {
        $reviews = $this->reviewService->getPendingReviews(15);
        return view('admin.reviews.pending', compact('reviews'));
    }

    /**
     * Display the specified review
     */
    public function show(int $id)
    {
        $review = ProductReview::with(['product', 'user', 'order', 'approver'])
            ->findOrFail($id);
        
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve review
     */
    public function approve(int $id)
    {
        try {
            $this->reviewService->approveReview($id);
            return redirect()->back()->with('success', 'Review approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve review: ' . $e->getMessage());
        }
    }

    /**
     * Reject review
     */
    public function reject(int $id)
    {
        try {
            $this->reviewService->rejectReview($id);
            return redirect()->back()->with('success', 'Review rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject review: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified review
     */
    public function destroy(int $id)
    {
        try {
            $this->reviewService->deleteReview($id);
            return redirect()->back()->with('success', 'Review deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete review: ' . $e->getMessage());
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

        $successCount = 0;
        foreach ($request->review_ids as $id) {
            try {
                $this->reviewService->approveReview($id);
                $successCount++;
            } catch (\Exception $e) {
                \Log::error("Failed to approve review {$id}: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', "{$successCount} reviews approved successfully.");
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

        $successCount = 0;
        foreach ($request->review_ids as $id) {
            try {
                $this->reviewService->deleteReview($id);
                $successCount++;
            } catch (\Exception $e) {
                \Log::error("Failed to delete review {$id}: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', "{$successCount} reviews deleted successfully.");
    }
}
