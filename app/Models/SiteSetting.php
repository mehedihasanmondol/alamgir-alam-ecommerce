<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * ModuleName: Site Settings Model
 * Purpose: Manage site-wide settings with caching
 * 
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $type
 * @property string $group
 * @property string $label
 * @property string|null $description
 * @property int $order
 * 
 * @category Models
 * @package  App\Models
 * @created  2025-11-11
 */
class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'order',
    ];

    /**
     * Cache key for all settings
     */
    const CACHE_KEY = 'site_settings_all';

    /**
     * Cache duration in seconds (1 day)
     */
    const CACHE_DURATION = 86400;

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when settings are updated
        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }

    /**
     * Get a setting value by key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::getAllCached();
        return $settings[$key] ?? $default;
    }

    /**
     * Get all settings cached
     * 
     * @return array
     */
    public static function getAllCached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return self::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get all settings grouped
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function getAllGrouped()
    {
        return self::orderBy('group')->orderBy('order')->get()->groupBy('group');
    }

    /**
     * Set a setting value
     * 
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function set(string $key, $value): bool
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            $setting->value = $value;
            return $setting->save();
        }

        return false;
    }

    /**
     * Clear settings cache
     * 
     * @return void
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get image URL for image type settings
     * 
     * @return string|null
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->type === 'image' && $this->value) {
            // Check if it's a full URL
            if (filter_var($this->value, FILTER_VALIDATE_URL)) {
                return $this->value;
            }
            
            // Return storage URL
            return Storage::url($this->value);
        }

        return null;
    }

    /**
     * Get boolean value for boolean type settings
     * 
     * @return bool
     */
    public function getBooleanValueAttribute(): bool
    {
        if ($this->type === 'boolean') {
            return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
        }

        return false;
    }
}
