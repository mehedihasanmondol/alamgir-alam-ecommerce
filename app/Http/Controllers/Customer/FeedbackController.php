<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display customer's feedback
     */
    public function index()
    {
        $feedbacks = Feedback::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('customer.feedback.index', compact('feedbacks'));
    }

    /**
     * Display feedback details
     */
    public function show(Feedback $feedback)
    {
        // Ensure feedback belongs to customer
        if ($feedback->user_id !== auth()->id()) {
            abort(403);
        }

        return view('customer.feedback.show', compact('feedback'));
    }

    /**
     * Update feedback
     */
    public function update(Request $request, Feedback $feedback)
    {
        // Ensure feedback belongs to customer
        if ($feedback->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'feedback' => 'required|string|max:2000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $feedback->update($validated);

        return redirect()->route('customer.feedback.index')
            ->with('success', 'Feedback updated successfully');
    }

    /**
     * Delete feedback
     */
    public function destroy(Feedback $feedback)
    {
        // Ensure feedback belongs to customer
        if ($feedback->user_id !== auth()->id()) {
            abort(403);
        }

        $feedback->delete();

        return redirect()->route('customer.feedback.index')
            ->with('success', 'Feedback deleted successfully');
    }
}
