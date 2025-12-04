<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'bKash',
                'slug' => 'bkash',
                'description' => 'bKash Payment Gateway - Leading mobile financial service in Bangladesh',
                'is_active' => false,
                'is_test_mode' => true,
                'credentials' => [
                    'app_key' => '',
                    'app_secret' => '',
                    'username' => '',
                    'password' => '',
                    'base_url' => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta',
                ],
                'settings' => [
                    'callback_url' => url('/payment/bkash/callback'),
                    'intent' => 'sale',
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Nagad',
                'slug' => 'nagad',
                'description' => 'Nagad Payment Gateway - Digital financial service by Bangladesh Post Office',
                'is_active' => false,
                'is_test_mode' => true,
                'credentials' => [
                    'merchant_id' => '',
                    'merchant_number' => '',
                    'public_key' => '',
                    'private_key' => '',
                    'base_url' => 'http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0',
                ],
                'settings' => [
                    'callback_url' => url('/payment/nagad/callback'),
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'SSL Commerz',
                'slug' => 'sslcommerz',
                'description' => 'SSL Commerz - Leading Payment Gateway in Bangladesh',
                'is_active' => false,
                'is_test_mode' => true,
                'credentials' => [
                    'store_id' => '',
                    'store_password' => '',
                    'base_url' => 'https://sandbox.sslcommerz.com',
                ],
                'settings' => [
                    'success_url' => url('/payment/sslcommerz/success'),
                    'fail_url' => url('/payment/sslcommerz/fail'),
                    'cancel_url' => url('/payment/sslcommerz/cancel'),
                ],
                'sort_order' => 3,
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(
                ['slug' => $gateway['slug']],
                $gateway
            );
        }
    }
}
