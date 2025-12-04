<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Services\FeedbackService;
use Illuminate\Http\Request;

/**
 * Frontend Feedback Controller
 * 
 * Handles public feedback submission and display
 * 
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   Windsurf AI
 * @created  2025-11-25
 */
class FeedbackController extends Controller
{
    protected $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

    /**
     * Show all feedback page
     */
    public function index()
    {
        // Check if feedback is enabled
        $feedbackEnabled = \App\Models\SiteSetting::get('feedback_enabled', '1') === '1';
        
        if (!$feedbackEnabled) {
            abort(404, 'Feedback feature is currently disabled.');
        }

        return view('frontend.feedback.index');
    }

    /**
     * Mark feedback as helpful
     */
    public function helpful(Feedback $feedback)
    {
        $this->feedbackService->markHelpful($feedback);

        return response()->json([
            'success' => true,
            'helpful_count' => $feedback->fresh()->helpful_count,
        ]);
    }

    /**
     * Mark feedback as not helpful
     */
    public function notHelpful(Feedback $feedback)
    {
        $this->feedbackService->markNotHelpful($feedback);

        return response()->json([
            'success' => true,
            'not_helpful_count' => $feedback->fresh()->not_helpful_count,
        ]);
    }
}
