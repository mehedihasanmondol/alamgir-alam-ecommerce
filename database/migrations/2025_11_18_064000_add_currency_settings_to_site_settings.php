<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add currency settings to site_settings table
        $settings = [
            [
                'key' => 'currency_symbol',
                'value' => '$',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Currency Symbol',
                'description' => 'The symbol to display for currency (e.g., $, €, £, ৳)',
                'order' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_code',
                'value' => 'USD',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Currency Code',
                'description' => 'ISO 4217 currency code (e.g., USD, EUR, GBP, BDT)',
                'order' => 101,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_position',
                'value' => 'before',
                'type' => 'select',
                'group' => 'general',
                'label' => 'Currency Position',
                'description' => 'Display currency symbol before or after the amount',
                'order' => 102,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            // Check if setting already exists
            $exists = DB::table('site_settings')
                ->where('key', $setting['key'])
                ->exists();

            if (!$exists) {
                DB::table('site_settings')->insert($setting);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', [
            'currency_symbol',
            'currency_code',
            'currency_position',
        ])->delete();
    }
};
