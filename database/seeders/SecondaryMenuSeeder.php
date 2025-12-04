<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SecondaryMenuItem;

/**
 * SecondaryMenuSeeder
 * Purpose: Seed default secondary menu items
 * 
 * @author AI Assistant
 * @date 2025-11-06
 */
class SecondaryMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menuItems = [
            [
                'label' => 'Sale Offers',
                'url' => '/sale',
                'color' => 'text-red-600',
                'type' => 'link',
                'sort_order' => 1,
                'is_active' => true,
                'open_new_tab' => false,
            ],
            [
                'label' => 'Best Sellers',
                'url' => '/best-sellers',
                'color' => 'text-gray-700',
                'type' => 'link',
                'sort_order' => 2,
                'is_active' => true,
                'open_new_tab' => false,
            ],
            [
                'label' => 'Try',
                'url' => '/try',
                'color' => 'text-gray-700',
                'type' => 'link',
                'sort_order' => 3,
                'is_active' => true,
                'open_new_tab' => false,
            ],
            [
                'label' => 'New',
                'url' => '/new-arrivals',
                'color' => 'text-gray-700',
                'type' => 'link',
                'sort_order' => 4,
                'is_active' => true,
                'open_new_tab' => false,
            ],
            [
                'label' => 'More',
                'url' => '#',
                'color' => 'text-gray-700',
                'type' => 'dropdown',
                'sort_order' => 5,
                'is_active' => true,
                'open_new_tab' => false,
            ],
        ];

        foreach ($menuItems as $item) {
            SecondaryMenuItem::updateOrCreate(
                ['label' => $item['label']],
                $item
            );
        }

        $this->command->info('✅ Secondary menu items seeded successfully!');
        $this->command->info('📊 Total items: ' . count($menuItems));
    }
}
