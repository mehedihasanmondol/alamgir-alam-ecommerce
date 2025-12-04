<?php

namespace App\Modules\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:blog_categories,id',
            'media_id' => 'nullable|exists:media_library,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'featured_image_alt' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|url|max:500',
            'status' => 'required|in:draft,published,scheduled,unlisted',
            'published_at' => 'nullable|date',
            'scheduled_at' => 'nullable|date|after:now',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:blog_tags,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'শিরোনাম আবশ্যক',
            'title.max' => 'শিরোনাম সর্বোচ্চ ২৫৫ অক্ষরের হতে হবে',
            'slug.unique' => 'এই স্লাগ ইতিমধ্যে ব্যবহৃত হয়েছে',
            'content.required' => 'কন্টেন্ট আবশ্যক',
            'featured_image.image' => 'ফিচার্ড ইমেজ একটি ছবি হতে হবে',
            'featured_image.max' => 'ফিচার্ড ইমেজ সর্বোচ্চ ২ MB হতে পারে',
            'status.required' => 'স্ট্যাটাস আবশ্যক',
            'scheduled_at.after' => 'শিডিউল তারিখ ভবিষ্যতের হতে হবে',
        ];
    }
}
