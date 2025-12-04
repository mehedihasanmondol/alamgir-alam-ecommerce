<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Seeders are executed in dependency order to prevent errors.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        
        // Phase 1: Core Configuration & Settings (No dependencies)
        $this->command->info('📋 Phase 1: Core Configuration & Settings');
        $this->call([
            SiteSettingSeeder::class,
            HomepageSettingSeeder::class,
            HeroSliderSeeder::class,
            FooterSeeder::class,
            ThemeSettingSeeder::class,
            ImageUploadSettingSeeder::class,
            SecondaryMenuSeeder::class,
            BlogTickMarkSeeder::class,
        ]);

        // Phase 2: User Management & Permissions (Depends on settings)
        $this->command->info('👥 Phase 2: User Management & Permissions');
        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
        ]);

    

        // Phase 6: Stock & Finance Management (Depends on products)
        $this->command->info('📊 Phase 6: Stock & Finance Management');
        $this->call([
            StockManagementSeeder::class,
        ]);

        // Phase 7: Payment & Delivery Systems (Depends on settings)
        $this->command->info('💳 Phase 7: Payment & Delivery Systems');
        $this->call([
            PaymentGatewaySeeder::class,
            DeliverySystemSeeder::class,
        ]);

   

        $this->command->info('✅ Database seeding completed successfully!');
    }
}
