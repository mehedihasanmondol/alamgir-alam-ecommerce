<?php

namespace App\Modules\Ecommerce\Product\Repositories;

use App\Modules\Ecommerce\Product\Models\ProductReview;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * ModuleName: Product Review Repository
 * Purpose: Handle database queries for product reviews
 * 
 * Key Methods:
 * - getByProduct(): Get reviews for a product
 * - create(): Create new review
 * - update(): Update review
 * - delete(): Soft delete review
 * - approve(): Approve review
 * - reject(): Reject review
 * 
 * Dependencies:
 * - ProductReview Model
 * 
 * @category Ecommerce
 * @package  Product
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ProductReviewRepository
{
    /**
     * Get reviews for a specific product with limit and offset
     */
    public function getByProductWithLimit(
        int $productId, 
        int $limit = 10, 
        int $offset = 0, 
        string $sortBy = 'recent',
        ?int $rating = null
    ): SupportCollection {
        $query = ProductReview::with(['user', 'order'])
            ->where('product_id', $productId)
            ->approved();

        // Filter by rating if specified
        if ($rating) {
            $query->byRating($rating);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'helpful':
                $query->mostHelpful();
                break;
            case 'highest':
                $query->highestRated();
                break;
            case 'lowest':
                $query->lowestRated();
                break;
            default:
                $query->recent();
                break;
        }

        return $query->skip($offset)
                    ->take($limit)
                    ->get();
    }

    /**
     * Get review count for product
     */
    public function getCountByProduct(int $productId, ?int $rating = null): int
    {
        $query = ProductReview::where('product_id', $productId)
            ->approved();

        if ($rating) {
            $query->byRating($rating);
        }

        return $query->count();
    }

    /**
     * Get rating distribution for product
     */
    public function getRatingDistribution(int $productId): array
    {
        $reviews = ProductReview::where('product_id', $productId)
            ->approved()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = $reviews[$i] ?? 0;
        }

        return $distribution;
    }

    /**
     * Get average rating for product
     */
    public function getAverageRating(int $productId): float
    {
        return (float) ProductReview::where('product_id', $productId)
            ->approved()
            ->avg('rating') ?? 0;
    }

    /**
     * Get all reviews with pagination (for admin)
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return ProductReview::with(['product', 'user'])
            ->recent()
            ->paginate($perPage);
    }

    /**
     * Get pending reviews
     */
    public function getPending(int $perPage = 10): LengthAwarePaginator
    {
        return ProductReview::with(['product', 'user'])
            ->pending()
            ->recent()
            ->paginate($perPage);
    }

    /**
     * Find review by ID
     */
    public function find(int $id): ?ProductReview
    {
        return ProductReview::with(['product', 'user', 'order'])->find($id);
    }

    /**
     * Create new review
     */
    public function create(array $data): ProductReview
    {
        return ProductReview::create($data);
    }

    /**
     * Update review
     */
    public function update(int $id, array $data): bool
    {
        $review = ProductReview::findOrFail($id);
        return $review->update($data);
    }

    /**
     * Delete review (soft delete)
     */
    public function delete(int $id): bool
    {
        $review = ProductReview::findOrFail($id);
        return $review->delete();
    }

    /**
     * Approve review
     */
    public function approve(int $id, int $approvedBy): bool
    {
        return $this->update($id, [
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approvedBy,
        ]);
    }

    /**
     * Reject review
     */
    public function reject(int $id): bool
    {
        return $this->update($id, ['status' => 'rejected']);
    }

    /**
     * Increment helpful count
     */
    public function incrementHelpful(int $id): void
    {
        $review = ProductReview::findOrFail($id);
        $review->incrementHelpful();
    }

    /**
     * Increment not helpful count
     */
    public function incrementNotHelpful(int $id): void
    {
        $review = ProductReview::findOrFail($id);
        $review->incrementNotHelpful();
    }

    /**
     * Check if user has purchased product
     */
    public function hasUserPurchasedProduct(int $userId, int $productId): bool
    {
        return \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.user_id', $userId)
            ->where('order_items.product_id', $productId)
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->exists();
    }

    /**
     * Check if user has already reviewed product
     */
    public function hasUserReviewedProduct(int $userId, int $productId): bool
    {
        return ProductReview::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }
}
