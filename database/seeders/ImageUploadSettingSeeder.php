<?php

namespace Database\Seeders;

use App\Models\ImageUploadSetting;
use Illuminate\Database\Seeder;

class ImageUploadSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Compression
            [
                'key' => 'default_compression',
                'value' => '70',
                'type' => 'number',
                'description' => 'Default WebP compression quality (0-100)',
            ],
            
            // Output formats
            [
                'key' => 'output_format',
                'value' => 'webp',
                'type' => 'text',
                'description' => 'Primary output format (webp, jpg, png)',
            ],
            [
                'key' => 'preserve_original',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Keep original uploaded file',
            ],
            
            // Size presets
            [
                'key' => 'size_large_width',
                'value' => '1920',
                'type' => 'number',
                'description' => 'Large size max width in pixels',
            ],
            [
                'key' => 'size_large_height',
                'value' => '1920',
                'type' => 'number',
                'description' => 'Large size max height in pixels',
            ],
            [
                'key' => 'size_medium_width',
                'value' => '1200',
                'type' => 'number',
                'description' => 'Medium size max width in pixels',
            ],
            [
                'key' => 'size_medium_height',
                'value' => '1200',
                'type' => 'number',
                'description' => 'Medium size max height in pixels',
            ],
            [
                'key' => 'size_small_width',
                'value' => '600',
                'type' => 'number',
                'description' => 'Small size max width in pixels',
            ],
            [
                'key' => 'size_small_height',
                'value' => '600',
                'type' => 'number',
                'description' => 'Small size max height in pixels',
            ],
            
            // File limits
            [
                'key' => 'max_file_size',
                'value' => '5',
                'type' => 'number',
                'description' => 'Maximum file size in MB',
            ],
            [
                'key' => 'max_width',
                'value' => '4000',
                'type' => 'number',
                'description' => 'Maximum image width in pixels',
            ],
            [
                'key' => 'max_height',
                'value' => '4000',
                'type' => 'number',
                'description' => 'Maximum image height in pixels',
            ],
            
            // Upload settings
            [
                'key' => 'allow_multiple',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Allow multiple file uploads',
            ],
            [
                'key' => 'storage_disk',
                'value' => 'public',
                'type' => 'text',
                'description' => 'Default storage disk (local, public, s3)',
            ],
            [
                'key' => 'enable_optimizer',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable spatie/image-optimizer pipeline',
            ],
            
            // Library settings
            [
                'key' => 'library_page_size',
                'value' => '20',
                'type' => 'number',
                'description' => 'Number of images per page in library',
            ],
            [
                'key' => 'default_library_scope',
                'value' => 'global',
                'type' => 'text',
                'description' => 'Default library scope (user, global)',
            ],
            
            // Aspect ratio presets
            [
                'key' => 'aspect_ratio_presets',
                'value' => json_encode([
                    ['label' => 'Free', 'value' => 'free'],
                    ['label' => 'Square (1:1)', 'value' => '1'],
                    ['label' => 'Landscape (16:9)', 'value' => '1.7778'],
                    ['label' => 'Portrait (9:16)', 'value' => '0.5625'],
                    ['label' => 'Wide (21:9)', 'value' => '2.3333'],
                    ['label' => '4:3', 'value' => '1.3333'],
                    ['label' => '3:2', 'value' => '1.5'],
                ]),
                'type' => 'json',
                'description' => 'Available aspect ratio presets for cropping',
            ],
        ];

        foreach ($settings as $setting) {
            ImageUploadSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
