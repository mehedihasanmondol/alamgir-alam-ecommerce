<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Ecommerce\Product\Services\ProductQuestionService;
use App\Modules\Ecommerce\Product\Services\ProductAnswerService;
use Illuminate\Http\Request;

/**
 * ModuleName: Admin Product Question Controller
 * Purpose: Handle admin moderation of product questions
 * 
 * Key Methods:
 * - index(): List all questions
 * - show(): Show question details
 * - approve(): Approve question
 * - reject(): Reject question
 * - destroy(): Delete question
 * 
 * Dependencies:
 * - ProductQuestionService
 * - ProductAnswerService
 * 
 * @category Admin
 * @package  Controllers
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ProductQuestionController extends Controller
{
    public function __construct(
        protected ProductQuestionService $questionService,
        protected ProductAnswerService $answerService
    ) {}

    /**
     * Display a listing of questions
     * Data is handled by Livewire component
     */
    public function index()
    {
        return view('admin.product-questions.index');
    }

    /**
     * Display the specified question
     */
    public function show(int $id)
    {
        $question = \App\Modules\Ecommerce\Product\Models\ProductQuestion::with(['product', 'user', 'answers.user'])
            ->findOrFail($id);
        
        return view('admin.product-questions.show', compact('question'));
    }

    /**
     * Approve question
     */
    public function approve(int $id)
    {
        $this->questionService->approveQuestion($id);
        
        return redirect()->back()->with('success', 'Question approved successfully.');
    }

    /**
     * Reject question
     */
    public function reject(int $id)
    {
        $this->questionService->rejectQuestion($id);
        
        return redirect()->back()->with('success', 'Question rejected successfully.');
    }

    /**
     * Remove the specified question
     */
    public function destroy(int $id)
    {
        $this->questionService->deleteQuestion($id);
        
        return redirect()->back()->with('success', 'Question deleted successfully.');
    }

    /**
     * Approve answer
     */
    public function approveAnswer(int $id)
    {
        $this->answerService->approveAnswer($id);
        
        return redirect()->back()->with('success', 'Answer approved successfully.');
    }

    /**
     * Reject answer
     */
    public function rejectAnswer(int $id)
    {
        $this->answerService->rejectAnswer($id);
        
        return redirect()->back()->with('success', 'Answer rejected successfully.');
    }

    /**
     * Mark answer as best
     */
    public function markBestAnswer(int $id)
    {
        $this->answerService->markAsBestAnswer($id);
        
        return redirect()->back()->with('success', 'Answer marked as best answer.');
    }
}
