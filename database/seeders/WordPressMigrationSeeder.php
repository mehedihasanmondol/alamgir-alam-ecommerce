<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 * WordPress Migration Seeder
 * 
 * This seeder migrates content from WordPress/WooCommerce to Laravel.
 * It uses the MigrateFromWordPress command to handle the migration.
 * 
 * Usage:
 * 
 * 1. Set environment variables in .env:
 *    WORDPRESS_DOMAIN=https://prokriti.org
 *    WOOCOMMERCE_KEY=ck_xxxxx
 *    WOOCOMMERCE_SECRET=cs_xxxxx
 * 
 * 2. Run the seeder:
 *    php artisan db:seed --class=WordPressMigrationSeeder
 * 
 * 3. Or run with custom options:
 *    php artisan wordpress:migrate --domain=https://prokriti.org --wc-key=ck_xxx --wc-secret=cs_xxx
 * 
 * Features:
 * - Migrates all blog posts with categories, tags, and authors
 * - Migrates WooCommerce products with variations and attributes
 * - Downloads all images to /storage/wordpress
 * - Replaces old WordPress URLs with new Laravel URLs
 * - Preserves SEO meta data (title, description, keywords)
 * - Maintains slugs and permalinks
 * - Preserves publish dates and authors
 */
class WordPressMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("🚀 Starting WordPress Migration Seeder");
        $this->command->newLine();

        // Get credentials from environment or prompt
        $domain = env('WORDPRESS_DOMAIN', 'https://prokriti.org');
        $wcKey = env('WOOCOMMERCE_KEY', '');
        $wcSecret = env('WOOCOMMERCE_SECRET', '');

        // Confirm migration
        if (!$this->command->confirm("Migrate from {$domain}? This may take a while.", true)) {
            $this->command->warn('Migration cancelled.');
            return;
        }

        // Ask for WooCommerce credentials if not in env
        if (empty($wcKey)) {
            $wcKey = $this->command->ask('Enter WooCommerce Consumer Key (leave empty to skip products)');
        }

        if (empty($wcSecret) && !empty($wcKey)) {
            $wcSecret = $this->command->secret('Enter WooCommerce Consumer Secret');
        }

        // Build command options
        $options = [
            '--domain' => $domain,
        ];

        if (!empty($wcKey) && !empty($wcSecret)) {
            $options['--wc-key'] = $wcKey;
            $options['--wc-secret'] = $wcSecret;
        }

        // Ask for additional options
        if ($this->command->confirm('Do you want to customize migration options?', false)) {
            $onlyPosts = $this->command->confirm('Migrate only blog posts?', false);
            $onlyProducts = $this->command->confirm('Migrate only products?', false);
            $skipImages = $this->command->confirm('Skip image download?', false);
            $batchSize = $this->command->ask('Batch size (items per page)', '10');
            $startFrom = $this->command->ask('Start from page number', '1');

            if ($onlyPosts) {
                $options['--only-posts'] = true;
            }
            if ($onlyProducts) {
                $options['--only-products'] = true;
            }
            if ($skipImages) {
                $options['--skip-images'] = true;
            }
            $options['--batch-size'] = $batchSize;
            $options['--start-from'] = $startFrom;
        }

        // Run migration command
        $this->command->newLine();
        $exitCode = Artisan::call('wordpress:migrate', $options, $this->command->getOutput());

        if ($exitCode === 0) {
            $this->command->newLine();
            $this->command->info('✅ WordPress migration completed successfully!');
            $this->command->newLine();
            
            // Display helpful information
            $this->displayPostMigrationInfo();
        } else {
            $this->command->error('❌ Migration failed. Check the error messages above.');
        }
    }

    /**
     * Display post-migration information
     */
    protected function displayPostMigrationInfo(): void
    {
        $this->command->info('📋 Next Steps:');
        $this->command->line('');
        $this->command->line('1. Review migrated content in admin panel:');
        $this->command->line('   - Blog Posts: /admin/blog/posts');
        $this->command->line('   - Products: /admin/products');
        $this->command->line('');
        $this->command->line('2. Check downloaded images in:');
        $this->command->line('   storage/app/public/wordpress/');
        $this->command->line('');
        $this->command->line('3. Update default admin user passwords:');
        $this->command->line('   - Migrated users have random passwords');
        $this->command->line('   - Use: php artisan user:reset-password');
        $this->command->line('');
        $this->command->line('4. Re-run migration if needed:');
        $this->command->line('   - The migration is idempotent (can run multiple times)');
        $this->command->line('   - Existing records will be updated, not duplicated');
        $this->command->line('');
        $this->command->line('5. Customize SEO settings:');
        $this->command->line('   - Review and adjust meta titles/descriptions');
        $this->command->line('   - Update slugs if needed');
        $this->command->line('');
        $this->command->info('💡 Tip: Run `php artisan wordpress:migrate --help` to see all options');
    }
}
