<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Ecommerce\Brand\Models\Brand;
use Illuminate\Support\Str;

/**
 * TrendingBrandSeeder
 * Purpose: Seed trending/featured brands for mega menu
 * 
 * @author AI Assistant
 * @date 2025-11-06
 */
class TrendingBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Candle Shack',
                'slug' => 'candle-shack',
                'description' => 'Premium candle making supplies and fragrances',
                'website' => 'https://candleshack.com',
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Schmidt\'s',
                'slug' => 'schmidts',
                'description' => 'Natural deodorant and personal care products',
                'website' => 'https://schmidts.com',
                'sort_order' => 2,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Cremo',
                'slug' => 'cremo',
                'description' => 'Men\'s grooming and shaving products',
                'website' => 'https://cremocompany.com',
                'sort_order' => 3,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'ArmLactin',
                'slug' => 'armlactin',
                'description' => 'Skin care and moisturizing products',
                'website' => 'https://armlactin.com',
                'sort_order' => 4,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Neutrogena',
                'slug' => 'neutrogena',
                'description' => 'Dermatologist-recommended skin care',
                'website' => 'https://neutrogena.com',
                'sort_order' => 5,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'CeraVe',
                'slug' => 'cerave',
                'description' => 'Developed with dermatologists for healthy skin',
                'website' => 'https://cerave.com',
                'sort_order' => 6,
                'is_active' => true,
                'is_featured' => true,
            ],
            // Additional non-featured brands
            [
                'name' => 'Nature\'s Way',
                'slug' => 'natures-way',
                'description' => 'Premium vitamins and supplements',
                'website' => 'https://naturesway.com',
                'sort_order' => 7,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Garden of Life',
                'slug' => 'garden-of-life',
                'description' => 'Organic vitamins and supplements',
                'website' => 'https://gardenoflife.com',
                'sort_order' => 8,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'NOW Foods',
                'slug' => 'now-foods',
                'description' => 'Natural products and supplements since 1968',
                'website' => 'https://nowfoods.com',
                'sort_order' => 9,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Jarrow Formulas',
                'slug' => 'jarrow-formulas',
                'description' => 'Superior nutrition and formulation',
                'website' => 'https://jarrow.com',
                'sort_order' => 10,
                'is_active' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($brands as $brandData) {
            // Add SEO fields
            $brandData['meta_title'] = $brandData['name'] . ' - Premium Products';
            $brandData['meta_description'] = $brandData['description'];
            $brandData['meta_keywords'] = $brandData['name'] . ', ' . strtolower($brandData['name']) . ', brand';

            Brand::updateOrCreate(
                ['slug' => $brandData['slug']],
                $brandData
            );
        }

        $this->command->info('✅ Trending brands seeded successfully!');
        $this->command->info('📊 Total brands: ' . count($brands));
        $this->command->info('⭐ Featured brands: ' . collect($brands)->where('is_featured', true)->count());
    }
}
