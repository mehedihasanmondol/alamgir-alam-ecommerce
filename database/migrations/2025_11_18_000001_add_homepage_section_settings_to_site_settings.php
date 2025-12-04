<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert settings for each homepage section
        $settings = [
            // New Arrivals Section - Managed in New Arrival Products page only
            [
                'key' => 'new_arrivals_section_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'internal_section_control',
                'label' => 'Enable New Arrivals Section',
                'description' => 'Managed from New Arrival Products page',
                'order' => 1,
            ],
            [
                'key' => 'new_arrivals_section_title',
                'value' => 'New Arrivals',
                'type' => 'text',
                'group' => 'internal_section_control',
                'label' => 'New Arrivals Section Title',
                'description' => 'Managed from New Arrival Products page',
                'order' => 2,
            ],
            
            // Best Sellers Section - Managed in Best Sellers page only
            [
                'key' => 'best_sellers_section_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'internal_section_control',
                'label' => 'Enable Best Sellers Section',
                'description' => 'Managed from Best Seller Products page',
                'order' => 3,
            ],
            [
                'key' => 'best_sellers_section_title',
                'value' => 'Best Sellers',
                'type' => 'text',
                'group' => 'internal_section_control',
                'label' => 'Best Sellers Section Title',
                'description' => 'Managed from Best Seller Products page',
                'order' => 4,
            ],
            
            // Trending Products Section - Managed in Trending Products page only
            [
                'key' => 'trending_section_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'internal_section_control',
                'label' => 'Enable Trending Section',
                'description' => 'Managed from Trending Products page',
                'order' => 5,
            ],
            [
                'key' => 'trending_section_title',
                'value' => 'Trending Now',
                'type' => 'text',
                'group' => 'internal_section_control',
                'label' => 'Trending Section Title',
                'description' => 'Managed from Trending Products page',
                'order' => 6,
            ],
            
            // Sale Offers Section - Managed in Sale Offers page only
            [
                'key' => 'sale_offers_section_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'internal_section_control',
                'label' => 'Enable Sale Offers Section',
                'description' => 'Managed from Sale Offers page',
                'order' => 7,
            ],
            [
                'key' => 'sale_offers_section_title',
                'value' => 'Sale Offers',
                'type' => 'text',
                'group' => 'internal_section_control',
                'label' => 'Sale Offers Section Title',
                'description' => 'Managed from Sale Offers page',
                'order' => 8,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', [
            'new_arrivals_section_enabled',
            'new_arrivals_section_title',
            'best_sellers_section_enabled',
            'best_sellers_section_title',
            'trending_section_enabled',
            'trending_section_title',
            'sale_offers_section_enabled',
            'sale_offers_section_title',
        ])->delete();
    }
};
