<?php

namespace App\Modules\Ecommerce\Product\Repositories;

use App\Modules\Ecommerce\Product\Models\ProductAnswer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * ModuleName: Product Answer Repository
 * Purpose: Handle database queries for product answers
 * 
 * Key Methods:
 * - getByQuestion(): Get answers for a question
 * - create(): Create new answer
 * - update(): Update answer
 * - delete(): Soft delete answer
 * - approve(): Approve answer
 * - markAsBest(): Mark as best answer
 * 
 * Dependencies:
 * - ProductAnswer Model
 * 
 * @category Ecommerce
 * @package  Product
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ProductAnswerRepository
{
    /**
     * Get answers for a specific question
     */
    public function getByQuestion(int $questionId, int $perPage = 10): LengthAwarePaginator
    {
        return ProductAnswer::with(['user'])
            ->where('question_id', $questionId)
            ->approved()
            ->orderBy('is_best_answer', 'desc')
            ->recent()
            ->paginate($perPage);
    }

    /**
     * Get all answers with pagination
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return ProductAnswer::with(['question.product', 'user'])
            ->recent()
            ->paginate($perPage);
    }

    /**
     * Get pending answers
     */
    public function getPending(int $perPage = 10): LengthAwarePaginator
    {
        return ProductAnswer::with(['question.product', 'user'])
            ->pending()
            ->recent()
            ->paginate($perPage);
    }

    /**
     * Find answer by ID
     */
    public function find(int $id): ?ProductAnswer
    {
        return ProductAnswer::with(['question', 'user'])->find($id);
    }

    /**
     * Create new answer
     */
    public function create(array $data): ProductAnswer
    {
        return ProductAnswer::create($data);
    }

    /**
     * Update answer
     */
    public function update(int $id, array $data): bool
    {
        $answer = ProductAnswer::findOrFail($id);
        return $answer->update($data);
    }

    /**
     * Delete answer (soft delete)
     */
    public function delete(int $id): bool
    {
        $answer = ProductAnswer::findOrFail($id);
        return $answer->delete();
    }

    /**
     * Approve answer
     */
    public function approve(int $id): bool
    {
        return $this->update($id, ['status' => 'approved']);
    }

    /**
     * Reject answer
     */
    public function reject(int $id): bool
    {
        return $this->update($id, ['status' => 'rejected']);
    }

    /**
     * Mark answer as best answer
     */
    public function markAsBest(int $id): bool
    {
        $answer = ProductAnswer::findOrFail($id);
        $answer->markAsBestAnswer();
        return true;
    }

    /**
     * Get most helpful answers
     */
    public function getMostHelpful(int $questionId, int $limit = 5): Collection
    {
        return ProductAnswer::where('question_id', $questionId)
            ->approved()
            ->mostHelpful()
            ->limit($limit)
            ->get();
    }

    /**
     * Increment helpful count
     */
    public function incrementHelpful(int $id): void
    {
        $answer = ProductAnswer::findOrFail($id);
        $answer->incrementHelpful();
    }

    /**
     * Increment not helpful count
     */
    public function incrementNotHelpful(int $id): void
    {
        $answer = ProductAnswer::findOrFail($id);
        $answer->incrementNotHelpful();
    }

    /**
     * Check if user has purchased product
     */
    public function checkVerifiedPurchase(int $userId, int $productId): bool
    {
        // This would check if user has purchased the product
        // Implementation depends on your order system
        return \App\Modules\Ecommerce\Order\Models\Order::where('user_id', $userId)
            ->whereHas('items', function ($query) use ($productId) {
                $query->whereHas('variant', function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                });
            })
            ->where('status', 'completed')
            ->exists();
    }
}
