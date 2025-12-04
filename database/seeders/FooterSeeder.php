<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FooterSetting;
use App\Models\FooterLink;
use App\Models\FooterBlogPost;

class FooterSeeder extends Seeder
{
    public function run(): void
    {
        // General Settings
        $this->upsertFooterSetting(['key' => 'newsletter_title', 'value' => 'BE THE FIRST TO GET PROMO OFFERS AND REWARD PERKS STRAIGHT TO YOUR INBOX', 'type' => 'text', 'group' => 'general']);
        $this->upsertFooterSetting(['key' => 'newsletter_description', 'value' => 'Your email address will be used to send you Health Newsletters and emails about our products, services, sales, and special offers.', 'type' => 'html', 'group' => 'general']);
        $this->upsertFooterSetting(['key' => 'value_guarantee', 'value' => 'World\'s best value - guaranteed!', 'type' => 'text', 'group' => 'general']);
        $this->upsertFooterSetting(['key' => 'rewards_text', 'value' => 'Enjoy free products, insider access and exclusive offers', 'type' => 'text', 'group' => 'general']);
        
        // Social Media
        $this->upsertFooterSetting(['key' => 'facebook_url', 'value' => '#', 'type' => 'url', 'group' => 'social']);
        $this->upsertFooterSetting(['key' => 'twitter_url', 'value' => '#', 'type' => 'url', 'group' => 'social']);
        $this->upsertFooterSetting(['key' => 'youtube_url', 'value' => '#', 'type' => 'url', 'group' => 'social']);
        $this->upsertFooterSetting(['key' => 'pinterest_url', 'value' => '#', 'type' => 'url', 'group' => 'social']);
        $this->upsertFooterSetting(['key' => 'instagram_url', 'value' => '#', 'type' => 'url', 'group' => 'social']);
        
        // Legal
        $this->upsertFooterSetting(['key' => 'copyright_text', 'value' => 'iHerb.com  Copyright 1997-2025 iHerb, Ltd. All rights reserved.', 'type' => 'html', 'group' => 'legal']);
        
        // Mobile Apps
        $this->upsertFooterSetting(['key' => 'mobile_apps_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'mobile_apps']);
        $this->upsertFooterSetting(['key' => 'mobile_apps_title', 'value' => 'MOBILE APPS', 'type' => 'text', 'group' => 'mobile_apps']);
        $this->upsertFooterSetting(['key' => 'qr_code_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'mobile_apps']);
        $this->upsertFooterSetting(['key' => 'qr_code_image', 'value' => '', 'type' => 'image', 'group' => 'mobile_apps']);
        $this->upsertFooterSetting(['key' => 'google_play_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'mobile_apps']);
        $this->upsertFooterSetting(['key' => 'google_play_url', 'value' => '#', 'type' => 'url', 'group' => 'mobile_apps']);
        $this->upsertFooterSetting(['key' => 'app_store_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'mobile_apps']);
        $this->upsertFooterSetting(['key' => 'app_store_url', 'value' => '#', 'type' => 'url', 'group' => 'mobile_apps']);
        
        // Rewards Section
        $this->upsertFooterSetting(['key' => 'rewards_section_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'rewards']);
        $this->upsertFooterSetting(['key' => 'rewards_brand_name', 'value' => 'iHerb', 'type' => 'text', 'group' => 'rewards']);
        $this->upsertFooterSetting(['key' => 'rewards_section_title', 'value' => 'REWARDS', 'type' => 'text', 'group' => 'rewards']);
        $this->upsertFooterSetting(['key' => 'rewards_description', 'value' => 'Enjoy free products, insider access and exclusive offers', 'type' => 'text', 'group' => 'rewards']);
        $this->upsertFooterSetting(['key' => 'rewards_link_url', 'value' => '#', 'type' => 'url', 'group' => 'rewards']);
        $this->upsertFooterSetting(['key' => 'rewards_link_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'rewards']);

        // Footer Links - About Section
        $this->upsertFooterLink(['section' => 'about', 'title' => 'About us', 'url' => '#', 'sort_order' => 1]);
        $this->upsertFooterLink(['section' => 'about', 'title' => 'Store Reviews', 'url' => '#', 'sort_order' => 2]);
        $this->upsertFooterLink(['section' => 'about', 'title' => 'Rewards Programme', 'url' => '#', 'sort_order' => 3]);
        $this->upsertFooterLink(['section' => 'about', 'title' => 'Affiliate Programme', 'url' => '#', 'sort_order' => 4]);
        $this->upsertFooterLink(['section' => 'about', 'title' => 'iTested', 'url' => '#', 'sort_order' => 5]);
        $this->upsertFooterLink(['section' => 'about', 'title' => 'We Give Back', 'url' => '#', 'sort_order' => 6]);

        // Footer Links - Company Section
        $this->upsertFooterLink(['section' => 'company', 'title' => 'Corporate', 'url' => '#', 'sort_order' => 1]);
        $this->upsertFooterLink(['section' => 'company', 'title' => 'Press', 'url' => '#', 'sort_order' => 2]);
        $this->upsertFooterLink(['section' => 'company', 'title' => 'Partner with iHerb', 'url' => '#', 'sort_order' => 3]);

        // Footer Links - Resources Section
        $this->upsertFooterLink(['section' => 'resources', 'title' => 'Wellness Hub', 'url' => '#', 'sort_order' => 1]);
        $this->upsertFooterLink(['section' => 'resources', 'title' => 'Accessibility View', 'url' => '#', 'sort_order' => 2]);
        $this->upsertFooterLink(['section' => 'resources', 'title' => 'Sales & Offers', 'url' => '#', 'sort_order' => 3]);

        // Footer Links - Customer Support Section
        $this->upsertFooterLink(['section' => 'customer_support', 'title' => 'Contact Us', 'url' => '#', 'sort_order' => 1]);
        $this->upsertFooterLink(['section' => 'customer_support', 'title' => 'Suggest a Product', 'url' => '#', 'sort_order' => 2]);
        $this->upsertFooterLink(['section' => 'customer_support', 'title' => 'Order Status', 'url' => '/orders/track', 'sort_order' => 3]);
        $this->upsertFooterLink(['section' => 'customer_support', 'title' => 'Delivery', 'url' => '#', 'sort_order' => 4]);
        $this->upsertFooterLink(['section' => 'customer_support', 'title' => 'Communication Preferences', 'url' => '#', 'sort_order' => 5]);

        // Blog Posts
        $this->upsertFooterBlogPost(['title' => '5 Simple Oral Health Tips', 'url' => '#', 'sort_order' => 1]);
        $this->upsertFooterBlogPost(['title' => 'Why Do Babies Need DHA?', 'url' => '#', 'sort_order' => 2]);
        $this->upsertFooterBlogPost(['title' => 'Best Home Remedies For A Cough + Sore Throat', 'url' => '#', 'sort_order' => 3]);
        $this->upsertFooterBlogPost(['title' => 'Top 10 Supplements For Longevity', 'url' => '#', 'sort_order' => 4]);
        $this->upsertFooterBlogPost(['title' => 'What Is Fungal Acne? A Doctor\'s Guide To Products', 'url' => '#', 'sort_order' => 5]);
        $this->upsertFooterBlogPost(['title' => 'Krill Oil: What It Is + 12 Science-Backed Benefits', 'url' => '#', 'sort_order' => 6]);
    }

    /**
     * Smart upsert for footer settings: Only create or update if metadata differs (excludes value, timestamps)
     */
    private function upsertFooterSetting(array $data): void
    {
        $existing = FooterSetting::where('key', $data['key'])->first();

        if (!$existing) {
            FooterSetting::create($data);
            $this->command->info("Created footer setting: {$data['key']}");
        } else {
            $excludeFields = ['key', 'value', 'created_at', 'updated_at'];
            $needsUpdate = false;
            $updates = [];

            foreach ($data as $field => $newValue) {
                if (in_array($field, $excludeFields)) continue;
                
                if ($existing->{$field} != $newValue) {
                    $needsUpdate = true;
                    $updates[$field] = $newValue;
                }
            }

            if ($needsUpdate) {
                $existing->update($updates);
                $this->command->info("Updated footer setting metadata: {$data['key']}");
            }
        }
    }

    /**
     * Smart upsert for footer links: Only create or update if data differs (excludes timestamps)
     */
    private function upsertFooterLink(array $data): void
    {
        $existing = FooterLink::where('section', $data['section'])
                              ->where('title', $data['title'])
                              ->first();

        if (!$existing) {
            FooterLink::create($data);
            $this->command->info("Created footer link: {$data['section']} - {$data['title']}");
        } else {
            $excludeFields = ['section', 'title', 'created_at', 'updated_at'];
            $needsUpdate = false;
            $updates = [];

            foreach ($data as $field => $newValue) {
                if (in_array($field, $excludeFields)) continue;
                
                if ($existing->{$field} != $newValue) {
                    $needsUpdate = true;
                    $updates[$field] = $newValue;
                }
            }

            if ($needsUpdate) {
                $existing->update($updates);
                $this->command->info("Updated footer link: {$data['section']} - {$data['title']}");
            }
        }
    }

    /**
     * Smart upsert for footer blog posts: Only create or update if data differs (excludes timestamps)
     */
    private function upsertFooterBlogPost(array $data): void
    {
        $existing = FooterBlogPost::where('title', $data['title'])->first();

        if (!$existing) {
            FooterBlogPost::create($data);
            $this->command->info("Created footer blog post: {$data['title']}");
        } else {
            $excludeFields = ['title', 'created_at', 'updated_at'];
            $needsUpdate = false;
            $updates = [];

            foreach ($data as $field => $newValue) {
                if (in_array($field, $excludeFields)) continue;
                
                if ($existing->{$field} != $newValue) {
                    $needsUpdate = true;
                    $updates[$field] = $newValue;
                }
            }

            if ($needsUpdate) {
                $existing->update($updates);
                $this->command->info("Updated footer blog post: {$data['title']}");
            }
        }
    }
}
