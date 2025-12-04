<?php

namespace App\Modules\Ecommerce\Order\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Modules\Ecommerce\Order\Models\Order;
use App\Modules\Ecommerce\Order\Repositories\OrderRepository;
use App\Modules\Ecommerce\Order\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderService $orderService
    ) {
        // Middleware is applied in routes/web.php
    }

    /**
     * Display customer profile.
     */
    public function profile()
    {
        $user = auth()->user();
        $orders = $this->orderRepository->getUserOrders(auth()->id());

        return view('customer.profile', compact('user', 'orders'));
    }

    /**
     * Display customer's orders with search and filter.
     */
    public function index(Request $request)
    {
        $query = Order::where('user_id', auth()->id())
            ->with(['items.product', 'items.variant']);

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('order_number', 'like', "%{$searchTerm}%")
                  ->orWhereHas('items', function ($itemQuery) use ($searchTerm) {
                      $itemQuery->where('product_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort by latest first
        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order.');
        }

        $order = $this->orderRepository->find($order->id);

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Track order by order number.
     */
    public function track(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'order_number' => 'required|string',
                'email' => 'required|email',
            ]);

            $order = $this->orderRepository->findByOrderNumber($request->order_number);

            if (!$order || $order->customer_email !== $request->email) {
                return back()->with('error', 'Order not found or email does not match.');
            }

            return view('customer.orders.tracking', compact('order'));
        }

        return view('customer.orders.track');
    }

    /**
     * Cancel order.
     */
    public function cancel(Request $request, Order $order)
    {
        // Ensure user can only cancel their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order.');
        }

        try {
            $reason = $request->input('reason', 'Cancelled by customer');
            $this->orderService->cancelOrder($order, $reason);

            return redirect()
                ->route('customer.orders.show', $order)
                ->with('success', 'Order cancelled successfully.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    /**
     * Download invoice.
     */
    public function invoice(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order.');
        }

        $order = $this->orderRepository->find($order->id);

        return view('customer.orders.invoice', compact('order'));
    }
}
