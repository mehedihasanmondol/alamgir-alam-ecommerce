# Delivery/Shipping System Documentation

## Overview
A comprehensive delivery and shipping management system for the Laravel ecommerce platform. This system supports multiple delivery zones, methods, and flexible rate calculations based on weight, price, or item count.

## Features

### ✅ Delivery Zones
- **Geographic Coverage**: Define zones by countries, states, cities, or postal codes
- **Flexible Configuration**: Support for local and international shipping
- **Active/Inactive Status**: Enable/disable zones as needed
- **Sort Order**: Control display order in checkout

### ✅ Delivery Methods
- **Multiple Carriers**: Support for different courier services (Pathao, Sundarban, SA Paribahan, etc.)
- **Calculation Types**:
  - Flat Rate: Fixed shipping cost
  - Weight-Based: Cost calculated by package weight
  - Price-Based: Cost as percentage of order total
  - Item-Based: Cost per item in cart
  - Free Shipping: No charge (with optional threshold)
- **Delivery Time Estimates**: Min/max days or custom text
- **Tracking Integration**: URL templates for tracking numbers
- **Restrictions**: Min/max order amount, max weight limits
- **Free Shipping Threshold**: Auto-apply free shipping above certain amount

### ✅ Delivery Rates
- **Zone-Method Mapping**: Different rates for each zone-method combination
- **Cost Breakdown**:
  - Base Rate
  - Handling Fee
  - Insurance Fee
  - COD (Cash on Delivery) Fee
- **Range-Based Pricing**: Different rates for weight/price/item ranges
- **Active/Inactive Status**: Enable/disable specific rates

### ✅ Order Integration
- **Delivery Information Storage**: Zone, method, and cost details saved with order
- **Delivery Status Tracking**: 
  - Pending → Processing → Picked Up → In Transit → Out for Delivery → Delivered
- **Timestamps**: Track pickup, transit, and delivery times
- **Cost Breakdown**: Separate fields for base cost, fees, and COD charges

## Database Structure

### Tables Created
1. **delivery_zones** - Geographic shipping zones
2. **delivery_methods** - Shipping methods/carriers
3. **delivery_rates** - Pricing configuration for zone-method combinations
4. **orders** (updated) - Added delivery-related fields

## Installation & Setup

### Step 1: Run Migrations
```bash
php artisan migrate
```

This will create:
- `delivery_zones` table
- `delivery_methods` table
- `delivery_rates` table
- Add delivery fields to `orders` table

### Step 2: Seed Sample Data
```bash
php artisan db:seed --class=DeliverySystemSeeder
```

This creates:
- **3 Zones**: Dhaka City, Outside Dhaka, International
- **4 Methods**: Standard, Express, Same Day, Free Shipping
- **8 Rates**: Pre-configured pricing for each zone-method combination

### Step 3: Verify Installation
Check the database tables:
```sql
SELECT * FROM delivery_zones;
SELECT * FROM delivery_methods;
SELECT * FROM delivery_rates;
```

## Usage Examples

### 1. Calculate Shipping Cost

```php
use App\Modules\Ecommerce\Delivery\Services\DeliveryService;

$deliveryService = app(DeliveryService::class);

$result = $deliveryService->calculateShippingCost(
    zoneId: 1,              // Dhaka City
    methodId: 1,            // Standard Delivery
    orderTotal: 1500.00,    // Order subtotal
    orderWeight: 2.5,       // Weight in kg
    itemCount: 3,           // Number of items
    isCod: true             // Cash on Delivery
);

// Result:
[
    'success' => true,
    'cost' => 95.00,
    'breakdown' => [
        'base_rate' => 60.00,
        'handling_fee' => 10.00,
        'insurance_fee' => 5.00,
        'cod_fee' => 20.00,
        'total' => 95.00
    ],
    'is_free' => false,
    'estimated_delivery' => '3-5 business days'
]
```

### 2. Get Available Delivery Options

```php
$options = $deliveryService->getAvailableDeliveryOptions(
    country: 'BD',
    state: 'Dhaka',
    city: 'Dhaka',
    postalCode: '1212',
    orderTotal: 1500.00,
    orderWeight: 2.5,
    itemCount: 3
);

// Result:
[
    'success' => true,
    'zone' => [
        'id' => 1,
        'name' => 'Dhaka City',
        'code' => 'dhaka-city'
    ],
    'options' => [
        [
            'zone_id' => 1,
            'zone_name' => 'Dhaka City',
            'method_id' => 1,
            'method_name' => 'Standard Delivery',
            'method_code' => 'standard',
            'description' => 'Regular delivery service',
            'carrier_name' => 'Sundarban Courier',
            'estimated_delivery' => '3-5 business days',
            'cost' => 95.00,
            'breakdown' => [...],
            'is_free' => false,
            'icon' => null
        ],
        // ... more options
    ]
]
```

### 3. Create Order with Delivery

```php
use App\Modules\Ecommerce\Order\Models\Order;

$order = Order::create([
    'order_number' => 'ORD-20251110-ABC123',
    'user_id' => 1,
    'subtotal' => 1500.00,
    'tax_amount' => 150.00,
    'shipping_cost' => 95.00,
    'total_amount' => 1745.00,
    
    // Delivery information
    'delivery_zone_id' => 1,
    'delivery_method_id' => 1,
    'delivery_zone_name' => 'Dhaka City',
    'delivery_method_name' => 'Standard Delivery',
    'estimated_delivery' => '3-5 business days',
    
    // Cost breakdown
    'base_shipping_cost' => 60.00,
    'handling_fee' => 10.00,
    'insurance_fee' => 5.00,
    'cod_fee' => 20.00,
    
    // Delivery status
    'delivery_status' => 'pending',
    
    // Other fields...
]);
```

### 4. Update Delivery Status

```php
$order = Order::find(1);

// Mark as picked up
$order->update([
    'delivery_status' => 'picked_up',
    'picked_up_at' => now()
]);

// Mark as in transit
$order->update([
    'delivery_status' => 'in_transit',
    'in_transit_at' => now()
]);

// Mark as out for delivery
$order->update([
    'delivery_status' => 'out_for_delivery',
    'out_for_delivery_at' => now()
]);

// Mark as delivered
$order->update([
    'delivery_status' => 'delivered',
    'delivered_at' => now()
]);
```

## Admin Management

### Delivery Zones Management
- Create/Edit/Delete zones
- Define geographic coverage (countries, states, cities, postal codes)
- Enable/disable zones
- Set display order

### Delivery Methods Management
- Create/Edit/Delete methods
- Configure carrier information
- Set calculation type (flat, weight-based, price-based, item-based, free)
- Set delivery time estimates
- Configure restrictions (min/max order amount, max weight)
- Set free shipping threshold
- Add tracking URL template

### Delivery Rates Management
- Create/Edit/Delete rates
- Map zones to methods
- Configure base rate and additional fees
- Set range-based pricing (weight, price, or item ranges)
- Enable/disable specific rates

## Pre-configured Delivery Options

### Dhaka City Zone
1. **Standard Delivery** (3-5 days)
   - Base: 60 BDT
   - Handling: 10 BDT
   - Insurance: 5 BDT
   - COD: 20 BDT
   - **Total: 95 BDT** (with COD)

2. **Express Delivery** (1-2 days)
   - Base: 120 BDT
   - Handling: 15 BDT
   - Insurance: 10 BDT
   - COD: 25 BDT
   - **Total: 170 BDT** (with COD)

3. **Same Day Delivery** (Same day)
   - Base: 200 BDT
   - Handling: 20 BDT
   - Insurance: 15 BDT
   - COD: 30 BDT
   - **Total: 265 BDT** (with COD)
   - Min order: 1000 BDT

4. **Free Shipping** (5-7 days)
   - **Total: 0 BDT**
   - Min order: 3000 BDT

### Outside Dhaka Zone
1. **Standard Delivery** (3-5 days)
   - Base: 100 BDT
   - Handling: 15 BDT
   - Insurance: 10 BDT
   - COD: 30 BDT
   - **Total: 155 BDT** (with COD)

2. **Express Delivery** (1-2 days)
   - Base: 180 BDT
   - Handling: 20 BDT
   - Insurance: 15 BDT
   - COD: 35 BDT
   - **Total: 250 BDT** (with COD)

3. **Free Shipping** (5-7 days)
   - **Total: 0 BDT**
   - Min order: 3000 BDT

## API Reference

### DeliveryService Methods

#### `calculateShippingCost()`
Calculate shipping cost for specific zone and method.

**Parameters:**
- `int $zoneId` - Delivery zone ID
- `int $methodId` - Delivery method ID
- `float $orderTotal` - Order subtotal
- `float $orderWeight` - Total weight in kg (default: 0)
- `int $itemCount` - Number of items (default: 0)
- `bool $isCod` - Cash on Delivery (default: false)

**Returns:** `array`

#### `getAvailableDeliveryOptions()`
Get all available delivery options for a location.

**Parameters:**
- `string $country` - Country code (optional)
- `string $state` - State/division name (optional)
- `string $city` - City name (optional)
- `string $postalCode` - Postal code (optional)
- `float $orderTotal` - Order subtotal (default: 0)
- `float $orderWeight` - Total weight in kg (default: 0)
- `int $itemCount` - Number of items (default: 0)

**Returns:** `array`

#### CRUD Methods
- `createZone(array $data): DeliveryZone`
- `updateZone(int $id, array $data): bool`
- `deleteZone(int $id): bool`
- `createMethod(array $data): DeliveryMethod`
- `updateMethod(int $id, array $data): bool`
- `deleteMethod(int $id): bool`
- `createRate(array $data): DeliveryRate`
- `updateRate(int $id, array $data): bool`
- `deleteRate(int $id): bool`

### DeliveryRepository Methods

- `getActiveZones(): Collection`
- `getActiveMethods(): Collection`
- `getZoneById(int $id): ?DeliveryZone`
- `getMethodById(int $id): ?DeliveryMethod`
- `findZoneByLocation(...): ?DeliveryZone`
- `getMethodsForZone(int $zoneId, ...): Collection`
- `getRate(int $zoneId, int $methodId, ...): ?DeliveryRate`

## Model Relationships

### Order Model
```php
$order->deliveryZone;  // BelongsTo DeliveryZone
$order->deliveryMethod;  // BelongsTo DeliveryMethod
```

### DeliveryZone Model
```php
$zone->rates;  // HasMany DeliveryRate
$zone->activeRates;  // HasMany DeliveryRate (active only)
$zone->availableMethods;  // Attribute - Collection of DeliveryMethod
```

### DeliveryMethod Model
```php
$method->rates;  // HasMany DeliveryRate
$method->activeRates;  // HasMany DeliveryRate (active only)
```

### DeliveryRate Model
```php
$rate->zone;  // BelongsTo DeliveryZone
$rate->method;  // BelongsTo DeliveryMethod
```

## Delivery Status Flow

```
pending → processing → picked_up → in_transit → out_for_delivery → delivered
                                                                   ↓
                                                                failed
                                                                   ↓
                                                               returned
```

## Customization

### Adding New Zones
```php
$zone = DeliveryZone::create([
    'name' => 'Chittagong City',
    'code' => 'chittagong-city',
    'description' => 'Chittagong metropolitan area',
    'countries' => ['BD'],
    'states' => ['Chittagong'],
    'cities' => ['Chittagong'],
    'is_active' => true,
    'sort_order' => 3,
]);
```

### Adding New Methods
```php
$method = DeliveryMethod::create([
    'name' => 'Overnight Delivery',
    'code' => 'overnight',
    'description' => 'Next day delivery',
    'estimated_days' => 'Next day',
    'min_days' => 1,
    'max_days' => 1,
    'carrier_name' => 'RedX',
    'calculation_type' => 'flat_rate',
    'is_active' => true,
    'show_on_checkout' => true,
    'sort_order' => 5,
]);
```

### Adding New Rates
```php
$rate = DeliveryRate::create([
    'delivery_zone_id' => 1,
    'delivery_method_id' => 5,
    'base_rate' => 150.00,
    'handling_fee' => 15.00,
    'insurance_fee' => 10.00,
    'cod_fee' => 25.00,
    'is_active' => true,
]);
```

## Best Practices

1. **Always validate location**: Use `findZoneByLocation()` to ensure zone exists
2. **Check method availability**: Use `isAvailableForOrder()` before showing to customer
3. **Calculate costs dynamically**: Don't hardcode shipping costs
4. **Store delivery details**: Save zone/method names with order for historical reference
5. **Track delivery status**: Update timestamps for each status change
6. **Handle COD separately**: Apply COD fee only when payment method is COD

## Troubleshooting

### No delivery options available
- Check if zones are active
- Verify zone covers the customer's location
- Ensure methods are active and show_on_checkout is true
- Check if order meets method restrictions (min amount, max weight)

### Incorrect shipping cost
- Verify rate configuration for zone-method combination
- Check calculation type matches rate setup
- Ensure weight/price/item ranges are correctly configured

### Free shipping not applying
- Check free_shipping_threshold is set correctly
- Verify order total meets threshold
- Ensure calculation_type is 'free' or threshold is met

## Files Created

### Migrations
- `2025_11_10_070000_create_delivery_zones_table.php`
- `2025_11_10_070100_create_delivery_methods_table.php`
- `2025_11_10_070200_create_delivery_rates_table.php`
- `2025_11_10_070300_add_delivery_fields_to_orders_table.php`

### Models
- `app/Modules/Ecommerce/Delivery/Models/DeliveryZone.php`
- `app/Modules/Ecommerce/Delivery/Models/DeliveryMethod.php`
- `app/Modules/Ecommerce/Delivery/Models/DeliveryRate.php`

### Repositories
- `app/Modules/Ecommerce/Delivery/Repositories/DeliveryRepository.php`

### Services
- `app/Modules/Ecommerce/Delivery/Services/DeliveryService.php`

### Seeders
- `database/seeders/DeliverySystemSeeder.php`

### Updated Files
- `app/Modules/Ecommerce/Order/Models/Order.php` (added delivery relationships)

## Next Steps

1. **Create Admin Controllers** for CRUD operations
2. **Create Admin Views** for zone/method/rate management
3. **Add Routes** for admin delivery management
4. **Update Navigation** to include delivery settings
5. **Integrate with Checkout** to show delivery options
6. **Create Livewire Components** for dynamic delivery selection
7. **Add Tracking Page** for customers to track deliveries
8. **Implement SMS/Email Notifications** for delivery status updates

## Support

For issues or questions, refer to:
- Laravel Documentation: https://laravel.com/docs
- Project .windsurfrules file for coding standards
- editor-task-management.md for implementation progress

---

**Version:** 1.0.0  
**Last Updated:** November 10, 2025  
**Status:** ✅ Core System Complete - Ready for Admin UI Implementation
