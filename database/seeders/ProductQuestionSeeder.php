<?php

namespace Database\Seeders;

use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductQuestion;
use App\Modules\Ecommerce\Product\Models\ProductAnswer;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::take(10)->get();
        $users = User::take(5)->get();

        if ($products->isEmpty()) {
            $this->command->error('No products found. Please seed products first.');
            return;
        }

        $questions = [
            'What is the warranty period for this product?',
            'Is this product available in different colors?',
            'What are the dimensions of this item?',
            'Does this come with free shipping?',
            'Is this product suitable for outdoor use?',
            'What materials is this made from?',
            'How long does delivery usually take?',
            'Can I return this if it doesn\'t fit?',
            'Is this product compatible with other brands?',
            'What is the weight of this product?',
            'Does this require assembly?',
            'Is there a user manual included?',
            'What is the power consumption?',
            'Can this be used internationally?',
            'Is this product eco-friendly?',
            'What certifications does this have?',
            'How do I clean and maintain this?',
            'Is there a bulk discount available?',
            'What is the expected lifespan?',
            'Are replacement parts available?',
        ];

        $answers = [
            'Yes, this product comes with a 1-year warranty covering manufacturing defects.',
            'Currently, this is available in 3 different colors: Black, White, and Blue.',
            'The dimensions are 30cm x 20cm x 15cm (L x W x H).',
            'Yes, we offer free shipping on orders over $50.',
            'Yes, this product is designed for both indoor and outdoor use.',
            'This is made from high-quality durable plastic and stainless steel.',
            'Standard delivery takes 3-5 business days.',
            'Yes, we have a 30-day return policy for unused items.',
            'Yes, this is compatible with most major brands.',
            'The product weighs approximately 2.5 kg.',
            'Minimal assembly required, takes about 10 minutes.',
            'Yes, a detailed user manual is included in the package.',
            'Power consumption is approximately 50W per hour.',
            'Yes, it works with 110-240V, suitable for international use.',
            'Yes, this product is made from recycled and eco-friendly materials.',
            'This product has CE and RoHS certifications.',
            'Simply wipe with a damp cloth. Avoid harsh chemicals.',
            'Yes, we offer discounts for orders of 10 or more units.',
            'With proper care, this product should last 5-7 years.',
            'Yes, replacement parts are available through our customer service.',
        ];

        $statuses = ['pending', 'approved', 'rejected'];

        $this->command->info('Creating 20 product questions with answers...');

        foreach ($questions as $index => $questionText) {
            $product = $products->random();
            $user = $users->random();
            $status = $statuses[array_rand($statuses)];

            // Create question
            $question = ProductQuestion::create([
                'product_id' => $product->id,
                'user_id' => rand(0, 1) ? $user->id : null,
                'user_name' => rand(0, 1) ? null : 'Guest User ' . ($index + 1),
                'user_email' => rand(0, 1) ? null : 'guest' . ($index + 1) . '@example.com',
                'question' => $questionText,
                'status' => $status,
                'helpful_count' => rand(0, 15),
                'not_helpful_count' => rand(0, 5),
            ]);

            // Add 1-3 answers to each question if it's approved
            if ($status === 'approved') {
                $answerCount = rand(1, 3);
                for ($i = 0; $i < $answerCount; $i++) {
                    $answerUser = $users->random();
                    $answerStatus = rand(0, 1) ? 'approved' : 'pending';
                    
                    ProductAnswer::create([
                        'question_id' => $question->id,
                        'user_id' => rand(0, 1) ? $answerUser->id : null,
                        'user_name' => rand(0, 1) ? null : 'Expert ' . ($i + 1),
                        'user_email' => rand(0, 1) ? null : 'expert' . ($i + 1) . '@example.com',
                        'answer' => $answers[array_rand($answers)],
                        'status' => $answerStatus,
                        'is_verified_purchase' => rand(0, 1),
                        'is_best_answer' => $i === 0 && $answerStatus === 'approved' ? rand(0, 1) : false,
                        'helpful_count' => rand(0, 20),
                        'not_helpful_count' => rand(0, 8),
                    ]);
                }
            }

            $this->command->info("Created question " . ($index + 1) . ": " . substr($questionText, 0, 50) . "...");
        }

        $this->command->info('✅ Successfully created 20 product questions with answers!');
    }
}
