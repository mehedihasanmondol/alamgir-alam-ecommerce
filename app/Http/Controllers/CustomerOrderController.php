<?php

namespace App\Http\Controllers;

use App\Modules\Ecommerce\Order\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    /**
     * Display customer's orders
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with([
                'items.product.images', 
                'items.variant',
                'deliveryZone', 
                'deliveryMethod'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Display single order details
     */
    public function show(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        $order->load([
            'items.product.images',
            'items.product.categories',
            'items.product.brand',
            'items.variant',
            'shippingAddress',
            'billingAddress',
            'deliveryZone',
            'deliveryMethod',
            'statusHistories' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'This order cannot be cancelled');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled successfully');
    }

    /**
     * Download invoice
     */
    public function invoice(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'shippingAddress', 'billingAddress']);

        return view('customer.orders.invoice', compact('order'));
    }

    /**
     * Track order (public)
     */
    public function track(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'order_number' => 'required|string',
                'email' => 'required|email',
            ]);

            $order = Order::where('order_number', $request->order_number)
                ->where('customer_email', $request->email)
                ->with(['items.product', 'statusHistories'])
                ->first();

            if (!$order) {
                return back()->with('error', 'Order not found');
            }

            return view('frontend.orders.track-result', compact('order'));
        }

        return view('frontend.orders.track');
    }
}
