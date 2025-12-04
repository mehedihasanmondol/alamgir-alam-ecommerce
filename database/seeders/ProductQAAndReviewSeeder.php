<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductQuestion;
use App\Modules\Ecommerce\Product\Models\ProductAnswer;
use App\Modules\Ecommerce\Product\Models\ProductReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProductQAAndReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Product Q&A and Review Seeding...');

        // Get all published products
        $products = Product::where('status', 'published')->get();
        
        if ($products->isEmpty()) {
            $this->command->warn('No published products found!');
            return;
        }

        $this->command->info("Found {$products->count()} published products");

        // Get all users for random assignment
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found! Creating sample users...');
            $this->createSampleUsers();
            $users = User::all();
        }

        $questionStatuses = ['pending', 'approved', 'rejected'];
        $reviewStatuses = ['pending', 'approved', 'rejected'];
        $ratings = [1, 2, 3, 4, 5];

        foreach ($products as $product) {
            $this->command->info("Seeding Q&A and Reviews for: {$product->name}");

            // Create 20 Questions for each product
            for ($i = 1; $i <= 20; $i++) {
                $user = $users->random();
                $status = $this->getWeightedStatus($questionStatuses);
                
                $question = ProductQuestion::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'question' => $this->generateQuestion($product->name, $i),
                    'status' => $status,
                    'helpful_count' => rand(0, 30),
                    'not_helpful_count' => rand(0, 5),
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now()->subDays(rand(1, 60)),
                ]);

                // Add 1-3 answers to approved questions
                if ($status === 'approved' && rand(1, 100) > 30) {
                    $answerCount = rand(1, 3);
                    for ($j = 1; $j <= $answerCount; $j++) {
                        $answerer = $users->random();
                        $answerStatus = $this->getWeightedStatus(['pending', 'approved']);
                        
                        ProductAnswer::create([
                            'question_id' => $question->id,
                            'user_id' => $answerer->id,
                            'answer' => $this->generateAnswer($j),
                            'is_best_answer' => $j === 1 && $answerStatus === 'approved' && rand(1, 100) > 70,
                            'is_verified_purchase' => rand(1, 100) > 40,
                            'is_rewarded' => false,
                            'status' => $answerStatus,
                            'helpful_count' => rand(0, 20),
                            'not_helpful_count' => rand(0, 3),
                            'created_at' => now()->subDays(rand(1, 50)),
                            'updated_at' => now()->subDays(rand(1, 50)),
                        ]);
                    }
                }
            }

            // Create 20 Reviews for each product
            for ($i = 1; $i <= 20; $i++) {
                $user = $users->random();
                $status = $this->getWeightedStatus($reviewStatuses);
                $rating = $this->getWeightedRating($ratings);
                $isVerified = rand(1, 100) > 30; // 70% verified purchases
                
                ProductReview::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'title' => $this->generateReviewTitle($rating, $i),
                    'review' => $this->generateReview($product->name, $rating, $i),
                    'images' => rand(1, 100) > 70 ? $this->generateReviewImages() : null,
                    'is_verified_purchase' => $isVerified,
                    'status' => $status,
                    'helpful_count' => rand(0, 50),
                    'not_helpful_count' => rand(0, 10),
                    'approved_at' => $status === 'approved' ? now()->subDays(rand(1, 30)) : null,
                    'approved_by' => $status === 'approved' ? 1 : null,
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now()->subDays(rand(1, 60)),
                ]);
            }

            $this->command->info("✓ Created 20 Q&A and 20 Reviews for {$product->name}");
        }

        $this->command->info('✓ Product Q&A and Review Seeding Completed!');
    }

    /**
     * Get weighted status (more approved than pending/rejected)
     */
    private function getWeightedStatus(array $statuses): string
    {
        $rand = rand(1, 100);
        
        if ($rand <= 70) {
            return 'approved'; // 70% approved
        } elseif ($rand <= 90) {
            return 'pending'; // 20% pending
        } else {
            return 'rejected'; // 10% rejected
        }
    }

    /**
     * Get weighted rating (more 4-5 stars)
     */
    private function getWeightedRating(array $ratings): int
    {
        $rand = rand(1, 100);
        
        if ($rand <= 40) {
            return 5; // 40% five stars
        } elseif ($rand <= 70) {
            return 4; // 30% four stars
        } elseif ($rand <= 85) {
            return 3; // 15% three stars
        } elseif ($rand <= 95) {
            return 2; // 10% two stars
        } else {
            return 1; // 5% one star
        }
    }

    /**
     * Generate realistic question
     */
    private function generateQuestion(string $productName, int $index): string
    {
        $questions = [
            "What is the warranty period for this {$productName}?",
            "Does this {$productName} come with a user manual?",
            "Is this {$productName} compatible with other brands?",
            "What are the dimensions of this {$productName}?",
            "How long does shipping take for this {$productName}?",
            "Is this {$productName} suitable for beginners?",
            "What materials is this {$productName} made from?",
            "Can I return this {$productName} if it doesn't fit?",
            "Does this {$productName} require assembly?",
            "Is there a discount available for bulk orders of this {$productName}?",
            "What colors are available for this {$productName}?",
            "Is this {$productName} water-resistant?",
            "How do I clean and maintain this {$productName}?",
            "Does this {$productName} come with batteries included?",
            "What is the weight of this {$productName}?",
            "Is this {$productName} eco-friendly?",
            "Can I use this {$productName} outdoors?",
            "What is the maximum capacity of this {$productName}?",
            "Is this {$productName} dishwasher safe?",
            "Does this {$productName} have a money-back guarantee?",
        ];

        return $questions[$index % count($questions)];
    }

    /**
     * Generate realistic answer
     */
    private function generateAnswer(int $index): string
    {
        $answers = [
            "Yes, this product comes with a 1-year manufacturer warranty covering defects in materials and workmanship.",
            "Absolutely! A comprehensive user manual is included in the package with detailed instructions.",
            "Yes, it's designed to be compatible with most standard brands and accessories.",
            "Great question! The dimensions are listed in the product specifications section above.",
            "Standard shipping typically takes 3-5 business days, while express shipping is 1-2 days.",
            "Yes, this is perfect for beginners! It's user-friendly and comes with easy-to-follow instructions.",
            "It's made from high-quality, durable materials that are built to last.",
            "Yes, we offer a 30-day return policy for unused items in original packaging.",
            "Minimal assembly required - usually takes about 10-15 minutes with basic tools.",
            "Yes, please contact our sales team for bulk order discounts and special pricing.",
            "Currently available in multiple colors - check the color selector above for options.",
            "Yes, it features water-resistant coating suitable for light rain and splashes.",
            "Simply wipe with a damp cloth. Avoid harsh chemicals or abrasive cleaners.",
            "Yes, batteries are included so you can start using it right away!",
            "The product weighs approximately as specified in the technical details section.",
            "Yes, it's made from eco-friendly and sustainable materials.",
            "Absolutely! It's designed for both indoor and outdoor use.",
            "The maximum capacity is clearly mentioned in the product specifications.",
            "Yes, it's dishwasher safe on the top rack for easy cleaning.",
            "Yes, we offer a 100% satisfaction guarantee with hassle-free returns.",
        ];

        return $answers[$index % count($answers)];
    }

    /**
     * Generate realistic review title
     */
    private function generateReviewTitle(int $rating, int $index): string
    {
        $titles = [
            5 => [
                "Excellent product! Highly recommended!",
                "Best purchase I've made this year!",
                "Outstanding quality and value!",
                "Exceeded my expectations!",
                "Perfect! Exactly what I needed!",
            ],
            4 => [
                "Very good product, minor issues",
                "Great quality, worth the price",
                "Solid product with good features",
                "Happy with my purchase overall",
                "Good value for money",
            ],
            3 => [
                "Decent product, meets expectations",
                "Average quality, nothing special",
                "It's okay, does the job",
                "Mixed feelings about this",
                "Good but could be better",
            ],
            2 => [
                "Disappointed with the quality",
                "Not as described, below expectations",
                "Several issues with this product",
                "Expected better for the price",
                "Mediocre quality, not impressed",
            ],
            1 => [
                "Very poor quality, do not buy!",
                "Waste of money, terrible product",
                "Completely unsatisfied",
                "Defective product, requesting refund",
                "Worst purchase ever made",
            ],
        ];

        $ratingTitles = $titles[$rating];
        return $ratingTitles[$index % count($ratingTitles)];
    }

    /**
     * Generate realistic review content
     */
    private function generateReview(string $productName, int $rating, int $index): string
    {
        $positiveReviews = [
            "I recently purchased this {$productName} and I'm absolutely thrilled with it! The quality is exceptional and it works exactly as described. The packaging was secure and delivery was prompt. I've been using it daily and it has exceeded all my expectations. Highly recommend to anyone looking for a reliable product!",
            "This {$productName} is fantastic! I did a lot of research before buying and I'm so glad I chose this one. The build quality is solid, the features are great, and it's very easy to use. Customer service was also excellent when I had a question. Worth every penny!",
            "Absolutely love this {$productName}! It arrived quickly and was well-packaged. The quality is top-notch and it's exactly what I was looking for. I've recommended it to several friends already. Five stars all the way!",
        ];

        $neutralReviews = [
            "This {$productName} is decent. It does what it's supposed to do, but nothing extraordinary. The quality is acceptable for the price point. There are a few minor issues but nothing deal-breaking. Overall, it's an okay purchase.",
            "The {$productName} works as advertised. Quality is average, not the best but not the worst either. It gets the job done. I think there are probably better options out there, but this one is fine if you're on a budget.",
        ];

        $negativeReviews = [
            "I'm quite disappointed with this {$productName}. The quality is not as good as I expected based on the description and reviews. It feels cheaply made and doesn't work as well as advertised. I'm considering returning it.",
            "Not happy with this {$productName} at all. The product arrived with some defects and the quality is subpar. It doesn't match the description and photos. I expected much better for this price. Would not recommend.",
        ];

        if ($rating >= 4) {
            $reviews = $positiveReviews;
        } elseif ($rating === 3) {
            $reviews = $neutralReviews;
        } else {
            $reviews = $negativeReviews;
        }

        $review = $reviews[$index % count($reviews)];
        return str_replace('{$productName}', $productName, $review);
    }

    /**
     * Generate fake review images (empty array for now)
     */
    private function generateReviewImages(): array
    {
        // Return empty array as we don't have actual images
        // In production, you would upload real images
        return [];
    }

    /**
     * Create sample users if none exist
     */
    private function createSampleUsers(): void
    {
        $this->command->info('Creating sample users...');
        
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "Customer {$i}",
                'email' => "customer{$i}@example.com",
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }
        
        $this->command->info('✓ Created 10 sample users');
    }
}
