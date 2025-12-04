<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;
use App\Modules\Ecommerce\Order\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CouponService
{
    /**
     * Validate and apply coupon
     */
    public function validateCoupon(string $code, float $subtotal, ?int $userId = null, array $cartItems = []): array
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'Invalid coupon code.',
            ];
        }

        // Check if coupon is valid
        if (!$coupon->isValid()) {
            return [
                'success' => false,
                'message' => 'This coupon is no longer valid.',
            ];
        }

        // Check minimum purchase amount
        if ($coupon->min_purchase_amount && $subtotal < $coupon->min_purchase_amount) {
            return [
                'success' => false,
                'message' => "Minimum purchase amount of ৳" . number_format($coupon->min_purchase_amount, 2) . " required.",
            ];
        }

        // Check user-specific validations
        if ($userId) {
            // Check if user can use this coupon
            if (!$coupon->canBeUsedByUser($userId)) {
                return [
                    'success' => false,
                    'message' => 'You have reached the usage limit for this coupon.',
                ];
            }

            // Check if first order only
            if ($coupon->first_order_only) {
                $hasOrders = Order::where('user_id', $userId)
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if ($hasOrders) {
                    return [
                        'success' => false,
                        'message' => 'This coupon is only valid for first-time orders.',
                    ];
                }
            }
        }

        // Check product/category restrictions
        if (!$this->checkProductRestrictions($coupon, $cartItems)) {
            return [
                'success' => false,
                'message' => 'This coupon is not applicable to items in your cart.',
            ];
        }

        // Calculate discount
        $discountAmount = $coupon->calculateDiscount($subtotal);

        return [
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'coupon' => $coupon,
            'discount_amount' => $discountAmount,
            'free_shipping' => $coupon->free_shipping,
        ];
    }

    /**
     * Check product/category restrictions
     */
    protected function checkProductRestrictions(Coupon $coupon, array $cartItems): bool
    {
        if (empty($cartItems)) {
            return true;
        }

        $hasApplicableItems = false;

        foreach ($cartItems as $item) {
            $productId = $item['product_id'] ?? null;
            $categoryId = $item['category_id'] ?? null;

            // Check excluded products
            if ($coupon->excluded_products && in_array($productId, $coupon->excluded_products)) {
                continue;
            }

            // Check excluded categories
            if ($coupon->excluded_categories && in_array($categoryId, $coupon->excluded_categories)) {
                continue;
            }

            // If specific products are set, check if this product is included
            if ($coupon->applicable_products && !in_array($productId, $coupon->applicable_products)) {
                continue;
            }

            // If specific categories are set, check if this category is included
            if ($coupon->applicable_categories && !in_array($categoryId, $coupon->applicable_categories)) {
                continue;
            }

            $hasApplicableItems = true;
            break;
        }

        // If restrictions exist but no applicable items found
        if (($coupon->applicable_products || $coupon->applicable_categories) && !$hasApplicableItems) {
            return false;
        }

        return true;
    }

    /**
     * Record coupon usage
     */
    public function recordUsage(Coupon $coupon, int $userId, float $discountAmount, ?int $orderId = null): void
    {
        DB::transaction(function () use ($coupon, $userId, $discountAmount, $orderId) {
            // Record in pivot table
            $coupon->users()->attach($userId, [
                'order_id' => $orderId,
                'discount_amount' => $discountAmount,
                'used_at' => now(),
            ]);

            // Increment usage count
            $coupon->incrementUsage();
        });
    }

    /**
     * Create new coupon
     */
    public function create(array $data): Coupon
    {
        $data['code'] = strtoupper($data['code']);
        
        return Coupon::create($data);
    }

    /**
     * Update coupon
     */
    public function update(Coupon $coupon, array $data): Coupon
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $coupon->update($data);
        
        return $coupon->fresh();
    }

    /**
     * Delete coupon
     */
    public function delete(Coupon $coupon): bool
    {
        return $coupon->delete();
    }

    /**
     * Generate random coupon code
     */
    public function generateCode(int $length = 8): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }

    /**
     * Get coupon statistics
     */
    public function getStatistics(Coupon $coupon): array
    {
        $totalRevenue = $coupon->users()->sum('discount_amount');
        $uniqueUsers = $coupon->users()->distinct('user_id')->count();
        
        $usagePercentage = 0;
        if ($coupon->usage_limit) {
            $usagePercentage = ($coupon->total_used / $coupon->usage_limit) * 100;
        }

        return [
            'total_used' => $coupon->total_used,
            'unique_users' => $uniqueUsers,
            'total_discount_given' => $totalRevenue,
            'usage_percentage' => round($usagePercentage, 2),
            'remaining_uses' => $coupon->usage_limit ? max(0, $coupon->usage_limit - $coupon->total_used) : null,
        ];
    }

    /**
     * Get active coupons for display
     */
    public function getActiveCoupons()
    {
        return Coupon::active()
            ->available()
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
