<?php

namespace App\Modules\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category');

        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug,' . $categoryId,
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:blog_categories,id',
            'media_id' => 'nullable|exists:media_library,id',  // NEW: Media library support
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'নাম আবশ্যক',
            'slug.unique' => 'এই স্লাগ ইতিমধ্যে ব্যবহৃত হয়েছে',
            'image.image' => 'ইমেজ একটি ছবি হতে হবে',
        ];
    }
}
