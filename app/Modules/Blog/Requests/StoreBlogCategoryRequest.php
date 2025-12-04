<?php

namespace App\Modules\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug',
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
            'name.max' => 'নাম সর্বোচ্চ ২৫৫ অক্ষরের হতে হবে',
            'slug.unique' => 'এই স্লাগ ইতিমধ্যে ব্যবহৃত হয়েছে',
            'parent_id.exists' => 'প্যারেন্ট ক্যাটাগরি বৈধ নয়',
            'image.image' => 'ইমেজ একটি ছবি হতে হবে',
            'image.max' => 'ইমেজ সর্বোচ্চ ২ MB হতে পারে',
        ];
    }
}
