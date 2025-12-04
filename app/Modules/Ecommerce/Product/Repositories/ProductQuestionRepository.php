<?php

namespace App\Modules\Ecommerce\Product\Repositories;

use App\Modules\Ecommerce\Product\Models\ProductQuestion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * ModuleName: Product Question Repository
 * Purpose: Handle database queries for product questions
 * 
 * Key Methods:
 * - getByProduct(): Get questions for a product
 * - create(): Create new question
 * - update(): Update question
 * - delete(): Soft delete question
 * - search(): Search questions
 * - approve(): Approve question
 * - reject(): Reject question
 * 
 * Dependencies:
 * - ProductQuestion Model
 * 
 * @category Ecommerce
 * @package  Product
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ProductQuestionRepository
{
    /**
     * Get questions for a specific product with pagination
     */
    public function getByProduct(int $productId, int $perPage = 10): LengthAwarePaginator
    {
        return ProductQuestion::with(['user', 'approvedAnswers.user'])
            ->where('product_id', $productId)
            ->approved()
            ->recent()
            ->paginate($perPage);
    }

    /**
     * Get questions for a specific product with limit and offset
     */
    public function getByProductWithLimit(int $productId, int $limit = 10, int $offset = 0, string $sortBy = 'recent', string $search = ''): SupportCollection
    {
        $query = ProductQuestion::with(['user', 'approvedAnswers.user'])
            ->where('product_id', $productId)
            ->approved();

        // Apply search filter
        if (!empty($search)) {
            $query->where('question', 'like', "%{$search}%");
        }

        // Apply sorting
        switch ($sortBy) {
            case 'helpful':
                $query->mostHelpful();
                break;
            case 'most_answers':
                $query->withCount('approvedAnswers')
                      ->orderBy('approved_answers_count', 'desc');
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
     * Get all questions with pagination
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return ProductQuestion::with(['product', 'user', 'answers'])
            ->recent()
            ->paginate($perPage);
    }

    /**
     * Get pending questions
     */
    public function getPending(int $perPage = 10): LengthAwarePaginator
    {
        return ProductQuestion::with(['product', 'user'])
            ->pending()
            ->recent()
            ->paginate($perPage);
    }

    /**
     * Find question by ID
     */
    public function find(int $id): ?ProductQuestion
    {
        return ProductQuestion::with(['product', 'user', 'answers.user'])->find($id);
    }

    /**
     * Create new question
     */
    public function create(array $data): ProductQuestion
    {
        return ProductQuestion::create($data);
    }

    /**
     * Update question
     */
    public function update(int $id, array $data): bool
    {
        $question = ProductQuestion::findOrFail($id);
        return $question->update($data);
    }

    /**
     * Delete question (soft delete)
     */
    public function delete(int $id): bool
    {
        $question = ProductQuestion::findOrFail($id);
        return $question->delete();
    }

    /**
     * Approve question
     */
    public function approve(int $id): bool
    {
        return $this->update($id, ['status' => 'approved']);
    }

    /**
     * Reject question
     */
    public function reject(int $id): bool
    {
        return $this->update($id, ['status' => 'rejected']);
    }

    /**
     * Search questions
     */
    public function search(string $query, int $perPage = 10): LengthAwarePaginator
    {
        return ProductQuestion::with(['product', 'user'])
            ->where('question', 'like', "%{$query}%")
            ->recent()
            ->paginate($perPage);
    }

    /**
     * Get most helpful questions
     */
    public function getMostHelpful(int $productId, int $limit = 5): Collection
    {
        return ProductQuestion::where('product_id', $productId)
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
        $question = ProductQuestion::findOrFail($id);
        $question->incrementHelpful();
    }

    /**
     * Increment not helpful count
     */
    public function incrementNotHelpful(int $id): void
    {
        $question = ProductQuestion::findOrFail($id);
        $question->incrementNotHelpful();
    }

    /**
     * Get question count for product
     */
    public function getCountByProduct(int $productId, string $search = ''): int
    {
        $query = ProductQuestion::where('product_id', $productId)
            ->approved();

        // Apply search filter
        if (!empty($search)) {
            $query->where('question', 'like', "%{$search}%");
        }

        return $query->count();
    }
}
