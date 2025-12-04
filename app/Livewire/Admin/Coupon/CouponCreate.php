<?php

namespace App\Livewire\Admin\Coupon;

use App\Models\Coupon;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Services\CouponService;
use Livewire\Component;

class CouponCreate extends Component
{
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
            'code' => 'required|string|max:50|unique:coupons,code',
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

    public function mount()
    {
        $this->categories = Category::select('id', 'name')->get();
        $this->products = Product::select('id', 'name')->get();
    }

    public function generateCode(CouponService $couponService)
    {
        $this->code = $couponService->generateCode();
    }

    public function save(CouponService $couponService)
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

        $couponService->create($validated);

        session()->flash('success', 'Coupon created successfully.');

        return redirect()->route('admin.coupons.index');
    }

    public function render()
    {
        return view('livewire.admin.coupon.coupon-create');
    }
}
