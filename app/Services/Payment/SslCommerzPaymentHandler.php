<?php

namespace App\Services\Payment;

use App\Models\PaymentGateway;
use App\Modules\Ecommerce\Order\Models\Order;

/**
 * SSL Commerz Payment Handler
 * TODO: Implement SSL Commerz payment integration
 */
class SslCommerzPaymentHandler
{
    public function initiate(Order $order, PaymentGateway $gateway)
    {
        return [
            'success' => false,
            'message' => 'SSL Commerz payment is not yet implemented',
        ];
    }

    public function verify($data, PaymentGateway $gateway)
    {
        return [
            'success' => false,
            'message' => 'SSL Commerz payment is not yet implemented',
        ];
    }
}
