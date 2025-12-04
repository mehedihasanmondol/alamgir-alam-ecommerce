<?php

namespace App\Console\Commands;

use App\Mail\RecommendationMail;
use App\Models\User;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Order\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SendRecommendationEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-recommendations {--test : Run in test mode without sending emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send product recommendation emails to subscribed users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $testMode = $this->option('test');
        
        $this->info('Starting recommendation email process...');
        
        // Get users who opted in for recommendations
        $users = User::where('email_recommendations', true)
            ->where('is_active', true)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();
        
        if ($users->isEmpty()) {
            $this->warn('No users subscribed to recommendation emails.');
            return 0;
        }
        
        $this->info("Found {$users->count()} subscribers.");
        
        if ($testMode) {
            $this->info('TEST MODE: No emails will be sent.');
            $this->table(
                ['Name', 'Email', 'Status'],
                $users->take(10)->map(function ($user) {
                    return [
                        $user->name,
                        $user->email,
                        'Would send'
                    ];
                })
            );
            return 0;
        }
        
        $sentCount = 0;
        $failedCount = 0;
        
        $this->info('Sending recommendation emails...');
        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();
        
        foreach ($users as $user) {
            try {
                $subject = $this->getRecommendationSubject($user);
                $recommendedProducts = $this->getRecommendedProducts($user);
                $basedOn = $this->getRecommendationBasis($user);
                
                // Only send if we have recommendations
                if ($recommendedProducts->isNotEmpty()) {
                    Mail::to($user->email)->send(
                        new RecommendationMail(
                            $user,
                            $subject,
                            $recommendedProducts,
                            $basedOn
                        )
                    );
                    
                    $sentCount++;
                }
                
            } catch (\Exception $e) {
                $failedCount++;
                Log::error("Failed to send recommendations to {$user->email}: " . $e->getMessage());
            }
            
            $progressBar->advance();
            
            // Rate limiting: Wait 100ms between emails
            usleep(100000);
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Recommendation campaign completed!");
        $this->info("✓ Successfully sent: {$sentCount}");
        
        if ($failedCount > 0) {
            $this->error("✗ Failed: {$failedCount}");
            $this->warn("Check logs for details: storage/logs/laravel.log");
        }
        
        return 0;
    }
    
    /**
     * Get recommendation subject
     */
    private function getRecommendationSubject($user): string
    {
        $siteName = config('app.name');
        return "Hi {$user->name}, We Think You'll Love These Products!";
    }
    
    /**
     * Get recommendation basis message
     */
    private function getRecommendationBasis($user): string
    {
        // Check if user has order history
        $hasOrders = Order::where('user_id', $user->id)->exists();
        
        if ($hasOrders) {
            return 'your purchase history';
        }
        
        return 'popular products in our store';
    }
    
    /**
     * Get recommended products for user
     */
    private function getRecommendedProducts($user)
    {
        // Try to get personalized recommendations based on order history
        $recommendations = $this->getPersonalizedRecommendations($user);
        
        // If no personalized recommendations, get trending products
        if ($recommendations->isEmpty()) {
            $recommendations = $this->getTrendingProducts();
        }
        
        return $recommendations->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $product->image,
                'price' => $product->defaultVariant->selling_price ?? $product->defaultVariant->price ?? 0,
                'original_price' => $product->defaultVariant->price ?? 0,
                'rating' => $product->average_rating ?? 0,
                'reviews_count' => $product->reviews_count ?? 0,
                'url' => route('products.show', $product->slug),
            ];
        });
    }
    
    /**
     * Get personalized recommendations based on purchase history
     */
    private function getPersonalizedRecommendations($user)
    {
        // Get categories from user's previous orders
        $purchasedCategoryIds = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.user_id', $user->id)
            ->where('orders.status', 'delivered')
            ->distinct()
            ->pluck('products.category_id');
        
        if ($purchasedCategoryIds->isEmpty()) {
            return collect([]);
        }
        
        // Get products from same categories that user hasn't purchased
        $purchasedProductIds = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $user->id)
            ->pluck('order_items.product_id');
        
        return Product::with(['defaultVariant', 'category'])
            ->where('is_active', true)
            ->whereIn('category_id', $purchasedCategoryIds)
            ->whereNotIn('id', $purchasedProductIds)
            ->inRandomOrder()
            ->limit(6)
            ->get();
    }
    
    /**
     * Get trending products as fallback
     */
    private function getTrendingProducts()
    {
        return Product::with(['defaultVariant', 'category'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('is_featured', true)
                    ->orWhere('is_trending', true);
            })
            ->inRandomOrder()
            ->limit(6)
            ->get();
    }
}
