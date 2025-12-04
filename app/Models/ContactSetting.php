<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ContactSetting extends Model
{
    protected $fillable = [
        'key',
        'group',
        'value',
        'type',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get a setting value by key with caching
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("contact_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->where('is_active', true)->first();
            
            if (!$setting) {
                return $default;
            }

            // Handle boolean type
            if ($setting->type === 'boolean') {
                return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
            }

            return $setting->value ?? $default;
        });
    }

    /**
     * Get all settings by group with caching
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("contact_settings_group_{$group}", 3600, function () use ($group) {
            return self::where('group', $group)
                ->where('is_active', true)
                ->orderBy('order')
                ->get()
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Clear settings cache
     */
    public static function clearCache(): void
    {
        Cache::flush();
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}
