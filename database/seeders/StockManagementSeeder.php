<?php

namespace Database\Seeders;

use App\Modules\Stock\Models\Warehouse;
use App\Modules\Stock\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Stock Management Data...');

        // Create Warehouses
        $this->createWarehouses();
        
        // Create Suppliers
        $this->createSuppliers();
        
        $this->command->info('Stock Management Data Seeded Successfully!');
    }

    /**
     * Create warehouses
     */
    private function createWarehouses()
    {
        $warehouses = [
            [
                'name' => 'Main Warehouse',
                'code' => 'WH-001',
                'address' => 'House 123, Road 45, Block A',
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'postal_code' => '1000',
                'country' => 'Bangladesh',
                'phone' => '+880 1711-123456',
                'email' => 'mainwarehouse@example.com',
                'manager_name' => 'John Doe',
                'is_active' => true,
                'is_default' => true,
                'capacity' => 10000,
                'notes' => 'Primary warehouse for all operations',
            ],
            [
                'name' => 'Secondary Warehouse',
                'code' => 'WH-002',
                'address' => 'Plot 67, Section 12',
                'city' => 'Chittagong',
                'state' => 'Chittagong',
                'postal_code' => '4000',
                'country' => 'Bangladesh',
                'phone' => '+880 1711-234567',
                'email' => 'chittagong@example.com',
                'manager_name' => 'Jane Smith',
                'is_active' => true,
                'is_default' => false,
                'capacity' => 5000,
                'notes' => 'Regional warehouse for Chittagong area',
            ],
            [
                'name' => 'Outlet Warehouse',
                'code' => 'WH-003',
                'address' => 'Shop 15, Market Complex',
                'city' => 'Sylhet',
                'state' => 'Sylhet',
                'postal_code' => '3100',
                'country' => 'Bangladesh',
                'phone' => '+880 1711-345678',
                'email' => 'sylhet@example.com',
                'manager_name' => 'Ahmed Hassan',
                'is_active' => true,
                'is_default' => false,
                'capacity' => 2000,
                'notes' => 'Retail outlet warehouse',
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }

        $this->command->info('✓ Created ' . count($warehouses) . ' warehouses');
    }

    /**
     * Create suppliers
     */
    private function createSuppliers()
    {
        $suppliers = [
            [
                'name' => 'Global Trading Co.',
                'code' => 'SUP-001',
                'email' => 'contact@globaltrading.com',
                'phone' => '+880 2-9876543',
                'mobile' => '+880 1911-111111',
                'website' => 'https://globaltrading.com',
                'address' => 'Building 12, Trade Center',
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'postal_code' => '1200',
                'country' => 'Bangladesh',
                'contact_person' => 'Mr. Rahman',
                'contact_person_phone' => '+880 1711-999999',
                'contact_person_email' => 'rahman@globaltrading.com',
                'status' => 'active',
                'credit_limit' => 500000.00,
                'payment_terms' => 30,
                'notes' => 'Main supplier for electronics and gadgets',
            ],
            [
                'name' => 'Wholesale Distributors Ltd.',
                'code' => 'SUP-002',
                'email' => 'info@wholesale.com',
                'phone' => '+880 2-8765432',
                'mobile' => '+880 1922-222222',
                'website' => 'https://wholesale.com',
                'address' => 'Warehouse Complex, Industrial Area',
                'city' => 'Chittagong',
                'state' => 'Chittagong',
                'postal_code' => '4100',
                'country' => 'Bangladesh',
                'contact_person' => 'Mrs. Sultana',
                'contact_person_phone' => '+880 1722-888888',
                'contact_person_email' => 'sultana@wholesale.com',
                'status' => 'active',
                'credit_limit' => 300000.00,
                'payment_terms' => 15,
                'notes' => 'Reliable supplier for household items',
            ],
            [
                'name' => 'Import & Export Inc.',
                'code' => 'SUP-003',
                'email' => 'sales@importexport.com',
                'phone' => '+880 2-7654321',
                'mobile' => '+880 1933-333333',
                'website' => 'https://importexport.com',
                'address' => 'Plot 88, Export Processing Zone',
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'postal_code' => '1350',
                'country' => 'Bangladesh',
                'contact_person' => 'Mr. Karim',
                'contact_person_phone' => '+880 1733-777777',
                'contact_person_email' => 'karim@importexport.com',
                'status' => 'active',
                'credit_limit' => 750000.00,
                'payment_terms' => 45,
                'notes' => 'International supplier for premium products',
            ],
            [
                'name' => 'Local Manufacturers',
                'code' => 'SUP-004',
                'email' => 'orders@localman.com',
                'phone' => '+880 2-6543210',
                'mobile' => '+880 1944-444444',
                'website' => null,
                'address' => 'Factory Road, Industrial Zone',
                'city' => 'Gazipur',
                'state' => 'Dhaka',
                'postal_code' => '1700',
                'country' => 'Bangladesh',
                'contact_person' => 'Mr. Hossain',
                'contact_person_phone' => '+880 1744-666666',
                'contact_person_email' => 'hossain@localman.com',
                'status' => 'active',
                'credit_limit' => 200000.00,
                'payment_terms' => 7,
                'notes' => 'Local manufacturer for custom products',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        $this->command->info('✓ Created ' . count($suppliers) . ' suppliers');
    }
}
