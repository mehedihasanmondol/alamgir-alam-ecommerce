<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Ecommerce\Delivery\Models\DeliveryZone;
use App\Modules\Ecommerce\Delivery\Models\DeliveryMethod;
use App\Modules\Ecommerce\Delivery\Models\DeliveryRate;

class DeliverySystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Delivery Zones
        $dhakaCity = DeliveryZone::create([
            'name' => 'Dhaka City',
            'code' => 'dhaka-city',
            'description' => 'Inside Dhaka city area',
            'countries' => ['BD'],
            'states' => ['Dhaka'],
            'cities' => ['Dhaka'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $outsideDhaka = DeliveryZone::create([
            'name' => 'Outside Dhaka',
            'code' => 'outside-dhaka',
            'description' => 'Areas outside Dhaka city',
            'countries' => ['BD'],
            'states' => ['Chittagong', 'Sylhet', 'Rajshahi', 'Khulna', 'Barisal', 'Rangpur', 'Mymensingh'],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $international = DeliveryZone::create([
            'name' => 'International',
            'code' => 'international',
            'description' => 'International shipping',
            'countries' => ['IN', 'PK', 'NP', 'LK', 'US', 'UK', 'CA', 'AU'],
            'is_active' => false, // Disabled by default
            'sort_order' => 3,
        ]);

        // Create Delivery Methods
        $standardDelivery = DeliveryMethod::create([
            'name' => 'Standard Delivery',
            'code' => 'standard',
            'description' => 'Regular delivery service',
            'estimated_days' => '3-5 business days',
            'min_days' => 3,
            'max_days' => 5,
            'carrier_name' => 'Sundarban Courier',
            'carrier_code' => 'sundarban',
            'tracking_url' => 'https://sundarban.com.bd/track/{tracking_number}',
            'calculation_type' => 'flat_rate',
            'free_shipping_threshold' => 2000.00,
            'min_order_amount' => null,
            'max_order_amount' => null,
            'max_weight' => 50.00,
            'is_active' => true,
            'show_on_checkout' => true,
            'sort_order' => 1,
        ]);

        $expressDelivery = DeliveryMethod::create([
            'name' => 'Express Delivery',
            'code' => 'express',
            'description' => 'Fast delivery service (1-2 days)',
            'estimated_days' => '1-2 business days',
            'min_days' => 1,
            'max_days' => 2,
            'carrier_name' => 'Pathao',
            'carrier_code' => 'pathao',
            'tracking_url' => 'https://pathao.com/track/{tracking_number}',
            'calculation_type' => 'flat_rate',
            'free_shipping_threshold' => 5000.00,
            'min_order_amount' => 500.00,
            'max_order_amount' => null,
            'max_weight' => 30.00,
            'is_active' => true,
            'show_on_checkout' => true,
            'sort_order' => 2,
        ]);

        $sameDayDelivery = DeliveryMethod::create([
            'name' => 'Same Day Delivery',
            'code' => 'same-day',
            'description' => 'Get your order today (Dhaka only)',
            'estimated_days' => 'Same day',
            'min_days' => 0,
            'max_days' => 0,
            'carrier_name' => 'Pathao',
            'carrier_code' => 'pathao',
            'tracking_url' => 'https://pathao.com/track/{tracking_number}',
            'calculation_type' => 'flat_rate',
            'free_shipping_threshold' => null,
            'min_order_amount' => 1000.00,
            'max_order_amount' => null,
            'max_weight' => 10.00,
            'is_active' => true,
            'show_on_checkout' => true,
            'sort_order' => 3,
        ]);

        $freeShipping = DeliveryMethod::create([
            'name' => 'Free Shipping',
            'code' => 'free',
            'description' => 'Free shipping on orders over 3000 BDT',
            'estimated_days' => '5-7 business days',
            'min_days' => 5,
            'max_days' => 7,
            'carrier_name' => 'SA Paribahan',
            'carrier_code' => 'sa-paribahan',
            'tracking_url' => null,
            'calculation_type' => 'free',
            'free_shipping_threshold' => 3000.00,
            'min_order_amount' => 3000.00,
            'max_order_amount' => null,
            'max_weight' => 100.00,
            'is_active' => true,
            'show_on_checkout' => true,
            'sort_order' => 4,
        ]);

        // Create Delivery Rates for Dhaka City
        DeliveryRate::create([
            'delivery_zone_id' => $dhakaCity->id,
            'delivery_method_id' => $standardDelivery->id,
            'base_rate' => 60.00,
            'handling_fee' => 10.00,
            'insurance_fee' => 5.00,
            'cod_fee' => 20.00,
            'is_active' => true,
        ]);

        DeliveryRate::create([
            'delivery_zone_id' => $dhakaCity->id,
            'delivery_method_id' => $expressDelivery->id,
            'base_rate' => 120.00,
            'handling_fee' => 15.00,
            'insurance_fee' => 10.00,
            'cod_fee' => 25.00,
            'is_active' => true,
        ]);

        DeliveryRate::create([
            'delivery_zone_id' => $dhakaCity->id,
            'delivery_method_id' => $sameDayDelivery->id,
            'base_rate' => 200.00,
            'handling_fee' => 20.00,
            'insurance_fee' => 15.00,
            'cod_fee' => 30.00,
            'is_active' => true,
        ]);

        DeliveryRate::create([
            'delivery_zone_id' => $dhakaCity->id,
            'delivery_method_id' => $freeShipping->id,
            'base_rate' => 0.00,
            'handling_fee' => 0.00,
            'insurance_fee' => 0.00,
            'cod_fee' => 0.00,
            'is_active' => true,
        ]);

        // Create Delivery Rates for Outside Dhaka
        DeliveryRate::create([
            'delivery_zone_id' => $outsideDhaka->id,
            'delivery_method_id' => $standardDelivery->id,
            'base_rate' => 100.00,
            'handling_fee' => 15.00,
            'insurance_fee' => 10.00,
            'cod_fee' => 30.00,
            'is_active' => true,
        ]);

        DeliveryRate::create([
            'delivery_zone_id' => $outsideDhaka->id,
            'delivery_method_id' => $expressDelivery->id,
            'base_rate' => 180.00,
            'handling_fee' => 20.00,
            'insurance_fee' => 15.00,
            'cod_fee' => 35.00,
            'is_active' => true,
        ]);

        DeliveryRate::create([
            'delivery_zone_id' => $outsideDhaka->id,
            'delivery_method_id' => $freeShipping->id,
            'base_rate' => 0.00,
            'handling_fee' => 0.00,
            'insurance_fee' => 0.00,
            'cod_fee' => 0.00,
            'is_active' => true,
        ]);

        // Create Delivery Rates for International (if needed in future)
        DeliveryRate::create([
            'delivery_zone_id' => $international->id,
            'delivery_method_id' => $standardDelivery->id,
            'base_rate' => 1500.00,
            'handling_fee' => 200.00,
            'insurance_fee' => 100.00,
            'cod_fee' => 0.00,
            'is_active' => false,
        ]);

        $this->command->info('✅ Delivery system seeded successfully!');
        $this->command->info('   - 3 Delivery Zones created');
        $this->command->info('   - 4 Delivery Methods created');
        $this->command->info('   - 8 Delivery Rates configured');
    }
}
