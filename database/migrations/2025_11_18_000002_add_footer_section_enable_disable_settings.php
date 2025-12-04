<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add enable/disable settings for footer sections
        $settings = [
            // Wellness Hub/Blog Section
            [
                'key' => 'wellness_hub_section_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'footer_sections',
            ],
            
            // Value Guarantee Banner
            [
                'key' => 'value_guarantee_section_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'footer_sections',
            ],
            
            // Newsletter Section
            [
                'key' => 'newsletter_section_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'footer_sections',
            ],
            
            // Social Media Section
            [
                'key' => 'social_media_section_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'footer_sections',
            ],
            
            // Footer Links Section
            [
                'key' => 'footer_links_section_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'footer_sections',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('footer_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('footer_settings')->whereIn('key', [
            'wellness_hub_section_enabled',
            'value_guarantee_section_enabled',
            'newsletter_section_enabled',
            'social_media_section_enabled',
            'footer_links_section_enabled',
        ])->delete();
    }
};
