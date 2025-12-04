<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductReview;
use App\Models\User;

/**
 * ModuleName: Product Review Seeder
 * Purpose: Seed sample product reviews for testing
 * 
 * @category Database
 * @package  Seeders
 * @author   Windsurf AI
 * @created  2025-11-08
 */
class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first 5 products
        $products = Product::take(5)->get();
        
        // Get first user or create one
        $user = User::first();

        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $reviewTemplates = [
            [
                'rating' => 5,
                'title' => 'Excellent Product!',
                'review' => 'This product exceeded my expectations. The quality is outstanding and it works exactly as described. Highly recommend to anyone looking for a reliable solution.',
                'is_verified_purchase' => true,
            ],
            [
                'rating' => 4,
                'title' => 'Very Good Quality',
                'review' => 'Great product overall. The build quality is solid and it performs well. Only minor issue is the packaging could be better, but the product itself is fantastic.',
                'is_verified_purchase' => true,
            ],
            [
                'rating' => 5,
                'title' => 'Best Purchase Ever!',
                'review' => 'I am extremely satisfied with this purchase. The product arrived quickly and was exactly what I needed. Customer service was also very helpful. Will definitely buy again!',
                'is_verified_purchase' => true,
            ],
            [
                'rating' => 3,
                'title' => 'Good but could be better',
                'review' => 'The product is decent for the price. It does what it\'s supposed to do, but there are some areas that could use improvement. Still, it\'s a reasonable purchase.',
                'is_verified_purchase' => false,
            ],
            [
                'rating' => 5,
                'title' => 'Amazing Value',
                'review' => 'For the price point, this is an incredible deal. The quality is much better than I expected and it has all the features I was looking for. Very happy with this purchase!',
                'is_verified_purchase' => true,
            ],
            [
                'rating' => 4,
                'title' => 'Solid Product',
                'review' => 'This is a well-made product that serves its purpose perfectly. The design is sleek and modern. My only complaint is that it took a bit longer to arrive than expected.',
                'is_verified_purchase' => true,
            ],
            [
                'rating' => 5,
                'title' => 'Highly Recommended',
                'review' => 'I\'ve been using this product for a few weeks now and it has been fantastic. The performance is excellent and it\'s very easy to use. Would definitely recommend to friends and family.',
                'is_verified_purchase' => true,
            ],
            [
                'rating' => 2,
                'title' => 'Not What I Expected',
                'review' => 'Unfortunately, this product didn\'t meet my expectations. The quality is okay but not great. I think there are better alternatives available for a similar price.',
                'is_verified_purchase' => false,
            ],
        ];

        foreach ($products as $product) {
            // Create 3-5 reviews per product
            $reviewCount = rand(3, 5);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                $template = $reviewTemplates[array_rand($reviewTemplates)];
                
                ProductReview::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'rating' => $template['rating'],
                    'title' => $template['title'],
                    'review' => $template['review'],
                    'is_verified_purchase' => $template['is_verified_purchase'],
                    'status' => 'approved',
                    'helpful_count' => rand(0, 25),
                    'not_helpful_count' => rand(0, 5),
                    'approved_at' => now(),
                    'approved_by' => $user->id,
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }

            // Update product rating statistics
            $avgRating = ProductReview::where('product_id', $product->id)
                ->where('status', 'approved')
                ->avg('rating');
            
            $reviewCount = ProductReview::where('product_id', $product->id)
                ->where('status', 'approved')
                ->count();

            $product->update([
                'average_rating' => round($avgRating, 2),
                'review_count' => $reviewCount,
            ]);
        }

        $this->command->info('Product reviews seeded successfully!');
    }
}
