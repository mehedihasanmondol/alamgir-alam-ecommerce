<?php

namespace App\Livewire\Product;

use App\Modules\Ecommerce\Product\Services\ProductReviewService;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * ModuleName: Review Form Livewire Component
 * Purpose: Handle review submission with image uploads
 * 
 * @category Livewire
 * @package  Product
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ReviewForm extends Component
{
    use WithFileUploads;

    public $productId;
    public $rating = 0;
    public $title = '';
    public $review = '';
    public $images = [];
    public $reviewerName = '';
    public $reviewerEmail = '';
    public $errorMessage = '';
    public $successMessage = '';

    protected function rules()
    {
        $rules = [
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'review' => 'required|string|min:10|max:2000',
            'images.*' => 'nullable|image|max:2048',
        ];

        if (!auth()->check()) {
            $rules['reviewerName'] = 'required|string|max:255';
            $rules['reviewerEmail'] = 'required|email|max:255';
        }

        return $rules;
    }

    public function mount($productId)
    {
        $this->productId = $productId;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
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

        // Only authenticated users can review
        if (!auth()->check()) {
            $this->errorMessage = 'Please login to write a review. Only verified purchases can be reviewed.';
            return;
        }
        
        // Validate purchase - check for delivered or completed orders
        $hasPurchased = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.user_id', auth()->id())
            ->where('order_items.product_id', $this->productId)
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->exists();

        if (!$hasPurchased) {
            $this->errorMessage = 'You can only review products you have purchased and received.';
            return;
        }

        $this->validate();

        try {
            $data = [
                'product_id' => $this->productId,
                'rating' => $this->rating,
                'title' => $this->title,
                'review' => $this->review,
                'images' => $this->images,
            ];

            if (!auth()->check()) {
                $data['reviewer_name'] = $this->reviewerName;
                $data['reviewer_email'] = $this->reviewerEmail;
            }

            app(ProductReviewService::class)->createReview($data);

            // Reset form
            $this->reset(['rating', 'title', 'review', 'images', 'reviewerName', 'reviewerEmail']);

            $this->successMessage = 'Thank you! Your review has been submitted successfully and is pending approval.';
            $this->dispatch('review-submitted');
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.product.review-form');
    }
}
