<?php

namespace App\Modules\Ecommerce\Product\Services;

use App\Modules\Ecommerce\Product\Models\ProductAnswer;
use App\Modules\Ecommerce\Product\Repositories\ProductAnswerRepository;
use Illuminate\Support\Facades\Auth;

/**
 * ModuleName: Product Answer Service
 * Purpose: Handle business logic for product answers
 * 
 * Key Methods:
 * - createAnswer(): Create new answer
 * - approveAnswer(): Approve answer
 * - markAsBestAnswer(): Mark answer as best
 * - voteHelpful(): Vote answer as helpful
 * 
 * Dependencies:
 * - ProductAnswerRepository
 * 
 * @category Ecommerce
 * @package  Product
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ProductAnswerService
{
    public function __construct(
        protected ProductAnswerRepository $answerRepository
    ) {}

    /**
     * Create new answer
     */
    public function createAnswer(array $data): ProductAnswer
    {
        // Check for spam
        if ($this->isSpam($data['answer'])) {
            throw new \Exception('Your answer appears to be spam.');
        }

        // Auto-approve for authenticated users (optional)
        $data['status'] = Auth::check() ? 'approved' : 'pending';
        $data['user_id'] = Auth::id();

        if (!Auth::check()) {
            $data['user_name'] = $data['user_name'] ?? 'Guest';
            $data['user_email'] = $data['user_email'] ?? null;
        }

        // Check if user has purchased the product
        if (Auth::check() && isset($data['product_id'])) {
            $data['is_verified_purchase'] = $this->answerRepository
                ->checkVerifiedPurchase(Auth::id(), $data['product_id']);
        }

        $answer = $this->answerRepository->create($data);

        return $answer;
    }

    /**
     * Approve answer
     */
    public function approveAnswer(int $id): bool
    {
        return $this->answerRepository->approve($id);
    }

    /**
     * Reject answer
     */
    public function rejectAnswer(int $id): bool
    {
        return $this->answerRepository->reject($id);
    }

    /**
     * Delete answer
     */
    public function deleteAnswer(int $id): bool
    {
        return $this->answerRepository->delete($id);
    }

    /**
     * Mark answer as best answer
     */
    public function markAsBestAnswer(int $id): bool
    {
        return $this->answerRepository->markAsBest($id);
    }

    /**
     * Vote answer as helpful
     */
    public function voteHelpful(int $id, bool $isHelpful = true): void
    {
        if ($isHelpful) {
            $this->answerRepository->incrementHelpful($id);
        } else {
            $this->answerRepository->incrementNotHelpful($id);
        }
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
     * Get answers for question
     */
    public function getAnswersByQuestion(int $questionId, int $perPage = 10)
    {
        return $this->answerRepository->getByQuestion($questionId, $perPage);
    }

    /**
     * Get pending answers
     */
    public function getPendingAnswers(int $perPage = 10)
    {
        return $this->answerRepository->getPending($perPage);
    }
}
