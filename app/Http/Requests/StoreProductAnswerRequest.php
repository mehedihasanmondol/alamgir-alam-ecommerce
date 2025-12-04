<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductAnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Anyone can answer questions
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question_id' => 'required|exists:product_questions,id',
            'answer' => 'required|string|min:10|max:1000',
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
            'answer.required' => 'Please enter your answer.',
            'answer.min' => 'Answer must be at least 10 characters.',
            'answer.max' => 'Answer cannot exceed 1000 characters.',
            'question_id.required' => 'Question ID is required.',
            'question_id.exists' => 'The selected question does not exist.',
            'user_name.required_without' => 'Please provide your name.',
            'user_email.required_without' => 'Please provide your email.',
        ];
    }
}
