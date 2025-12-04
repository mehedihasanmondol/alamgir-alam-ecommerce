<?php

namespace App\Modules\Ecommerce\Order\Services;

class OrderCalculationService
{
    /**
     * Tax rate (percentage).
     */
    protected float $taxRate = 0; // 0% tax, adjust as needed

    /**
     * Calculate order totals.
     */
    public function calculateOrderTotals(
        array $items,
        float $shippingCost = 0,
        float $couponDiscount = 0
    ): array {
        $subtotal = $this->calculateSubtotal($items);
        $taxAmount = $this->calculateTax($subtotal);
        
        // Total amount calculation: subtotal + tax + shipping - coupon discount
        $totalAmount = $subtotal + $taxAmount + $shippingCost - $couponDiscount;

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'shipping_cost' => round($shippingCost, 2),
            'discount_amount' => round($couponDiscount, 2),
            'total_amount' => round(max(0, $totalAmount), 2), // Ensure total is never negative
        ];
    }

    /**
     * Calculate subtotal from items.
     */
    protected function calculateSubtotal(array $items): float
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $product = $item['product'];
            $variant = $item['variant'] ?? null;
            $price = $variant ? $variant->price : $product->price;
            $quantity = $item['quantity'];

            $subtotal += $price * $quantity;
        }

        return $subtotal;
    }

    /**
     * Calculate tax amount.
     */
    protected function calculateTax(float $subtotal): float
    {
        return $subtotal * ($this->taxRate / 100);
    }

    /**
     * Calculate product-level discount amount.
     * Note: Coupon discounts are handled separately and passed directly.
     */
    protected function calculateProductDiscount(array $items): float
    {
        $discount = 0;
        
        foreach ($items as $item) {
            // If item has a discount (original_price vs sale_price)
            if (isset($item['original_price']) && isset($item['price'])) {
                $itemDiscount = ($item['original_price'] - $item['price']) * $item['quantity'];
                $discount += max(0, $itemDiscount);
            }
        }
        
        return $discount;
    }

    /**
     * Calculate shipping cost based on location and weight.
     */
    public function calculateShippingCost(string $city, float $weight = 0): float
    {
        // Basic shipping calculation
        // Inside Dhaka: 60 BDT
        // Outside Dhaka: 120 BDT
        
        $insideDhaka = ['dhaka', 'ঢাকা'];
        
        if (in_array(strtolower($city), $insideDhaka)) {
            return 60;
        }

        return 120;
    }

    /**
     * Set tax rate.
     */
    public function setTaxRate(float $rate): void
    {
        $this->taxRate = $rate;
    }
}
