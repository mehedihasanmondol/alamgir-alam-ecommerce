<?php

namespace App\Modules\Ecommerce\Product\Services;

use App\Modules\Ecommerce\Product\Models\ProductQuestion;
use App\Modules\Ecommerce\Product\Repositories\ProductQuestionRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * ModuleName: Product Question Service
 * Purpose: Handle business logic for product questions
 * 
 * Key Methods:
 * - createQuestion(): Create new question
 * - approveQuestion(): Approve question
 * - rejectQuestion(): Reject question
 * - voteHelpful(): Vote question as helpful
 * - checkRateLimit(): Check if user can post question
 * 
 * Dependencies:
 * - ProductQuestionRepository
 * 
 * @category Ecommerce
 * @package  Product
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ProductQuestionService
{
    public function __construct(
        protected ProductQuestionRepository $questionRepository
    ) {}

    /**
     * Create new question
     */
    public function createQuestion(array $data): ProductQuestion
    {
        // Check rate limit
        if (Auth::check() && !$this->checkRateLimit(Auth::id())) {
            throw new \Exception('You have reached the maximum number of questions per day.');
        }

        // Check for spam
        if ($this->isSpam($data['question'])) {
            throw new \Exception('Your question appears to be spam.');
        }

        // Auto-approve for authenticated users (optional)
        $data['status'] = Auth::check() ? 'approved' : 'pending';
        $data['user_id'] = Auth::id();

        if (!Auth::check()) {
            $data['user_name'] = $data['user_name'] ?? 'Guest';
            $data['user_email'] = $data['user_email'] ?? null;
        }

        $question = $this->questionRepository->create($data);

        // Update rate limit cache
        if (Auth::check()) {
            $this->updateRateLimit(Auth::id());
        }

        return $question;
    }

    /**
     * Approve question
     */
    public function approveQuestion(int $id): bool
    {
        return $this->questionRepository->approve($id);
    }

    /**
     * Reject question
     */
    public function rejectQuestion(int $id): bool
    {
        return $this->questionRepository->reject($id);
    }

    /**
     * Delete question
     */
    public function deleteQuestion(int $id): bool
    {
        return $this->questionRepository->delete($id);
    }

    /**
     * Vote question as helpful
     */
    public function voteHelpful(int $id, bool $isHelpful = true): void
    {
        if ($isHelpful) {
            $this->questionRepository->incrementHelpful($id);
        } else {
            $this->questionRepository->incrementNotHelpful($id);
        }
    }

    /**
     * Check rate limit (max 5 questions per day)
     */
    protected function checkRateLimit(int $userId): bool
    {
        $cacheKey = "question_rate_limit_{$userId}";
        $count = Cache::get($cacheKey, 0);
        
        return $count < 5;
    }

    /**
     * Update rate limit cache
     */
    protected function updateRateLimit(int $userId): void
    {
        $cacheKey = "question_rate_limit_{$userId}";
        $count = Cache::get($cacheKey, 0);
        
        Cache::put($cacheKey, $count + 1, now()->endOfDay());
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

    /**
     * Get questions for product with limit and offset
     */
    public function getQuestionsByProduct(int $productId, int $limit = 10, int $offset = 0, string $sortBy = 'recent', string $search = '')
    {
        return $this->questionRepository->getByProductWithLimit($productId, $limit, $offset, $sortBy, $search);
    }

    /**
     * Get question count for product
     */
    public function getQuestionCountByProduct(int $productId, string $search = ''): int
    {
        return $this->questionRepository->getCountByProduct($productId, $search);
    }

    /**
     * Get pending questions
     */
    public function getPendingQuestions(int $perPage = 10)
    {
        return $this->questionRepository->getPending($perPage);
    }

    /**
     * Search questions
     */
    public function searchQuestions(string $query, int $perPage = 10)
    {
        return $this->questionRepository->search($query, $perPage);
    }
}
