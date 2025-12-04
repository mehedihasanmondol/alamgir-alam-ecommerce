<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Modules\Ecommerce\Category\Models\Category;
use Illuminate\Support\Str;

/**
 * Health Product Categories Seeder
 * 
 * Creates realistic health product categories and subcategories
 * Based on iHerb-style health and wellness store structure
 */
class HealthCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // 1. Supplements
            [
                'name' => 'Supplements',
                'description' => 'Vitamins, minerals, and dietary supplements for optimal health',
                'is_active' => true,
                'sort_order' => 1,
                'meta_title' => 'Health Supplements - Vitamins & Minerals',
                'meta_description' => 'Shop high-quality supplements, vitamins, and minerals for your health needs.',
                'children' => [
                    ['name' => 'Vitamins', 'description' => 'Essential vitamins for daily health', 'sort_order' => 1],
                    ['name' => 'Minerals', 'description' => 'Important minerals and trace elements', 'sort_order' => 2],
                    ['name' => 'Multivitamins', 'description' => 'Complete multivitamin formulas', 'sort_order' => 3],
                    ['name' => 'Omega-3 & Fish Oil', 'description' => 'Essential fatty acids and fish oil supplements', 'sort_order' => 4],
                    ['name' => 'Probiotics', 'description' => 'Digestive health and gut flora support', 'sort_order' => 5],
                    ['name' => 'Protein Supplements', 'description' => 'Protein powders and supplements', 'sort_order' => 6],
                    ['name' => 'Herbal Supplements', 'description' => 'Natural herbal remedies and extracts', 'sort_order' => 7],
                    ['name' => 'Amino Acids', 'description' => 'Essential and non-essential amino acids', 'sort_order' => 8],
                ]
            ],

            // 2. Sports Nutrition
            [
                'name' => 'Sports Nutrition',
                'description' => 'Performance supplements and nutrition for athletes',
                'is_active' => true,
                'sort_order' => 2,
                'meta_title' => 'Sports Nutrition - Performance Supplements',
                'meta_description' => 'Premium sports nutrition products for athletes and fitness enthusiasts.',
                'children' => [
                    ['name' => 'Pre-Workout', 'description' => 'Energy and focus boosters for workouts', 'sort_order' => 1],
                    ['name' => 'Post-Workout', 'description' => 'Recovery and muscle repair supplements', 'sort_order' => 2],
                    ['name' => 'Protein Powder', 'description' => 'Whey, casein, and plant-based proteins', 'sort_order' => 3],
                    ['name' => 'Creatine', 'description' => 'Muscle strength and performance', 'sort_order' => 4],
                    ['name' => 'BCAAs', 'description' => 'Branched-chain amino acids for muscle recovery', 'sort_order' => 5],
                    ['name' => 'Energy Drinks', 'description' => 'Sports drinks and energy boosters', 'sort_order' => 6],
                    ['name' => 'Weight Gainers', 'description' => 'Mass and weight gain supplements', 'sort_order' => 7],
                    ['name' => 'Fat Burners', 'description' => 'Weight loss and metabolism support', 'sort_order' => 8],
                ]
            ],

            // 3. Beauty & Personal Care
            [
                'name' => 'Beauty',
                'description' => 'Natural beauty and skincare products',
                'is_active' => true,
                'sort_order' => 3,
                'meta_title' => 'Beauty & Skincare - Natural Products',
                'meta_description' => 'Discover natural beauty and skincare products for healthy, glowing skin.',
                'children' => [
                    ['name' => 'Skincare', 'description' => 'Face and body skincare products', 'sort_order' => 1],
                    ['name' => 'Hair Care', 'description' => 'Shampoos, conditioners, and treatments', 'sort_order' => 2],
                    ['name' => 'Makeup', 'description' => 'Natural and organic makeup products', 'sort_order' => 3],
                    ['name' => 'Bath & Shower', 'description' => 'Body washes, soaps, and bath products', 'sort_order' => 4],
                    ['name' => 'Oral Care', 'description' => 'Toothpaste, mouthwash, and dental care', 'sort_order' => 5],
                    ['name' => 'Deodorants', 'description' => 'Natural deodorants and antiperspirants', 'sort_order' => 6],
                    ['name' => 'Sun Care', 'description' => 'Sunscreen and sun protection products', 'sort_order' => 7],
                    ['name' => 'Anti-Aging', 'description' => 'Anti-aging serums and treatments', 'sort_order' => 8],
                ]
            ],

            // 4. Grocery & Food
            [
                'name' => 'Grocery',
                'description' => 'Healthy foods, snacks, and beverages',
                'is_active' => true,
                'sort_order' => 4,
                'meta_title' => 'Healthy Grocery - Organic Foods & Snacks',
                'meta_description' => 'Shop organic foods, healthy snacks, and natural beverages.',
                'children' => [
                    ['name' => 'Organic Foods', 'description' => 'Certified organic food products', 'sort_order' => 1],
                    ['name' => 'Snacks', 'description' => 'Healthy snacks and treats', 'sort_order' => 2],
                    ['name' => 'Beverages', 'description' => 'Teas, coffees, and healthy drinks', 'sort_order' => 3],
                    ['name' => 'Protein Bars', 'description' => 'Nutritious protein and energy bars', 'sort_order' => 4],
                    ['name' => 'Nuts & Seeds', 'description' => 'Raw and roasted nuts and seeds', 'sort_order' => 5],
                    ['name' => 'Superfoods', 'description' => 'Nutrient-dense superfoods', 'sort_order' => 6],
                    ['name' => 'Cooking Oils', 'description' => 'Healthy cooking and salad oils', 'sort_order' => 7],
                    ['name' => 'Sweeteners', 'description' => 'Natural and alternative sweeteners', 'sort_order' => 8],
                ]
            ],

            // 5. Home & Lifestyle
            [
                'name' => 'Home',
                'description' => 'Home essentials and lifestyle products',
                'is_active' => true,
                'sort_order' => 5,
                'meta_title' => 'Home & Lifestyle - Natural Products',
                'meta_description' => 'Natural home care and lifestyle products for healthy living.',
                'children' => [
                    ['name' => 'Cleaning Products', 'description' => 'Natural and eco-friendly cleaners', 'sort_order' => 1],
                    ['name' => 'Aromatherapy', 'description' => 'Essential oils and diffusers', 'sort_order' => 2],
                    ['name' => 'Air Fresheners', 'description' => 'Natural air fresheners and purifiers', 'sort_order' => 3],
                    ['name' => 'Laundry Care', 'description' => 'Natural laundry detergents and softeners', 'sort_order' => 4],
                    ['name' => 'Kitchen Essentials', 'description' => 'Eco-friendly kitchen products', 'sort_order' => 5],
                    ['name' => 'Storage & Organization', 'description' => 'Home storage solutions', 'sort_order' => 6],
                ]
            ],

            // 6. Baby & Kids
            [
                'name' => 'Baby',
                'description' => 'Natural products for babies and children',
                'is_active' => true,
                'sort_order' => 6,
                'meta_title' => 'Baby & Kids - Natural Baby Products',
                'meta_description' => 'Safe, natural products for babies and children.',
                'children' => [
                    ['name' => 'Baby Food', 'description' => 'Organic baby food and formula', 'sort_order' => 1],
                    ['name' => 'Baby Care', 'description' => 'Diapers, wipes, and baby care products', 'sort_order' => 2],
                    ['name' => 'Baby Bath', 'description' => 'Gentle bath products for babies', 'sort_order' => 3],
                    ['name' => 'Kids Vitamins', 'description' => 'Vitamins and supplements for children', 'sort_order' => 4],
                    ['name' => 'Baby Skincare', 'description' => 'Lotions, creams, and skincare for babies', 'sort_order' => 5],
                    ['name' => 'Nursing & Feeding', 'description' => 'Bottles, nursing pads, and feeding accessories', 'sort_order' => 6],
                ]
            ],

            // 7. Pets
            [
                'name' => 'Pets',
                'description' => 'Natural products for pet health and wellness',
                'is_active' => true,
                'sort_order' => 7,
                'meta_title' => 'Pet Products - Natural Pet Care',
                'meta_description' => 'Natural and healthy products for your pets.',
                'children' => [
                    ['name' => 'Pet Supplements', 'description' => 'Vitamins and supplements for pets', 'sort_order' => 1],
                    ['name' => 'Pet Food', 'description' => 'Natural and organic pet food', 'sort_order' => 2],
                    ['name' => 'Pet Treats', 'description' => 'Healthy treats and snacks for pets', 'sort_order' => 3],
                    ['name' => 'Pet Grooming', 'description' => 'Shampoos and grooming products', 'sort_order' => 4],
                    ['name' => 'Pet Care', 'description' => 'Health and wellness products for pets', 'sort_order' => 5],
                ]
            ],

            // 8. Health Goals
            [
                'name' => 'Health Goals',
                'description' => 'Products organized by health objectives',
                'is_active' => true,
                'sort_order' => 8,
                'meta_title' => 'Health Goals - Targeted Wellness Solutions',
                'meta_description' => 'Find products for your specific health goals and wellness objectives.',
                'children' => [
                    ['name' => 'Weight Management', 'description' => 'Products for healthy weight control', 'sort_order' => 1],
                    ['name' => 'Immune Support', 'description' => 'Boost your immune system naturally', 'sort_order' => 2],
                    ['name' => 'Heart Health', 'description' => 'Cardiovascular health support', 'sort_order' => 3],
                    ['name' => 'Digestive Health', 'description' => 'Gut health and digestion support', 'sort_order' => 4],
                    ['name' => 'Joint & Bone Health', 'description' => 'Support for joints and bones', 'sort_order' => 5],
                    ['name' => 'Brain & Memory', 'description' => 'Cognitive function and memory support', 'sort_order' => 6],
                    ['name' => 'Sleep Support', 'description' => 'Natural sleep aids and relaxation', 'sort_order' => 7],
                    ['name' => 'Energy & Vitality', 'description' => 'Boost energy and vitality naturally', 'sort_order' => 8],
                ]
            ],
        ];

        // Create categories and subcategories
        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            // Generate slug
            $categoryData['slug'] = Str::slug($categoryData['name']);

            // Create parent category
            $category = Category::create($categoryData);

            echo "✓ Created category: {$category->name}\n";

            // Create subcategories
            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                $childData['slug'] = Str::slug($childData['name']);
                $childData['is_active'] = true;
                $childData['meta_title'] = $childData['name'] . ' - ' . $category->name;
                $childData['meta_description'] = $childData['description'];

                $child = Category::create($childData);
                echo "  ✓ Created subcategory: {$child->name}\n";
            }
        }

        echo "\n✅ Successfully created " . Category::count() . " categories!\n";
    }
}
