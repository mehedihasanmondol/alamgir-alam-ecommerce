<?php

namespace App\Console\Commands;

use App\Mail\NewsletterMail;
use App\Models\User;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNewsletterEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-newsletter {--test : Run in test mode without sending emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send newsletter emails to subscribed users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $testMode = $this->option('test');
        
        $this->info('Starting newsletter email process...');
        
        // Get users who opted in for newsletter
        $users = User::where('email_newsletter', true)
            ->where('is_active', true)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();
        
        if ($users->isEmpty()) {
            $this->warn('No users subscribed to newsletter.');
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
        
        // Get content for newsletter
        $subject = $this->getNewsletterSubject();
        $content = $this->getNewsletterContent();
        $featuredProducts = $this->getFeaturedProducts();
        $recentBlogPosts = $this->getRecentBlogPosts();
        
        $sentCount = 0;
        $failedCount = 0;
        
        $this->info('Sending newsletter emails...');
        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();
        
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(
                    new NewsletterMail(
                        $user,
                        $subject,
                        $content,
                        $featuredProducts,
                        $recentBlogPosts
                    )
                );
                
                $sentCount++;
                
            } catch (\Exception $e) {
                $failedCount++;
                Log::error("Failed to send newsletter to {$user->email}: " . $e->getMessage());
            }
            
            $progressBar->advance();
            
            // Rate limiting: Wait 100ms between emails to avoid overwhelming the mail server
            usleep(100000);
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Newsletter campaign completed!");
        $this->info("✓ Successfully sent: {$sentCount}");
        
        if ($failedCount > 0) {
            $this->error("✗ Failed: {$failedCount}");
            $this->warn("Check logs for details: storage/logs/laravel.log");
        }
        
        return 0;
    }
    
    /**
     * Get newsletter subject
     */
    private function getNewsletterSubject(): string
    {
        $siteName = config('app.name');
        $week = now()->format('F d, Y');
        return "{$siteName} Newsletter - {$week}";
    }
    
    /**
     * Get newsletter content
     */
    private function getNewsletterContent(): string
    {
        return "Hello! Here's what's new this week at " . config('app.name') . ". "
            . "Check out our featured products, latest blog posts, and exclusive offers below.";
    }
    
    /**
     * Get featured products for newsletter
     */
    private function getFeaturedProducts()
    {
        return Product::with(['defaultVariant', 'category'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->inRandomOrder()
            ->limit(4)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->image,
                    'price' => $product->defaultVariant->selling_price ?? $product->defaultVariant->price ?? 0,
                    'original_price' => $product->defaultVariant->price ?? 0,
                    'url' => route('products.show', $product->slug),
                ];
            });
    }
    
    /**
     * Get recent blog posts for newsletter
     */
    private function getRecentBlogPosts()
    {
        return BlogPost::with('category')
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->excerpt,
                    'image' => $post->featured_image,
                    'category' => $post->category->name ?? 'Uncategorized',
                    'published_at' => $post->published_at->format('M d, Y'),
                    'url' => route('blog.show', $post->slug),
                ];
            });
    }
}
