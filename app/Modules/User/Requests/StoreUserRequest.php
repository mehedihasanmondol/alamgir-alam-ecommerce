<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * ModuleName: User Management
 * Purpose: Validation rules for creating new users
 * 
 * @author AI Assistant
 * @date 2025-11-04
 */
class StoreUserRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'mobile' => ['nullable', 'string', 'max:20', 'unique:users,mobile'],
            'password' => ['required', 'string', 'min:4'],
            'role' => ['required', 'in:admin,customer,author'],
            'is_active' => ['boolean'],
            'avatar' => ['nullable', 'image', 'max:2048'], // 2MB max
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
            
            // Author Profile Fields
            'author_bio' => ['nullable', 'string', 'max:2000'],
            'author_job_title' => ['nullable', 'string', 'max:255'],
            'author_website' => ['nullable', 'url', 'max:255'],
            'author_twitter' => ['nullable', 'string', 'max:50'],
            'author_facebook' => ['nullable', 'string', 'max:50'],
            'author_linkedin' => ['nullable', 'string', 'max:50'],
            'author_instagram' => ['nullable', 'string', 'max:50'],
            'author_github' => ['nullable', 'string', 'max:50'],
            'author_youtube' => ['nullable', 'string', 'max:50'],
            'author_whatsapp' => ['nullable', 'string', 'max:20'],
            'author_avatar' => ['nullable', 'image', 'max:2048'],
            'author_is_featured' => ['nullable', 'boolean'],
            'author_display_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'User name is required',
            'email.unique' => 'This email is already registered',
            'mobile.unique' => 'This mobile number is already registered',
            'password.required' => 'Password is required',
            'role.required' => 'User role is required',
            'avatar.image' => 'Avatar must be an image file',
            'avatar.max' => 'Avatar size must not exceed 2MB',
        ];
    }
}
