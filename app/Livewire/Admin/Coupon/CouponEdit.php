<?php

namespace App\Livewire\Admin\Coupon;

use App\Models\Coupon;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Services\CouponService;
use Livewire\Component;

class CouponEdit extends Component
{
    public Coupon $coupon;
    
    public $code = '';
    public $name = '';
    public $description = '';
    public $type = 'percentage';
    public $value = '';
    public $min_purchase_amount = '';
    public $max_discount_amount = '';
    public $usage_limit = '';
    public $usage_limit_per_user = '';
    public $starts_at = '';
    public $expires_at = '';
    public $is_active = true;
    public $first_order_only = false;
    public $free_shipping = false;
    
    public $applicable_categories = [];
    public $applicable_products = [];
    public $excluded_categories = [];
    public $excluded_products = [];

    public $categories = [];
    public $products = [];

    protected function rules()
    {
        return [
            'code' => 'required|string|max:50|unique:coupons,code,' . $this->coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'first_order_only' => 'boolean',
            'free_shipping' => 'boolean',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'excluded_categories' => 'nullable|array',
            'excluded_products' => 'nullable|array',
        ];
    }

    public function mount(Coupon $coupon)
    {
        $this->coupon = $coupon;
        
        $this->code = $coupon->code;
        $this->name = $coupon->name;
        $this->description = $coupon->description;
        $this->type = $coupon->type;
        $this->value = $coupon->value;
        $this->min_purchase_amount = $coupon->min_purchase_amount;
        $this->max_discount_amount = $coupon->max_discount_amount;
        $this->usage_limit = $coupon->usage_limit;
        $this->usage_limit_per_user = $coupon->usage_limit_per_user;
        $this->starts_at = $coupon->starts_at?->format('Y-m-d\TH:i');
        $this->expires_at = $coupon->expires_at?->format('Y-m-d\TH:i');
        $this->is_active = $coupon->is_active;
        $this->first_order_only = $coupon->first_order_only;
        $this->free_shipping = $coupon->free_shipping;
        
        $this->applicable_categories = $coupon->applicable_categories ?? [];
        $this->applicable_products = $coupon->applicable_products ?? [];
        $this->excluded_categories = $coupon->excluded_categories ?? [];
        $this->excluded_products = $coupon->excluded_products ?? [];

        $this->categories = Category::select('id', 'name')->get();
        $this->products = Product::select('id', 'name')->get();
    }

    public function update(CouponService $couponService)
    {
        $validated = $this->validate();

        // Convert empty strings to null
        $validated['min_purchase_amount'] = $validated['min_purchase_amount'] ?: null;
        $validated['max_discount_amount'] = $validated['max_discount_amount'] ?: null;
        $validated['usage_limit'] = $validated['usage_limit'] ?: null;
        $validated['usage_limit_per_user'] = $validated['usage_limit_per_user'] ?: null;
        $validated['starts_at'] = $validated['starts_at'] ?: null;
        $validated['expires_at'] = $validated['expires_at'] ?: null;

        // Convert empty arrays to null
        $validated['applicable_categories'] = !empty($validated['applicable_categories']) ? $validated['applicable_categories'] : null;
        $validated['applicable_products'] = !empty($validated['applicable_products']) ? $validated['applicable_products'] : null;
        $validated['excluded_categories'] = !empty($validated['excluded_categories']) ? $validated['excluded_categories'] : null;
        $validated['excluded_products'] = !empty($validated['excluded_products']) ? $validated['excluded_products'] : null;

        $couponService->update($this->coupon, $validated);

        session()->flash('success', 'Coupon updated successfully.');

        return redirect()->route('admin.coupons.index');
    }

    public function render()
    {
        return view('livewire.admin.coupon.coupon-edit');
    }
}
