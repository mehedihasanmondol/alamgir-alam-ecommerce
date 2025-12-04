<?php

namespace App\Livewire\Product;

use App\Modules\Ecommerce\Product\Services\ProductQuestionService;
use Livewire\Component;

/**
 * ModuleName: Ask Question Livewire Component
 * Purpose: Modal component for submitting product questions
 * 
 * Key Methods:
 * - submitQuestion(): Submit new question
 * - openModal(): Open question modal
 * - closeModal(): Close question modal
 * 
 * Dependencies:
 * - ProductQuestionService
 * 
 * @category Livewire
 * @package  Product
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class AskQuestion extends Component
{
    public $productId;
    public $question = '';
    public $userName = '';
    public $userEmail = '';
    public $showModal = false;

    protected $listeners = ['openQuestionModal' => 'openModal'];

    public function mount($productId)
    {
        $this->productId = $productId;
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->question = '';
        if (!auth()->check()) {
            $this->userName = '';
            $this->userEmail = '';
        }
        $this->resetValidation();
    }

    public function submitQuestion()
    {
        $rules = [
            'question' => 'required|string|min:10|max:500',
        ];

        if (!auth()->check()) {
            $rules['userName'] = 'required|string|max:255';
            $rules['userEmail'] = 'required|email|max:255';
        }

        $this->validate($rules, [
            'question.required' => 'Please enter your question.',
            'question.min' => 'Question must be at least 10 characters.',
            'question.max' => 'Question cannot exceed 500 characters.',
            'userName.required' => 'Please provide your name.',
            'userEmail.required' => 'Please provide your email.',
            'userEmail.email' => 'Please provide a valid email address.',
        ]);

        try {
            $data = [
                'product_id' => $this->productId,
                'question' => $this->question,
            ];

            if (!auth()->check()) {
                $data['user_name'] = $this->userName;
                $data['user_email'] = $this->userEmail;
            }

            app(ProductQuestionService::class)->createQuestion($data);

            $this->closeModal();
            
            // Dispatch event to refresh question list
            $this->dispatch('question-submitted');
            
            // Show success notification
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Your question has been submitted successfully and is pending approval. Thank you!'
            ]);
            
        } catch (\Exception $e) {
            // Show error in modal
            $this->addError('submit', 'Failed to submit question: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.product.ask-question');
    }
}
