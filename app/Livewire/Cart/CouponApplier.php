<?php

namespace App\Livewire\Cart;

use App\Services\CouponService;
use Livewire\Component;

class CouponApplier extends Component
{
    public $couponCode = '';
    public $appliedCoupon = null;
    public $discountAmount = 0;
    public $freeShipping = false;
    public $message = '';
    public $messageType = ''; // success or error
    public $showCouponSection = false;
    public $availableCoupons = [];

    protected $listeners = ['cartUpdated' => 'revalidateCoupon'];

    public function mount()
    {
        // Check if coupon is already in session
        $this->loadCouponFromSession();
        
        // Load available coupons
        $this->loadAvailableCoupons();
        
        // Show section if coupon is applied
        if ($this->appliedCoupon) {
            $this->showCouponSection = true;
        }
    }

    public function applyCoupon(CouponService $couponService)
    {
        $this->message = '';
        $this->messageType = '';

        if (empty($this->couponCode)) {
            $this->message = 'Please enter a coupon code.';
            $this->messageType = 'error';
            return;
        }

        // Get cart data
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            $this->message = 'Your cart is empty.';
            $this->messageType = 'error';
            return;
        }

        // Calculate subtotal
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // Prepare cart items for validation
        $cartItems = collect($cart)->map(function ($item) {
            return [
                'product_id' => $item['product_id'] ?? $item['id'],
                'category_id' => $item['category_id'] ?? null,
            ];
        })->toArray();

        // Validate coupon
        $userId = auth()->id();
        $result = $couponService->validateCoupon(
            $this->couponCode,
            $subtotal,
            $userId,
            $cartItems
        );

        if ($result['success']) {
            $this->appliedCoupon = $result['coupon'];
            $this->discountAmount = $result['discount_amount'];
            $this->freeShipping = $result['free_shipping'];
            $this->message = $result['message'];
            $this->messageType = 'success';

            // Store in session
            session()->put('applied_coupon', [
                'id' => $this->appliedCoupon->id,
                'code' => $this->appliedCoupon->code,
                'discount_amount' => $this->discountAmount,
                'free_shipping' => $this->freeShipping,
            ]);

            // Dispatch event to update cart totals
            $this->dispatch('couponApplied', [
                'discount' => $this->discountAmount,
                'freeShipping' => $this->freeShipping,
            ]);
        } else {
            $this->message = $result['message'];
            $this->messageType = 'error';
        }
    }

    public function removeCoupon()
    {
        $this->appliedCoupon = null;
        $this->discountAmount = 0;
        $this->freeShipping = false;
        $this->couponCode = '';
        $this->message = '';
        $this->messageType = '';

        // Remove from session
        session()->forget('applied_coupon');

        // Dispatch event to update cart totals
        $this->dispatch('couponRemoved');
    }

    public function revalidateCoupon(CouponService $couponService)
    {
        if ($this->appliedCoupon) {
            // Re-apply the coupon to recalculate discount
            $this->applyCoupon($couponService);
        }
    }

    protected function loadCouponFromSession()
    {
        $sessionCoupon = session()->get('applied_coupon');
        
        if ($sessionCoupon) {
            $this->couponCode = $sessionCoupon['code'];
            $this->discountAmount = $sessionCoupon['discount_amount'];
            $this->freeShipping = $sessionCoupon['free_shipping'];
            
            // Load the full coupon model
            $this->appliedCoupon = \App\Models\Coupon::find($sessionCoupon['id']);
        }
    }

    protected function loadAvailableCoupons()
    {
        // Get cart data
        $cart = session()->get('cart', []);
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // Get active coupons that user can potentially use
        $this->availableCoupons = \App\Models\Coupon::active()
            ->available()
            ->where(function ($query) use ($subtotal) {
                $query->whereNull('min_purchase_amount')
                    ->orWhere('min_purchase_amount', '<=', $subtotal);
            })
            ->orderBy('value', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($coupon) use ($subtotal) {
                return [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'name' => $coupon->name,
                    'description' => $coupon->description,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'formatted_value' => $coupon->formatted_value,
                    'min_purchase_amount' => $coupon->min_purchase_amount,
                    'free_shipping' => $coupon->free_shipping,
                    'expires_at' => $coupon->expires_at,
                    'can_apply' => $subtotal >= ($coupon->min_purchase_amount ?? 0),
                ];
            })
            ->toArray();
    }

    public function toggleCouponSection()
    {
        $this->showCouponSection = !$this->showCouponSection;
    }

    public function quickApply($code)
    {
        $this->couponCode = $code;
        $this->applyCoupon(app(\App\Services\CouponService::class));
    }

    public function render()
    {
        return view('livewire.cart.coupon-applier');
    }
}
