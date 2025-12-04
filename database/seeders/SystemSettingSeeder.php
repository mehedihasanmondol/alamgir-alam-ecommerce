<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Maintenance Mode Settings
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'maintenance',
                'description' => 'Enable or disable site construction/maintenance mode',
            ],
            [
                'key' => 'maintenance_title',
                'value' => 'We\'ll be back soon!',
                'type' => 'text',
                'group' => 'maintenance',
                'description' => 'Maintenance page title',
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'Sorry for the inconvenience. We\'re performing some maintenance at the moment. We\'ll be back online shortly!',
                'type' => 'text',
                'group' => 'maintenance',
                'description' => 'Maintenance page message',
            ],
            [
                'key' => 'maintenance_image',
                'value' => '',
                'type' => 'file',
                'group' => 'maintenance',
                'description' => 'Custom maintenance page image',
            ],
        ];

        foreach ($settings as $setting) {
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
