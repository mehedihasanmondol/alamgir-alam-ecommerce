<?php

namespace App\Modules\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * ModuleName: Blog
 * Purpose: Form request validation for tick marks
 * 
 * @category Blog
 * @package  App\Modules\Blog\Requests
 * @author   AI Assistant
 * @created  2025-11-10
 */
class TickMarkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $tickMarkId = $this->route('tick_mark');
        
        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('blog_tick_marks', 'slug')->ignore($tickMarkId)
            ],
            'label' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:50'],
            'bg_color' => ['required', 'string', 'max:50'],
            'text_color' => ['required', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'name' => 'tick mark name',
            'label' => 'display label',
            'icon' => 'icon name',
            'bg_color' => 'background color',
            'text_color' => 'text color',
            'sort_order' => 'sort order',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a name for the tick mark',
            'label.required' => 'Please enter a display label',
            'icon.required' => 'Please select an icon',
            'color.required' => 'Please select a color',
        ];
    }
}
