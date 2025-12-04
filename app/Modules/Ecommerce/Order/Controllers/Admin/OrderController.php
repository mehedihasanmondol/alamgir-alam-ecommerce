<?php

namespace App\Modules\Ecommerce\Order\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Ecommerce\Order\Models\Order;
use App\Modules\Ecommerce\Order\Repositories\OrderRepository;
use App\Modules\Ecommerce\Order\Services\OrderService;
use App\Modules\Ecommerce\Order\Services\OrderStatusService;
use App\Modules\Ecommerce\Order\Requests\CreateOrderRequest;
use App\Modules\Ecommerce\Order\Requests\UpdateOrderRequest;
use App\Modules\Ecommerce\Order\Requests\UpdateOrderStatusRequest;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductVariant;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderService $orderService,
        protected OrderStatusService $statusService
    ) {
        // Middleware is applied in routes/admin.php
        // No policy authorization needed - admin middleware handles access
    }

    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $filters = [
            'status' => $request->get('status'),
            'payment_status' => $request->get('payment_status'),
            'search' => $request->get('search'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $orders = $this->orderRepository->paginate(15, $filters);
        $statistics = $this->orderService->getStatistics();

        return view('admin.orders.index', compact('orders', 'statistics', 'filters'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        return view('admin.orders.create');
    }

    /**
     * Store a newly created order.
     */
    public function store(CreateOrderRequest $request)
    {
        try {
            $validated = $request->validated();
            
            // Check stock restriction setting
            $stockRestrictionEnabled = ProductVariant::isStockRestrictionEnabled();
            
            // Prepare items data and validate stock if restriction is enabled
            $items = [];
            foreach ($validated['items'] as $itemData) {
                $product = Product::find($itemData['product_id']);
                
                // Get variant if provided (required per .windsurfrules)
                $variant = null;
                if (!empty($itemData['variant_id'])) {
                    $variant = ProductVariant::find($itemData['variant_id']);
                    
                    // Validate stock availability if restriction is enabled
                    if ($stockRestrictionEnabled) {
                        if (!$variant) {
                            return back()
                                ->withInput()
                                ->with('error', "Product variant not found for {$product->name}");
                        }
                        
                        if (!$variant->canAddToCart()) {
                            return back()
                                ->withInput()
                                ->with('error', "Product '{$product->name}' is currently out of stock and cannot be ordered.");
                        }
                        
                        if ($variant->stock_quantity < $itemData['quantity']) {
                            return back()
                                ->withInput()
                                ->with('error', "Insufficient stock for '{$product->name}'. Only {$variant->stock_quantity} available.");
                        }
                    }
                }
                
                $items[] = [
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'], // Use custom price from form
                ];
            }
            
            // Split customer name into first and last name
            $nameParts = explode(' ', $validated['customer_name'], 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';
            
            // Prepare order data
            $orderData = [
                'user_id' => !empty($validated['user_id']) ? $validated['user_id'] : null,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'customer_notes' => $validated['customer_notes'] ?? null,
                'payment_method' => $validated['payment_method'],
                'items' => $items,
                'billing_address' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $validated['customer_phone'],
                    'email' => $validated['customer_email'],
                    'address_line_1' => $validated['customer_address'],
                    'address_line_2' => null,
                    'city' => 'Dhaka',
                    'state' => null,
                    'postal_code' => '',
                    'country' => 'Bangladesh',
                ],
                'shipping_cost' => (float) $validated['shipping_cost'],
                'coupon_code' => $validated['coupon_code'] ?? null,
            ];
            
            // Add shipping address
            if (!empty($validated['same_as_billing'])) {
                // Use billing address (customer info) for shipping
                $orderData['shipping_address'] = $orderData['billing_address'];
            } else {
                // Use different shipping address if provided
                $shippingNameParts = explode(' ', $validated['shipping_name'] ?? '', 2);
                $orderData['shipping_address'] = [
                    'first_name' => $shippingNameParts[0] ?? '',
                    'last_name' => $shippingNameParts[1] ?? '',
                    'phone' => $validated['shipping_phone'] ?? '',
                    'email' => $validated['shipping_email'] ?? null,
                    'address_line_1' => $validated['shipping_address_line_1'] ?? '',
                    'address_line_2' => null,
                    'city' => 'Dhaka',
                    'state' => null,
                    'postal_code' => '',
                    'country' => 'Bangladesh',
                ];
            }
            
            // Create order
            $order = $this->orderService->createOrder($orderData);
            
            // Update payment status if paid
            if ($validated['payment_status'] === 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);
            }
            
            // Add admin notes
            if (!empty($validated['admin_notes'])) {
                $order->update(['admin_notes' => $validated['admin_notes']]);
            }
            
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Order created successfully.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order = $this->orderRepository->find($order->id);
        $availableStatuses = $this->statusService->getAvailableStatuses($order);

        return view('admin.orders.show', compact('order', 'availableStatuses'));
    }

    /**
     * Update the specified order.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        try {
            $this->orderService->updateOrder($order, $request->validated());

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Order updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    /**
     * Update order status.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        try {
            $validated = $request->validated();

            // Update tracking info if provided
            if (!empty($validated['tracking_number'])) {
                $this->orderService->updateOrder($order, [
                    'tracking_number' => $validated['tracking_number'],
                    'carrier' => $validated['carrier'] ?? null,
                ]);
            }

            // Update status
            $this->statusService->updateStatus(
                $order,
                $validated['status'],
                $validated['notes'] ?? null,
                $validated['notify_customer'] ?? true
            );

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Order status updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order.
     */
    public function cancel(Request $request, Order $order)
    {
        try {
            $reason = $request->input('reason', 'Cancelled by admin');
            $this->orderService->cancelOrder($order, $reason);

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Order cancelled successfully.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    /**
     * Delete order (soft delete).
     */
    public function destroy(Order $order)
    {
        try {
            $this->orderRepository->delete($order);

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Order deleted successfully.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    /**
     * Print invoice.
     */
    public function invoice(Order $order)
    {
        $order = $this->orderRepository->find($order->id);

        return view('admin.orders.invoice', compact('order'));
    }
}
