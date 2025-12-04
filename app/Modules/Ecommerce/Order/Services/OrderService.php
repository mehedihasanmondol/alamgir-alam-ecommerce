<?php

namespace App\Modules\Ecommerce\Order\Services;

use App\Modules\Ecommerce\Order\Models\Order;
use App\Modules\Ecommerce\Order\Models\OrderAddress;
use App\Modules\Ecommerce\Order\Models\OrderItem;
use App\Modules\Ecommerce\Order\Repositories\OrderRepository;
use App\Modules\Ecommerce\Product\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderStatusService $statusService,
        protected OrderCalculationService $calculationService
    ) {}

    /**
     * Create new order from cart data.
     */
    public function createOrder(array $data): Order
    {
        try {
            DB::beginTransaction();

            // Separate coupon discount from general discount
            // When order is from frontend with coupon, don't pass it as general discount
            $couponDiscount = 0;
            $generalDiscount = 0;
            
            if (!empty($data['coupon_code']) && !empty($data['discount_amount'])) {
                // This is a coupon discount from frontend
                $couponDiscount = $data['discount_amount'];
            } elseif (!empty($data['discount_amount'])) {
                // This is a general discount (admin-applied)
                $generalDiscount = $data['discount_amount'];
            }
            
            // Calculate totals with general discount only (not coupon)
            $calculations = $this->calculationService->calculateOrderTotals(
                $data['items'],
                $data['shipping_cost'] ?? 0,
                $generalDiscount
            );

            // Get delivery zone and method names for historical record
            $deliveryZoneName = null;
            $deliveryMethodName = null;
            
            if (!empty($data['delivery_zone_id'])) {
                $deliveryZone = \App\Modules\Ecommerce\Delivery\Models\DeliveryZone::find($data['delivery_zone_id']);
                $deliveryZoneName = $deliveryZone?->name;
            }
            
            if (!empty($data['delivery_method_id'])) {
                $deliveryMethod = \App\Modules\Ecommerce\Delivery\Models\DeliveryMethod::find($data['delivery_method_id']);
                $deliveryMethodName = $deliveryMethod?->name;
            }

            // Create order
            $order = $this->orderRepository->create([
                'user_id' => $data['user_id'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'customer_notes' => $data['customer_notes'] ?? null,
                'payment_method' => $data['payment_method'],
                'subtotal' => $calculations['subtotal'],
                'tax_amount' => $calculations['tax_amount'],
                'shipping_cost' => $calculations['shipping_cost'],
                'discount_amount' => $generalDiscount,  // General discount only
                'coupon_discount' => $couponDiscount,  // Coupon discount in separate field
                'total_amount' => $calculations['total_amount'] - $couponDiscount,  // Subtract coupon discount from total
                'coupon_code' => $data['coupon_code'] ?? null,
                'ip_address' => request()->ip(),
                'status' => 'pending',
                'payment_status' => $data['payment_method'] === 'cod' ? 'pending' : 'pending',
                // Delivery information
                'delivery_zone_id' => $data['delivery_zone_id'] ?? null,
                'delivery_method_id' => $data['delivery_method_id'] ?? null,
                'delivery_zone_name' => $deliveryZoneName,
                'delivery_method_name' => $deliveryMethodName,
            ]);

            // Create order items
            foreach ($data['items'] as $item) {
                $this->createOrderItem($order, $item);
            }

            // Create billing address
            if (!empty($data['billing_address'])) {
                $this->createOrderAddress($order, $data['billing_address'], 'billing');
            }

            // Create shipping address
            if (!empty($data['shipping_address'])) {
                $this->createOrderAddress($order, $data['shipping_address'], 'shipping');
            } else {
                // Use billing address as shipping address
                $this->createOrderAddress($order, $data['billing_address'], 'shipping');
            }

            // Create initial status history
            $this->statusService->createStatusHistory($order, null, 'pending', 'Order created');

            // Update product stock
            $this->updateProductStock($data['items']);

            DB::commit();

            return $order->load(['items', 'billingAddress', 'shippingAddress']);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create order item.
     */
    protected function createOrderItem(Order $order, array $itemData): OrderItem
    {
        $product = $itemData['product'];
        $variant = $itemData['variant'] ?? null;

        // Use custom price from form if provided, otherwise use variant/product price
        $price = $itemData['price'] ?? ($variant->price ?? $product->price);
        $subtotal = $price * $itemData['quantity'];

        // Format variant attributes as key-value pairs
        $variantAttributes = null;
        if ($variant && isset($variant->attributeValues) && $variant->attributeValues->count() > 0) {
            $variantAttributes = [];
            foreach ($variant->attributeValues as $attributeValue) {
                $attributeName = $attributeValue->attribute->name ?? 'Attribute';
                $variantAttributes[$attributeName] = $attributeValue->value;
            }
        }

        return OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id ?? null, // Allow null for products without variants
            'product_name' => $product->name,
            'product_sku' => ($variant && isset($variant->sku)) ? $variant->sku : ($product->sku ?? 'N/A'),
            'variant_name' => ($variant && isset($variant->name)) ? $variant->name : null,
            'variant_attributes' => $variantAttributes,
            'price' => $price,
            'quantity' => $itemData['quantity'],
            'subtotal' => $subtotal,
            'tax_amount' => 0, // Calculate if needed
            'discount_amount' => 0, // Calculate if needed
            'total' => $subtotal,
            'product_image' => $product->image_url ?? null,
        ]);
    }

    /**
     * Create order address.
     */
    protected function createOrderAddress(Order $order, array $addressData, string $type): OrderAddress
    {
        return OrderAddress::create([
            'order_id' => $order->id,
            'type' => $type,
            'first_name' => $addressData['first_name'],
            'last_name' => $addressData['last_name'],
            'email' => $addressData['email'] ?? null,
            'phone' => $addressData['phone'],
            'company' => $addressData['company'] ?? null,
            'address_line_1' => $addressData['address_line_1'],
            'address_line_2' => $addressData['address_line_2'] ?? null,
            'city' => $addressData['city'],
            'state' => $addressData['state'] ?? null,
            'postal_code' => $addressData['postal_code'],
            'country' => $addressData['country'] ?? 'Bangladesh',
        ]);
    }

    /**
     * Update product stock after order.
     * Only reduces stock if stock restriction is enabled.
     */
    protected function updateProductStock(array $items): void
    {
        // Only update stock if restriction is enabled
        if (!ProductVariant::isStockRestrictionEnabled()) {
            return;
        }
        
        foreach ($items as $item) {
            if (isset($item['variant'])) {
                $variant = ProductVariant::find($item['variant']->id);
                if ($variant) {
                    $variant->decrement('stock_quantity', $item['quantity']);
                }
            }
        }
    }

    /**
     * Update order.
     */
    public function updateOrder(Order $order, array $data): bool
    {
        return $this->orderRepository->update($order, $data);
    }

    /**
     * Cancel order.
     */
    public function cancelOrder(Order $order, string $reason = null): bool
    {
        if (!$order->canBeCancelled()) {
            throw new Exception('This order cannot be cancelled.');
        }

        try {
            DB::beginTransaction();

            // Update order status
            $this->orderRepository->update($order, [
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Create status history
            $this->statusService->createStatusHistory(
                $order,
                $order->status,
                'cancelled',
                $reason ?? 'Order cancelled'
            );

            // Restore product stock (only if stock restriction is enabled)
            if (ProductVariant::isStockRestrictionEnabled()) {
                foreach ($order->items as $item) {
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::find($item->product_variant_id);
                        if ($variant) {
                            $variant->increment('stock_quantity', $item->quantity);
                        }
                    }
                }
            }

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get order statistics.
     */
    public function getStatistics(array $filters = []): array
    {
        return $this->orderRepository->getStatistics($filters);
    }

    /**
     * Get daily orders data for chart.
     */
    public function getDailyOrdersData(int $days = 7): array
    {
        return $this->orderRepository->getDailyOrdersCount($days);
    }

    /**
     * Get daily revenue data for chart.
     */
    public function getDailyRevenueData(int $days = 7): array
    {
        return $this->orderRepository->getDailyRevenue($days);
    }

    /**
     * Get status distribution.
     */
    public function getStatusDistribution(): array
    {
        return $this->orderRepository->getStatusDistribution();
    }
}
