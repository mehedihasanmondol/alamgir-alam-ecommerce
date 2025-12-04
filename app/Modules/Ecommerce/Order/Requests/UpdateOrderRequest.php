<?php

namespace App\Modules\Ecommerce\Order\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Route is already protected by admin middleware, so just check authentication.
     */
    public function authorize(): bool
    {
        return true; // Admin middleware already handles authorization
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['sometimes', 'string', 'max:255'],
            'customer_email' => ['sometimes', 'email', 'max:255'],
            'customer_phone' => ['sometimes', 'string', 'max:20'],
            'payment_method' => ['sometimes', 'string', 'in:cod,bkash,nagad,rocket,card,bank_transfer'],
            'payment_status' => ['sometimes', 'string', 'in:pending,paid,failed,refunded'],
            'shipping_cost' => ['sometimes', 'numeric', 'min:0'],
            'discount_amount' => ['sometimes', 'numeric', 'min:0'],
            'admin_notes' => ['nullable', 'string'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'carrier' => ['nullable', 'string', 'max:100'],
            // Delivery fields
            'delivery_zone_id' => ['nullable', 'exists:delivery_zones,id'],
            'delivery_method_id' => ['nullable', 'exists:delivery_methods,id'],
            'delivery_status' => ['nullable', 'string', 'in:pending,picked_up,in_transit,out_for_delivery,delivered,failed,returned'],
            'estimated_delivery' => ['nullable', 'date'],
        ];
    }
}
