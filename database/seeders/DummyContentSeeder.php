<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyContentSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Seeders are executed in dependency order to prevent errors.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Dummy content database seeding...');
      
        // Phase 2: User Management & Permissions (Depends on settings)
        $this->command->info('👥 Phase 2: User Management & Permissions');
        $this->call([
            TestUsersSeeder::class,
        ]);

        // Phase 3: Product & Category Setup (Depends on users)
        $this->command->info('📦 Phase 3: Product & Category Setup');
        $this->call([
            HealthCategorySeeder::class,
            TrendingBrandSeeder::class,
        ]);

        // Phase 4: Blog Content (Depends on categories and users)
        $this->command->info('📝 Phase 4: Blog Content');
        $this->call([
            ComprehensiveHealthBlogSeeder::class,
            HealthBlogPostsSeeder::class,
            BlogCommentSeeder::class,
        ]);

        // Phase 5: E-commerce Features (Depends on products)
        $this->command->info('🛒 Phase 5: E-commerce Features');
        $this->call([
            ProductQAAndReviewSeeder::class,
            ProductQuestionSeeder::class,
            ProductReviewSeeder::class,
            CouponSeeder::class,
        ]);

        // Phase 6: Stock & Finance Management (Depends on products)
        $this->command->info('📊 Phase 6: Stock & Finance Management');
        $this->call([
            StockManagementSeeder::class,
        ]);

        // Phase 8: Test Data (Optional - Depends on all above)
        $this->command->info('🧪 Phase 8: Test Data (Optional)');
        $this->call([
            TestOrdersSeeder::class,
        ]);

        $this->command->info('✅ Dummy content Database seeding completed successfully!');
    }
}
