<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ThemeSetting extends Model
{
    protected $fillable = [
        'name',
        'label',
        'is_active',
        'is_predefined',
        // Primary
        'primary_bg',
        'primary_bg_hover',
        'primary_text',
        'primary_border',
        // Secondary
        'secondary_bg',
        'secondary_bg_hover',
        'secondary_text',
        'secondary_border',
        // Success
        'success_bg',
        'success_bg_hover',
        'success_text',
        'success_border',
        // Danger
        'danger_bg',
        'danger_bg_hover',
        'danger_text',
        'danger_border',
        // Warning
        'warning_bg',
        'warning_bg_hover',
        'warning_text',
        'warning_border',
        // Info
        'info_bg',
        'info_bg_hover',
        'info_text',
        'info_border',
        // Buttons
        'button_primary_bg',
        'button_primary_bg_hover',
        'button_primary_text',
        'button_primary_border',
        'button_secondary_bg',
        'button_secondary_bg_hover',
        'button_secondary_text',
        'button_secondary_border',
        // Card
        'card_bg',
        'card_text',
        'card_border',
        'card_shadow',
        // Sidebar
        'sidebar_bg',
        'sidebar_text',
        'sidebar_active_bg',
        'sidebar_active_text',
        'sidebar_hover_bg',
        // Header
        'header_bg',
        'header_text',
        'header_border',
        // Link
        'link_text',
        'link_hover_text',
        'link_underline',
        // Badge
        'badge_primary_bg',
        'badge_primary_text',
        'badge_success_bg',
        'badge_success_text',
        'badge_danger_bg',
        'badge_danger_text',
        'badge_warning_bg',
        'badge_warning_text',
        // Input
        'input_bg',
        'input_text',
        'input_border',
        'input_focus_border',
        'input_focus_ring',
        // Table
        'table_header_bg',
        'table_header_text',
        'table_row_hover',
        'table_border',
        // Modal
        'modal_bg',
        'modal_text',
        'modal_overlay_bg',
        'modal_overlay_opacity',
        // Frontend Navigation
        'nav_bg',
        'nav_text',
        'nav_hover_bg',
        'nav_hover_text',
        'nav_active_text',
        // Frontend Product Card
        'product_card_bg',
        'product_card_border',
        'product_card_hover_shadow',
        'product_title_text',
        'product_price_text',
        'product_old_price_text',
        'product_discount_badge_bg',
        'product_discount_badge_text',
        // Frontend Buttons
        'shop_button_bg',
        'shop_button_hover_bg',
        'shop_button_text',
        'cart_button_bg',
        'cart_button_hover_bg',
        'cart_button_text',
        // Frontend Footer
        'footer_bg',
        'footer_text',
        'footer_heading_text',
        'footer_link_text',
        'footer_link_hover_text',
        // Frontend Hero/Banner
        'hero_overlay_bg',
        'hero_title_text',
        'hero_subtitle_text',
        'hero_button_bg',
        'hero_button_hover_bg',
        'hero_button_text',
        // Frontend Category
        'category_badge_bg',
        'category_badge_text',
        'category_badge_hover_bg',
        // Frontend Price
        'price_color',
        'sale_price_color',
        'stock_available_text',
        'stock_unavailable_text',
        // Frontend Search
        'search_input_bg',
        'search_input_text',
        'search_input_border',
        'search_input_focus_border',
        // Frontend Rating
        'rating_star_color',
        'rating_star_empty_color',
        // Frontend Newsletter
        'newsletter_bg',
        'newsletter_text',
        'newsletter_button_bg',
        'newsletter_button_text',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_predefined' => 'boolean',
    ];

    /**
     * Get the active theme
     */
    public static function getActive()
    {
        return Cache::remember('active_theme', 3600, function () {
            return self::where('is_active', true)->first() ?? self::getDefault();
        });
    }

    /**
     * Get default theme
     */
    public static function getDefault()
    {
        return self::where('name', 'default')->first();
    }

    /**
     * Activate this theme
     */
    public function activate()
    {
        // Deactivate all themes
        self::query()->update(['is_active' => false]);
        
        // Activate this theme
        $this->update(['is_active' => true]);
        
        // Clear cache
        Cache::forget('active_theme');
        
        return true;
    }

    /**
     * Get a specific theme class
     */
    public static function getClass(string $key, string $default = '')
    {
        $theme = self::getActive();
        
        if (!$theme) {
            return $default;
        }
        
        return $theme->$key ?? $default;
    }

    /**
     * Get all theme classes as array
     */
    public function toClassArray(): array
    {
        $attributes = $this->getAttributes();
        $classes = [];
        
        // Remove non-class fields
        unset($attributes['id'], $attributes['name'], $attributes['label'], 
              $attributes['is_active'], $attributes['is_predefined'], 
              $attributes['created_at'], $attributes['updated_at']);
        
        return $attributes;
    }
}
