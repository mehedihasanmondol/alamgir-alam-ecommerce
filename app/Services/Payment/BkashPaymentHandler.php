<?php

namespace App\Services\Payment;

use App\Models\PaymentGateway;
use App\Modules\Ecommerce\Order\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * bKash Payment Handler
 * 
 * Handles bKash tokenized payment integration
 */
class BkashPaymentHandler
{
    protected $token;

    /**
     * Initiate bKash payment
     */
    public function initiate(Order $order, PaymentGateway $gateway)
    {
        try {
            // Get auth token
            $this->token = $this->getToken($gateway);

            // Create payment
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'X-APP-Key' => $gateway->getCredential('app_key'),
            ])->post($gateway->getCredential('base_url') . '/tokenized/checkout/create', [
                'mode' => '0011',
                'payerReference' => $order->order_number,
                'callbackURL' => $gateway->getSetting('callback_url'),
                'amount' => number_format($order->total_amount, 2, '.', ''),
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $order->order_number,
            ]);

            $result = $response->json();

            if (isset($result['statusCode']) && $result['statusCode'] === '0000') {
                // Store payment ID in order
                $order->update([
                    'payment_gateway_id' => $gateway->id,
                    'transaction_id' => $result['paymentID'] ?? null,
                ]);

                return [
                    'success' => true,
                    'payment_url' => $result['bkashURL'] ?? null,
                    'payment_id' => $result['paymentID'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $result['statusMessage'] ?? 'Payment initiation failed',
            ];

        } catch (\Exception $e) {
            Log::error('bKash payment initiation error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment initiation failed. Please try again.',
            ];
        }
    }

    /**
     * Verify bKash payment
     */
    public function verify($data, PaymentGateway $gateway)
    {
        try {
            $paymentID = $data['paymentID'] ?? null;
            $status = $data['status'] ?? null;

            if (!$paymentID || $status !== 'success') {
                return [
                    'success' => false,
                    'message' => 'Payment verification failed',
                ];
            }

            // Get auth token
            $this->token = $this->getToken($gateway);

            // Execute payment
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'X-APP-Key' => $gateway->getCredential('app_key'),
            ])->post($gateway->getCredential('base_url') . '/tokenized/checkout/execute', [
                'paymentID' => $paymentID,
            ]);

            $result = $response->json();

            if (isset($result['statusCode']) && $result['statusCode'] === '0000') {
                // Find order by transaction ID
                $order = Order::where('transaction_id', $paymentID)->first();

                if ($order) {
                    $order->update([
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                    ]);
                }

                return [
                    'success' => true,
                    'order' => $order,
                    'transaction_id' => $result['trxID'] ?? $paymentID,
                ];
            }

            return [
                'success' => false,
                'message' => $result['statusMessage'] ?? 'Payment execution failed',
            ];

        } catch (\Exception $e) {
            Log::error('bKash payment verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment verification failed',
            ];
        }
    }

    /**
     * Get bKash auth token
     */
    protected function getToken(PaymentGateway $gateway)
    {
        $response = Http::withHeaders([
            'username' => $gateway->getCredential('username'),
            'password' => $gateway->getCredential('password'),
        ])->post($gateway->getCredential('base_url') . '/tokenized/checkout/token/grant', [
            'app_key' => $gateway->getCredential('app_key'),
            'app_secret' => $gateway->getCredential('app_secret'),
        ]);

        $result = $response->json();

        if (isset($result['id_token'])) {
            return $result['id_token'];
        }

        throw new \Exception('Failed to get bKash token');
    }
}
