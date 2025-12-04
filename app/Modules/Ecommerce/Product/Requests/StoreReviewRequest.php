<?php

namespace App\Modules\Ecommerce\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ModuleName: Store Review Request
 * Purpose: Validate review submission data
 * 
 * @category Ecommerce
 * @package  Product
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'review' => 'required|string|min:10|max:2000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:2048', // 2MB max per image
        ];

        // Add name and email validation for guests
        if (!auth()->check()) {
            $rules['reviewer_name'] = 'required|string|max:255';
            $rules['reviewer_email'] = 'required|email|max:255';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Product is required.',
            'product_id.exists' => 'Invalid product selected.',
            'rating.required' => 'Please select a rating.',
            'rating.min' => 'Rating must be at least 1 star.',
            'rating.max' => 'Rating cannot exceed 5 stars.',
            'review.required' => 'Please write your review.',
            'review.min' => 'Review must be at least 10 characters.',
            'review.max' => 'Review cannot exceed 2000 characters.',
            'images.max' => 'You can upload maximum 5 images.',
            'images.*.image' => 'File must be an image.',
            'images.*.mimes' => 'Image must be jpeg, jpg, png, or webp.',
            'images.*.max' => 'Each image must not exceed 2MB.',
            'reviewer_name.required' => 'Name is required.',
            'reviewer_email.required' => 'Email is required.',
            'reviewer_email.email' => 'Please enter a valid email address.',
        ];
    }
}
