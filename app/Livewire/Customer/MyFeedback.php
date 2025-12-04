<?php

namespace App\Livewire\Customer;

use App\Models\Feedback;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Customer Feedback Management Component
 * 
 * Allows customers to view their own feedback submissions
 * 
 * @category Livewire
 * @package  Customer
 * @author   Windsurf AI
 * @created  2025-11-26
 */
class MyFeedback extends Component
{
    use WithPagination;

    public function render()
    {
        $feedback = Feedback::where('user_id', auth()->id())
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.customer.my-feedback', [
            'feedback' => $feedback,
        ]);
    }
}
