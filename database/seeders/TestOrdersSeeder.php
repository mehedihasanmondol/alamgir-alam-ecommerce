<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Ecommerce\Order\Models\Order;
use App\Modules\Ecommerce\Order\Models\OrderItem;
use App\Modules\Ecommerce\Order\Models\OrderAddress;
use App\Models\User;
use App\Modules\Ecommerce\Product\Models\Product;
use Illuminate\Support\Str;

class TestOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating test orders...');

        // Get or create test users
        $users = User::limit(10)->get();
        if ($users->count() < 10) {
            $this->command->warn('Not enough users found. Creating test users...');
            for ($i = $users->count(); $i < 10; $i++) {
                $users->push(User::create([
                    'name' => 'Test Customer ' . ($i + 1),
                    'email' => 'customer' . ($i + 1) . '@test.com',
                    'phone' => '01' . rand(700000000, 799999999),
                    'password' => bcrypt('password'),
                ]));
            }
        }

        // Get products (any status)
        $products = Product::limit(20)->get();
        if ($products->count() < 5) {
            $this->command->error('Not enough products found. Please create products first.');
            return;
        }
        $this->command->info('Found ' . $products->count() . ' products to use.');

        $statuses = ['pending', 'processing', 'confirmed', 'shipped', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed'];
        $paymentMethods = ['cod', 'bkash', 'nagad', 'rocket', 'card'];

        // Create 50 test orders
        for ($i = 1; $i <= 50; $i++) {
            $user = $users->random();
            $status = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone ?? '01' . rand(700000000, 799999999),
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'subtotal' => 0,
                'tax_amount' => 0,
                'shipping_cost' => rand(50, 150),
                'discount_amount' => rand(0, 500),
                'total_amount' => 0,
                'created_at' => now()->subDays(rand(0, 60)),
            ]);

            // Create order items (2-5 items per order)
            $itemCount = rand(2, 5);
            $subtotal = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                
                // Get first variant or create default
                $variant = $product->variants()->first();
                if (!$variant) {
                    // Skip products without variants
                    continue;
                }
                
                $price = $variant->price ?? $product->price ?? rand(500, 5000);
                $itemTotal = $price * $quantity;
                $subtotal += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $product->name,
                    'product_sku' => $variant->sku ?? $product->sku ?? 'SKU-' . rand(1000, 9999),
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $itemTotal,
                    'total' => $itemTotal,
                ]);
            }

            // Update order totals
            $taxAmount = $subtotal * 0.05; // 5% tax
            $totalAmount = $subtotal + $taxAmount + $order->shipping_cost - $order->discount_amount;

            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);

            // Create shipping address
            $nameParts = explode(' ', $user->name, 2);
            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'shipping',
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'phone' => $user->phone ?? '01' . rand(700000000, 799999999),
                'email' => $user->email,
                'address_line_1' => rand(1, 999) . ' Test Street',
                'address_line_2' => 'Apartment ' . rand(1, 50),
                'city' => ['Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna'][array_rand(['Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna'])],
                'state' => 'Bangladesh',
                'postal_code' => (string) rand(1000, 9999),
                'country' => 'Bangladesh',
            ]);

            // Create billing address (same as shipping for test)
            OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'billing',
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'phone' => $user->phone ?? '01' . rand(700000000, 799999999),
                'email' => $user->email,
                'address_line_1' => rand(1, 999) . ' Test Street',
                'address_line_2' => 'Apartment ' . rand(1, 50),
                'city' => ['Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna'][array_rand(['Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna'])],
                'state' => 'Bangladesh',
                'postal_code' => (string) rand(1000, 9999),
                'country' => 'Bangladesh',
            ]);

            if ($i % 10 == 0) {
                $this->command->info("Created {$i} orders...");
            }
        }

        $this->command->info('✅ Successfully created 50 test orders!');
    }
}
