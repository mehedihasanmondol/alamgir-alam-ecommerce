<?php

namespace Database\Seeders;

use App\Models\HeroSlider;
use Illuminate\Database\Seeder;

class HeroSliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Only creates new sliders or updates existing ones if data differs (excludes timestamps).
     */
    public function run(): void
    {
        // Hero Sliders
        $sliders = [
            [
                'title' => 'Up to 70% off',
                'subtitle' => 'iHerb Brands',
                'image' => 'https://via.placeholder.com/1920x400/1e3a8a/ffffff?text=Nutricost+-+Take+control+of+your+health',
                'link' => '/shop',
                'button_text' => 'Shop Now',
                'order' => 0,
                'is_active' => true,
            ],
            [
                'title' => 'Trusted Brands',
                'subtitle' => 'Up to 20% off',
                'image' => 'https://via.placeholder.com/1920x400/059669/ffffff?text=Trusted+Brands+-+Shop+Now',
                'link' => '/shop',
                'button_text' => 'Explore',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Wellness Hub',
                'subtitle' => 'Learn More',
                'image' => 'https://via.placeholder.com/1920x400/7c3aed/ffffff?text=Wellness+Hub+-+Discover+Health',
                'link' => '/about',
                'button_text' => 'Learn More',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => '20 High-Fibre Foods',
                'subtitle' => 'Find out more',
                'image' => 'https://via.placeholder.com/1920x400/dc2626/ffffff?text=High+Fibre+Foods+-+Nutrition+Guide',
                'link' => '/shop',
                'button_text' => 'View Guide',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Nutricost',
                'subtitle' => 'Shop now',
                'image' => 'https://via.placeholder.com/1920x400/ea580c/ffffff?text=Nutricost+-+Premium+Supplements',
                'link' => '/shop',
                'button_text' => 'Shop Now',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            $this->upsertHeroSlider($slider);
        }

        $this->command->info('Hero sliders seeded successfully!');
    }

    /**
     * Smart upsert for hero sliders: Only create or update if data differs (excludes timestamps)
     */
    private function upsertHeroSlider(array $sliderData): void
    {
        $existing = HeroSlider::where('title', $sliderData['title'])->first();

        if (!$existing) {
            // Slider doesn't exist, create it
            HeroSlider::create($sliderData);
            $this->command->info("Created hero slider: {$sliderData['title']}");
        } else {
            // Slider exists, check if any data differs (exclude title and timestamps)
            $excludeFields = ['title', 'created_at', 'updated_at'];
            $needsUpdate = false;
            $updates = [];

            foreach ($sliderData as $field => $newValue) {
                if (in_array($field, $excludeFields)) continue;
                
                $oldValue = $existing->{$field};
                
                // Compare data fields (handle null and boolean comparisons)
                if ($oldValue != $newValue) {
                    $needsUpdate = true;
                    $updates[$field] = $newValue;
                }
            }

            if ($needsUpdate) {
                $existing->update($updates);
                $this->command->info("Updated hero slider: {$sliderData['title']}");
            }
        }
    }
}
