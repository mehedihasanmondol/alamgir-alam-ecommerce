<?php

namespace App\Console\Commands;

use App\Mail\PromotionalMail;
use App\Models\User;
use App\Models\Coupon;
use App\Modules\Ecommerce\Product\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendPromotionalEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-promotions {--test : Run in test mode without sending emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send promotional emails to subscribed users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $testMode = $this->option('test');
        
        $this->info('Starting promotional email process...');
        
        // Get users who opted in for promotions
        $users = User::where('email_promotions', true)
            ->where('is_active', true)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();
        
        if ($users->isEmpty()) {
            $this->warn('No users subscribed to promotional emails.');
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
        
        // Get promotion details
        $subject = $this->getPromotionSubject();
        $promotionTitle = $this->getPromotionTitle();
        $promotionDescription = $this->getPromotionDescription();
        $activeCoupon = $this->getActiveCoupon();
        $saleProducts = $this->getSaleProducts();
        
        $sentCount = 0;
        $failedCount = 0;
        
        $this->info('Sending promotional emails...');
        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();
        
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(
                    new PromotionalMail(
                        $user,
                        $subject,
                        $promotionTitle,
                        $promotionDescription,
                        $activeCoupon['code'] ?? null,
                        $activeCoupon['discount'] ?? null,
                        $activeCoupon['expiry'] ?? null,
                        $saleProducts
                    )
                );
                
                $sentCount++;
                
            } catch (\Exception $e) {
                $failedCount++;
                Log::error("Failed to send promotion to {$user->email}: " . $e->getMessage());
            }
            
            $progressBar->advance();
            
            // Rate limiting: Wait 100ms between emails
            usleep(100000);
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Promotional campaign completed!");
        $this->info("✓ Successfully sent: {$sentCount}");
        
        if ($failedCount > 0) {
            $this->error("✗ Failed: {$failedCount}");
            $this->warn("Check logs for details: storage/logs/laravel.log");
        }
        
        return 0;
    }
    
    /**
     * Get promotion subject
     */
    private function getPromotionSubject(): string
    {
        $siteName = config('app.name');
        return "🎉 Special Offer from {$siteName} - Don't Miss Out!";
    }
    
    /**
     * Get promotion title
     */
    private function getPromotionTitle(): string
    {
        return "Weekend Flash Sale - Up to 50% OFF!";
    }
    
    /**
     * Get promotion description
     */
    private function getPromotionDescription(): string
    {
        return "Hurry! This weekend only, enjoy massive discounts on selected health & wellness products. "
            . "Shop now and save big on your favorite items. Limited time offer!";
    }
    
    /**
     * Get active coupon
     */
    private function getActiveCoupon(): ?array
    {
        $coupon = Coupon::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                    ->orWhereRaw('usage_count < usage_limit');
            })
            ->orderBy('discount_percentage', 'desc')
            ->first();
        
        if ($coupon) {
            return [
                'code' => $coupon->code,
                'discount' => $coupon->discount_percentage,
                'expiry' => $coupon->end_date->format('M d, Y'),
            ];
        }
        
        return null;
    }
    
    /**
     * Get products on sale
     */
    private function getSaleProducts()
    {
        return Product::with(['defaultVariant', 'category'])
            ->where('is_active', true)
            ->whereHas('defaultVariant', function ($query) {
                $query->whereColumn('selling_price', '<', 'price')
                    ->whereNotNull('selling_price');
            })
            ->inRandomOrder()
            ->limit(6)
            ->get()
            ->map(function ($product) {
                $originalPrice = $product->defaultVariant->price ?? 0;
                $salePrice = $product->defaultVariant->selling_price ?? $originalPrice;
                $discount = $originalPrice > 0 ? round((($originalPrice - $salePrice) / $originalPrice) * 100) : 0;
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->image,
                    'price' => $salePrice,
                    'original_price' => $originalPrice,
                    'discount_percentage' => $discount,
                    'url' => route('products.show', $product->slug),
                ];
            });
    }
}
