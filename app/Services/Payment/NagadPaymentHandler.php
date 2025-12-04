<?php

namespace App\Services\Payment;

use App\Models\PaymentGateway;
use App\Modules\Ecommerce\Order\Models\Order;

/**
 * Nagad Payment Handler
 * TODO: Implement Nagad payment integration
 */
class NagadPaymentHandler
{
    public function initiate(Order $order, PaymentGateway $gateway)
    {
        return [
            'success' => false,
            'message' => 'Nagad payment is not yet implemented',
        ];
    }

    public function verify($data, PaymentGateway $gateway)
    {
        return [
            'success' => false,
            'message' => 'Nagad payment is not yet implemented',
        ];
    }
}
