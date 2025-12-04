<?php

namespace App\Services\Payment;

use App\Models\PaymentGateway;
use App\Modules\Ecommerce\Order\Models\Order;
use Exception;

/**
 * Payment Service
 * 
 * Handles payment processing for different gateways
 */
class PaymentService
{
    /**
     * Initiate payment for an order
     */
    public function initiatePayment(Order $order, string $gatewaySlug)
    {
        $gateway = PaymentGateway::where('slug', $gatewaySlug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get appropriate payment handler
        $handler = $this->getPaymentHandler($gateway);

        return $handler->initiate($order, $gateway);
    }

    /**
     * Verify payment callback
     */
    public function verifyPayment($gatewaySlug, $data)
    {
        $gateway = PaymentGateway::where('slug', $gatewaySlug)->firstOrFail();
        
        $handler = $this->getPaymentHandler($gateway);

        return $handler->verify($data, $gateway);
    }

    /**
     * Get payment handler based on gateway
     */
    protected function getPaymentHandler(PaymentGateway $gateway)
    {
        return match($gateway->slug) {
            'bkash' => new BkashPaymentHandler(),
            'nagad' => new NagadPaymentHandler(),
            'sslcommerz' => new SslCommerzPaymentHandler(),
            default => throw new Exception("Unsupported payment gateway: {$gateway->slug}"),
        };
    }

    /**
     * Get active payment gateways
     */
    public function getActiveGateways()
    {
        return PaymentGateway::active()->get();
    }
}
