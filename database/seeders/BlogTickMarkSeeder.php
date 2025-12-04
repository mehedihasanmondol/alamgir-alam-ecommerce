<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogTickMarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickMarks = [
            [
                'name' => 'Verified',
                'slug' => 'verified',
                'label' => 'Verified',
                'description' => 'Content has been verified and is trustworthy',
                'icon' => 'check-circle',
                'color' => 'blue',
                'bg_color' => 'bg-blue-500',
                'text_color' => 'text-white',
                'is_system' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Editor\'s Choice',
                'slug' => 'editor-choice',
                'label' => 'Editor\'s Choice',
                'description' => 'Featured by our editorial team',
                'icon' => 'star',
                'color' => 'purple',
                'bg_color' => 'bg-purple-500',
                'text_color' => 'text-white',
                'is_system' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Trending',
                'slug' => 'trending',
                'label' => 'Trending',
                'description' => 'Currently trending content',
                'icon' => 'trending-up',
                'color' => 'red',
                'bg_color' => 'bg-red-500',
                'text_color' => 'text-white',
                'is_system' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'label' => 'Premium',
                'description' => 'Premium content for subscribers',
                'icon' => 'crown',
                'color' => 'yellow',
                'bg_color' => 'bg-yellow-500',
                'text_color' => 'text-white',
                'is_system' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Hot',
                'slug' => 'hot',
                'label' => 'Hot',
                'description' => 'Hot and popular content',
                'icon' => 'flame',
                'color' => 'orange',
                'bg_color' => 'bg-orange-500',
                'text_color' => 'text-white',
                'is_system' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'New',
                'slug' => 'new',
                'label' => 'New',
                'description' => 'Recently published content',
                'icon' => 'sparkles',
                'color' => 'green',
                'bg_color' => 'bg-green-500',
                'text_color' => 'text-white',
                'is_system' => false,
                'sort_order' => 6,
            ],
        ];

        foreach ($tickMarks as $tickMark) {
            DB::table('blog_tick_marks')->insert([
                'name' => $tickMark['name'],
                'slug' => $tickMark['slug'],
                'label' => $tickMark['label'],
                'description' => $tickMark['description'],
                'icon' => $tickMark['icon'],
                'color' => $tickMark['color'],
                'bg_color' => $tickMark['bg_color'],
                'text_color' => $tickMark['text_color'],
                'is_active' => true,
                'is_system' => $tickMark['is_system'],
                'sort_order' => $tickMark['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
