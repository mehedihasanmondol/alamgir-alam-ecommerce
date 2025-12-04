<?php

namespace App\Http\Controllers;

use App\Modules\Ecommerce\Order\Models\Order;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Payment Controller
 * 
 * Handles payment initiation and callbacks
 */
class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Process payment from checkout (direct)
     */
    public function process($gateway, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Verify order belongs to user (allow guest orders)
        if (Auth::check() && $order->user_id && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if order is already paid
        if ($order->payment_status === 'paid') {
            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'This order is already paid');
        }

        try {
            $result = $this->paymentService->initiatePayment($order, $gateway);

            if ($result['success']) {
                return redirect($result['payment_url']);
            }

            return redirect()->route('cart.index')
                ->with('error', $result['message'] ?? 'Payment initiation failed');

        } catch (\Exception $e) {
            \Log::error('Payment processing error: ' . $e->getMessage());
            return redirect()->route('cart.index')
                ->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Initiate payment for an order
     */
    public function initiate(Request $request, Order $order)
    {
        // Verify order belongs to user
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if order is already paid
        if ($order->payment_status === 'paid') {
            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'This order is already paid');
        }

        $validated = $request->validate([
            'gateway' => 'required|string|exists:payment_gateways,slug',
        ]);

        try {
            $result = $this->paymentService->initiatePayment($order, $validated['gateway']);

            if ($result['success']) {
                return redirect($result['payment_url']);
            }

            return back()->with('error', $result['message'] ?? 'Payment initiation failed');

        } catch (\Exception $e) {
            \Log::error('Payment initiation error: ' . $e->getMessage());
            return back()->with('error', 'Payment initiation failed. Please try again.');
        }
    }

    /**
     * bKash payment callback
     */
    public function bkashCallback(Request $request)
    {
        try {
            $result = $this->paymentService->verifyPayment('bkash', $request->all());

            if ($result['success'] && isset($result['order'])) {
                return redirect()->route('customer.orders.show', $result['order']->id)
                    ->with('success', 'Payment successful! Your order has been confirmed.');
            }

            return redirect()->route('customer.orders.index')
                ->with('error', $result['message'] ?? 'Payment verification failed');

        } catch (\Exception $e) {
            \Log::error('bKash callback error: ' . $e->getMessage());
            return redirect()->route('customer.orders.index')
                ->with('error', 'Payment processing failed');
        }
    }

    /**
     * Nagad payment callback
     */
    public function nagadCallback(Request $request)
    {
        try {
            $result = $this->paymentService->verifyPayment('nagad', $request->all());

            if ($result['success'] && isset($result['order'])) {
                return redirect()->route('customer.orders.show', $result['order']->id)
                    ->with('success', 'Payment successful!');
            }

            return redirect()->route('customer.orders.index')
                ->with('error', $result['message'] ?? 'Payment verification failed');

        } catch (\Exception $e) {
            return redirect()->route('customer.orders.index')
                ->with('error', 'Payment processing failed');
        }
    }

    /**
     * SSL Commerz payment callbacks
     */
    public function sslcommerzSuccess(Request $request)
    {
        try {
            $result = $this->paymentService->verifyPayment('sslcommerz', $request->all());

            if ($result['success'] && isset($result['order'])) {
                return redirect()->route('customer.orders.show', $result['order']->id)
                    ->with('success', 'Payment successful!');
            }

            return redirect()->route('customer.orders.index')
                ->with('error', 'Payment verification failed');

        } catch (\Exception $e) {
            return redirect()->route('customer.orders.index')
                ->with('error', 'Payment processing failed');
        }
    }

    public function sslcommerzFail(Request $request)
    {
        return redirect()->route('customer.orders.index')
            ->with('error', 'Payment failed. Please try again.');
    }

    public function sslcommerzCancel(Request $request)
    {
        return redirect()->route('customer.orders.index')
            ->with('info', 'Payment cancelled.');
    }
}
