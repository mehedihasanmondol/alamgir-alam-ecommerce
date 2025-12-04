<?php

namespace App\Modules\Ecommerce\Order\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow authenticated users (admin middleware is already applied in routes)
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Customer Information
            'user_id' => ['nullable', 'exists:users,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_address' => ['required', 'string', 'max:500'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
            
            // Order Items
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.variant_id' => ['nullable', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            
            // Billing Address (not used - kept for backward compatibility)
            // We use customer_address instead
            
            // Shipping Address (simplified)
            'same_as_billing' => ['boolean'],
            'shipping_name' => ['required_if:same_as_billing,false', 'nullable', 'string', 'max:255'],
            'shipping_phone' => ['required_if:same_as_billing,false', 'nullable', 'string', 'max:20'],
            'shipping_email' => ['nullable', 'email', 'max:255'],
            'shipping_address_line_1' => ['required_if:same_as_billing,false', 'nullable', 'string', 'max:500'],
            
            // Payment & Pricing
            'payment_method' => ['required', 'string', 'in:cod,bkash,nagad,rocket,card,bank_transfer'],
            'payment_status' => ['required', 'string', 'in:pending,paid,failed,refunded'],
            'shipping_cost' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            
            // Admin Notes
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'At least one product is required.',
            'items.*.product_id.required' => 'Product is required.',
            'items.*.quantity.required' => 'Quantity is required.',
            'items.*.price.required' => 'Price is required.',
        ];
    }
}
