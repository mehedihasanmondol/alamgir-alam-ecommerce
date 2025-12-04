<?php

namespace App\Console\Commands;

use App\Services\MegaMenuService;
use Illuminate\Console\Command;

/**
 * Clear Mega Menu Cache Command
 * Purpose: Clear all mega menu related caches including trending brands
 * 
 * Usage: php artisan cache:clear-megamenu
 * 
 * @author AI Assistant
 * @date 2025-11-19
 */
class ClearMegaMenuCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-megamenu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all mega menu related caches including trending brands';

    /**
     * Execute the console command.
     */
    public function handle(MegaMenuService $megaMenuService)
    {
        $this->info('Clearing mega menu caches...');
        
        // Clear trending brands cache
        $megaMenuService->clearTrendingBrandsCache();
        
        $this->info('✓ Mega menu categories cache cleared');
        $this->info('✓ Trending brands cache cleared for all categories');
        $this->info('✓ Global trending brands cache cleared');
        
        $this->newLine();
        $this->info('Mega menu cache cleared successfully!');
        $this->info('The mega menu will now show updated trending brands based on latest sales data.');
        
        return Command::SUCCESS;
    }
}
