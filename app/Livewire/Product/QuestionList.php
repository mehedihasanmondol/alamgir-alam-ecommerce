<?php

namespace App\Livewire\Product;

use App\Modules\Ecommerce\Product\Services\ProductQuestionService;
use App\Modules\Ecommerce\Product\Services\ProductAnswerService;
use Livewire\Component;

/**
 * ModuleName: Question List Livewire Component
 * Purpose: Display and manage product questions with real-time interactions
 * 
 * Key Methods:
 * - render(): Display questions with pagination
 * - voteHelpful(): Vote question/answer as helpful
 * - submitAnswer(): Submit new answer
 * 
 * Dependencies:
 * - ProductQuestionService
 * - ProductAnswerService
 * 
 * @category Livewire
 * @package  Product
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class QuestionList extends Component
{
    public $productId;
    public $search = '';
    public $sortBy = 'recent'; // recent, helpful, most_answers
    public $showAnswerForm = [];
    public $answerText = [];
    public $perLoad = 10;
    public $offset = 0;
    public $totalCount = 0;

    protected $queryString = ['search', 'sortBy'];
    protected $listeners = ['question-submitted' => '$refresh'];

    public function mount($productId)
    {
        $this->productId = $productId;
    }

    public function updatingSearch()
    {
        $this->offset = 0;
    }

    public function updatingSortBy()
    {
        $this->offset = 0;
    }

    public function loadMore()
    {
        $this->offset += $this->perLoad;
    }

    public function voteHelpful($type, $id, $isHelpful = true)
    {
        if ($type === 'question') {
            app(ProductQuestionService::class)->voteHelpful($id, $isHelpful);
        } else {
            app(ProductAnswerService::class)->voteHelpful($id, $isHelpful);
        }

        $this->dispatch('vote-updated');
    }

    public function toggleAnswerForm($questionId)
    {
        $this->showAnswerForm[$questionId] = !($this->showAnswerForm[$questionId] ?? false);
    }

    public function submitAnswer($questionId)
    {
        $this->validate([
            "answerText.{$questionId}" => 'required|min:10|max:1000',
        ], [
            "answerText.{$questionId}.required" => 'Please enter your answer.',
            "answerText.{$questionId}.min" => 'Answer must be at least 10 characters.',
            "answerText.{$questionId}.max" => 'Answer cannot exceed 1000 characters.',
        ]);

        try {
            app(ProductAnswerService::class)->createAnswer([
                'question_id' => $questionId,
                'answer' => $this->answerText[$questionId],
                'product_id' => $this->productId,
            ]);

            $this->answerText[$questionId] = '';
            $this->showAnswerForm[$questionId] = false;
            
            session()->flash('success', 'Your answer has been submitted and is pending approval.');
            $this->dispatch('answer-submitted');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $questionService = app(ProductQuestionService::class);
        
        // Get total count
        $this->totalCount = $questionService->getQuestionCountByProduct($this->productId, $this->search);
        
        // Get ALL questions from start to current offset + perLoad (to keep previous loaded questions)
        $limit = $this->offset + $this->perLoad;
        $questions = $questionService->getQuestionsByProduct(
            $this->productId, 
            $limit,
            0, // Always start from 0 to get all previous questions
            $this->sortBy,
            $this->search
        );

        return view('livewire.product.question-list', [
            'questions' => $questions,
            'hasMore' => $limit < $this->totalCount,
            'loadedCount' => min($limit, $this->totalCount),
        ]);
    }
}
