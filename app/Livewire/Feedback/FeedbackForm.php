<?php

namespace App\Livewire\Feedback;

use App\Services\FeedbackService;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Feedback Form Livewire Component
 * 
 * Handles feedback submission with auto-user registration
 * and image uploads with webp compression
 * 
 * @category Livewire
 * @package  Feedback
 * @author   Windsurf AI
 * @created  2025-11-25
 */
class FeedbackForm extends Component
{
    use WithFileUploads;

    public $customer_name = '';
    public $customer_email = '';
    public $customer_mobile = '';
    public $customer_address = '';
    public $rating = 0;
    public $title = '';
    public $feedback = '';
    public $images = [];
    public $errorMessage = '';
    public $successMessage = '';
    public $isSubmitting = false;
    public $ratingEnabled = true;
    public $showImages = true;
    public $emailRequired = false;

    protected function rules()
    {
        $emailRequired = \App\Models\SiteSetting::get('feedback_email_required', '0') === '1' ? 'required' : 'nullable';
        $ratingEnabled = \App\Models\SiteSetting::get('feedback_rating_enabled', '1') === '1';
        $showImages = \App\Models\SiteSetting::get('feedback_show_images', '1') === '1';
        
        return [
            'customer_name' => 'required|string|max:255',
            'customer_email' => $emailRequired . '|email|max:255',
            'customer_mobile' => 'required|string|max:20',
            'customer_address' => 'nullable|string|max:500',
            'rating' => $ratingEnabled ? 'required|integer|min:1|max:5' : 'nullable|integer|min:0|max:5',
            'title' => 'nullable|string|max:255',
            'feedback' => 'required|string|min:10|max:2000',
            'images.*' => 'nullable|image|max:5120', // 5MB
        ];
    }

    protected function messages()
    {
        return [
            'customer_name.required' => 'Please enter your name',
            'customer_email.required' => 'Please enter your email address',
            'customer_email.email' => 'Please enter a valid email address',
            'customer_mobile.required' => 'Please enter your mobile number',
            'rating.required' => 'Please select a rating',
            'rating.min' => 'Please select at least 1 star',
            'feedback.required' => 'Please write your feedback',
            'feedback.min' => 'Feedback must be at least 10 characters',
            'images.*.max' => 'Each image must not exceed 5MB',
        ];
    }

    public function mount()
    {
        // Load settings
        $this->ratingEnabled = \App\Models\SiteSetting::get('feedback_rating_enabled', '1') === '1';
        $this->showImages = \App\Models\SiteSetting::get('feedback_show_images', '1') === '1';
        $this->emailRequired = \App\Models\SiteSetting::get('feedback_email_required', '0') === '1';
        
        // Pre-fill if user is logged in
        if (auth()->check()) {
            $user = auth()->user();
            $this->customer_name = $user->name;
            $this->customer_email = $user->email ?? '';
            $this->customer_mobile = $user->mobile ?? '';
            $this->customer_address = $user->address ?? '';
        }
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
        $this->validateOnly('rating');
    }

    public function removeImage($index)
    {
        array_splice($this->images, $index, 1);
    }

    public function submit()
    {
        // Clear previous messages
        $this->errorMessage = '';
        $this->successMessage = '';
        $this->isSubmitting = true;

        $this->validate();

        try {
            $data = [
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_mobile' => $this->customer_mobile,
                'customer_address' => $this->customer_address,
                'rating' => $this->rating,
                'title' => $this->title,
                'feedback' => $this->feedback,
                'images' => $this->images,
            ];

            $result = app(FeedbackService::class)->createFeedback($data);

            // Reset form
            $this->reset([
                'rating', 
                'title', 
                'feedback', 
                'images',
                'customer_address'
            ]);

            // If not logged in, user info will be pre-filled after auto-login
            if (!auth()->check() && $result['was_created']) {
                $this->successMessage = 'Thank you! Your account has been created and your feedback has been submitted for review.';
            } else {
                $this->successMessage = 'Thank you for your feedback! It will be reviewed shortly.';
            }

            $this->dispatch('feedback-submitted');
            
            // Scroll to top
            $this->dispatch('scroll-to-top');
        } catch (\Exception $e) {
            $this->errorMessage = 'An error occurred: ' . $e->getMessage();
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function render()
    {
        return view('livewire.feedback.feedback-form');
    }
}
