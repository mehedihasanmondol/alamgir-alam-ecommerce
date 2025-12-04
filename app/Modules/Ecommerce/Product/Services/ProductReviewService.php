<?php

namespace App\Modules\Ecommerce\Product\Services;

use App\Modules\Ecommerce\Product\Models\ProductReview;
use App\Modules\Ecommerce\Product\Repositories\ProductReviewRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * ModuleName: Product Review Service
 * Purpose: Handle business logic for product reviews
 * 
 * Key Methods:
 * - createReview(): Create new review with validation
 * - approveReview(): Approve review
 * - rejectReview(): Reject review
 * - voteHelpful(): Vote review as helpful
 * - uploadReviewImages(): Handle image uploads
 * 
 * Dependencies:
 * - ProductReviewRepository
 * 
 * @category Ecommerce
 * @package  Product
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ProductReviewService
{
    public function __construct(
        protected ProductReviewRepository $reviewRepository
    ) {}

    /**
     * Create new review
     */
    public function createReview(array $data): ProductReview
    {
        // Check if user already reviewed this product
        if (Auth::check() && $this->reviewRepository->hasUserReviewedProduct(Auth::id(), $data['product_id'])) {
            throw new \Exception('You have already reviewed this product.');
        }

        // Check for spam
        if ($this->isSpam($data['review'])) {
            throw new \Exception('Your review appears to be spam.');
        }

        // Check if user purchased the product
        $isVerifiedPurchase = false;
        if (Auth::check()) {
            $isVerifiedPurchase = $this->reviewRepository->hasUserPurchasedProduct(Auth::id(), $data['product_id']);
        }

        // Auto-approve for authenticated users with verified purchase
        $data['status'] = (Auth::check() && $isVerifiedPurchase) ? 'approved' : 'pending';
        $data['user_id'] = Auth::id();
        $data['is_verified_purchase'] = $isVerifiedPurchase;

        if (!Auth::check()) {
            $data['reviewer_name'] = $data['reviewer_name'] ?? 'Guest';
            $data['reviewer_email'] = $data['reviewer_email'] ?? null;
        }

        // Handle image uploads if present
        if (isset($data['images']) && is_array($data['images'])) {
            $data['images'] = $this->uploadReviewImages($data['images']);
        }

        $review = $this->reviewRepository->create($data);

        // Update product rating cache
        $this->updateProductRating($data['product_id']);

        return $review;
    }

    /**
     * Upload review images with WebP compression
     */
    public function uploadReviewImages(array $images): array
    {
        $uploadedPaths = [];

        foreach ($images as $image) {
            if ($image && $image->isValid()) {
                // Get original image
                $originalImage = imagecreatefromstring(file_get_contents($image->getRealPath()));
                
                if ($originalImage !== false) {
                    // Generate unique filename
                    $filename = 'review-' . uniqid() . '.webp';
                    $path = 'reviews/' . $filename;
                    $fullPath = storage_path('app/public/' . $path);
                    
                    // Ensure directory exists
                    $directory = dirname($fullPath);
                    if (!file_exists($directory)) {
                        mkdir($directory, 0755, true);
                    }
                    
                    // Save as WebP with high quality (90)
                    imagewebp($originalImage, $fullPath, 90);
                    imagedestroy($originalImage);
                    
                    $uploadedPaths[] = $path;
                } else {
                    // Fallback to regular upload if image processing fails
                    $path = $image->store('reviews', 'public');
                    $uploadedPaths[] = $path;
                }
            }
        }

        return $uploadedPaths;
    }

    /**
     * Approve review
     */
    public function approveReview(int $id): bool
    {
        $result = $this->reviewRepository->approve($id, Auth::id());
        
        if ($result) {
            $review = $this->reviewRepository->find($id);
            $this->updateProductRating($review->product_id);
        }

        return $result;
    }

    /**
     * Reject review
     */
    public function rejectReview(int $id): bool
    {
        return $this->reviewRepository->reject($id);
    }

    /**
     * Delete review
     */
    public function deleteReview(int $id): bool
    {
        $review = $this->reviewRepository->find($id);
        
        // Delete images
        if ($review->images) {
            foreach ($review->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $result = $this->reviewRepository->delete($id);
        
        if ($result) {
            $this->updateProductRating($review->product_id);
        }

        return $result;
    }

    /**
     * Vote review as helpful
     */
    public function voteHelpful(int $id, bool $isHelpful = true): void
    {
        if ($isHelpful) {
            $this->reviewRepository->incrementHelpful($id);
        } else {
            $this->reviewRepository->incrementNotHelpful($id);
        }
    }

    /**
     * Get reviews for product
     */
    public function getReviewsByProduct(
        int $productId, 
        int $limit = 10, 
        int $offset = 0, 
        string $sortBy = 'recent',
        ?int $rating = null
    ) {
        return $this->reviewRepository->getByProductWithLimit($productId, $limit, $offset, $sortBy, $rating);
    }

    /**
     * Get review count for product
     */
    public function getReviewCountByProduct(int $productId, ?int $rating = null): int
    {
        return $this->reviewRepository->getCountByProduct($productId, $rating);
    }

    /**
     * Get rating distribution
     */
    public function getRatingDistribution(int $productId): array
    {
        return $this->reviewRepository->getRatingDistribution($productId);
    }

    /**
     * Get average rating
     */
    public function getAverageRating(int $productId): float
    {
        return $this->reviewRepository->getAverageRating($productId);
    }

    /**
     * Get pending reviews
     */
    public function getPendingReviews(int $perPage = 10)
    {
        return $this->reviewRepository->getPending($perPage);
    }

    /**
     * Update product rating cache
     */
    protected function updateProductRating(int $productId): void
    {
        $averageRating = $this->getAverageRating($productId);
        $reviewCount = $this->getReviewCountByProduct($productId);

        \DB::table('products')
            ->where('id', $productId)
            ->update([
                'average_rating' => $averageRating,
                'review_count' => $reviewCount,
            ]);
    }

    /**
     * Simple spam detection
     */
    protected function isSpam(string $text): bool
    {
        $spamKeywords = ['viagra', 'cialis', 'casino', 'lottery', 'click here', 'buy now'];
        
        foreach ($spamKeywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                return true;
            }
        }

        // Check for excessive links
        if (substr_count(strtolower($text), 'http') > 2) {
            return true;
        }

        return false;
    }
}
