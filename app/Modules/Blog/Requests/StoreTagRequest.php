<?php

namespace App\Modules\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_tags,slug',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'নাম আবশ্যক',
            'name.max' => 'নাম সর্বোচ্চ ২৫৫ অক্ষরের হতে হবে',
            'slug.unique' => 'এই স্লাগ ইতিমধ্যে ব্যবহৃত হয়েছে',
        ];
    }
}
