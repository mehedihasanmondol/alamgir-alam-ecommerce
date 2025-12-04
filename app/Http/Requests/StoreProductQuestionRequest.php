<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Anyone can ask questions
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'question' => 'required|string|min:10|max:500',
            'user_name' => 'required_without:user_id|string|max:255',
            'user_email' => 'required_without:user_id|email|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'question.required' => 'Please enter your question.',
            'question.min' => 'Question must be at least 10 characters.',
            'question.max' => 'Question cannot exceed 500 characters.',
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'user_name.required_without' => 'Please provide your name.',
            'user_email.required_without' => 'Please provide your email.',
        ];
    }
}
