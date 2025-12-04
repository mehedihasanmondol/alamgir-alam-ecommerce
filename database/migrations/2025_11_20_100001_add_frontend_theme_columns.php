<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            // Frontend Navigation
            $table->text('nav_bg')->nullable()->after('modal_overlay_opacity');
            $table->text('nav_text')->nullable();
            $table->text('nav_hover_bg')->nullable();
            $table->text('nav_hover_text')->nullable();
            $table->text('nav_active_text')->nullable();
            
            // Frontend Product Card
            $table->text('product_card_bg')->nullable();
            $table->text('product_card_border')->nullable();
            $table->text('product_card_hover_shadow')->nullable();
            $table->text('product_title_text')->nullable();
            $table->text('product_price_text')->nullable();
            $table->text('product_old_price_text')->nullable();
            $table->text('product_discount_badge_bg')->nullable();
            $table->text('product_discount_badge_text')->nullable();
            
            // Frontend Buttons (Shop, Add to Cart, etc.)
            $table->text('shop_button_bg')->nullable();
            $table->text('shop_button_hover_bg')->nullable();
            $table->text('shop_button_text')->nullable();
            $table->text('cart_button_bg')->nullable();
            $table->text('cart_button_hover_bg')->nullable();
            $table->text('cart_button_text')->nullable();
            
            // Frontend Footer
            $table->text('footer_bg')->nullable();
            $table->text('footer_text')->nullable();
            $table->text('footer_heading_text')->nullable();
            $table->text('footer_link_text')->nullable();
            $table->text('footer_link_hover_text')->nullable();
            
            // Frontend Hero/Banner
            $table->text('hero_overlay_bg')->nullable();
            $table->text('hero_title_text')->nullable();
            $table->text('hero_subtitle_text')->nullable();
            $table->text('hero_button_bg')->nullable();
            $table->text('hero_button_hover_bg')->nullable();
            $table->text('hero_button_text')->nullable();
            
            // Frontend Category Badge
            $table->text('category_badge_bg')->nullable();
            $table->text('category_badge_text')->nullable();
            $table->text('category_badge_hover_bg')->nullable();
            
            // Frontend Price & Cart
            $table->text('price_color')->nullable();
            $table->text('sale_price_color')->nullable();
            $table->text('stock_available_text')->nullable();
            $table->text('stock_unavailable_text')->nullable();
            
            // Frontend Search
            $table->text('search_input_bg')->nullable();
            $table->text('search_input_text')->nullable();
            $table->text('search_input_border')->nullable();
            $table->text('search_input_focus_border')->nullable();
            
            // Frontend Rating Stars
            $table->text('rating_star_color')->nullable();
            $table->text('rating_star_empty_color')->nullable();
            
            // Frontend Newsletter
            $table->text('newsletter_bg')->nullable();
            $table->text('newsletter_text')->nullable();
            $table->text('newsletter_button_bg')->nullable();
            $table->text('newsletter_button_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->dropColumn([
                'nav_bg', 'nav_text', 'nav_hover_bg', 'nav_hover_text', 'nav_active_text',
                'product_card_bg', 'product_card_border', 'product_card_hover_shadow',
                'product_title_text', 'product_price_text', 'product_old_price_text',
                'product_discount_badge_bg', 'product_discount_badge_text',
                'shop_button_bg', 'shop_button_hover_bg', 'shop_button_text',
                'cart_button_bg', 'cart_button_hover_bg', 'cart_button_text',
                'footer_bg', 'footer_text', 'footer_heading_text',
                'footer_link_text', 'footer_link_hover_text',
                'hero_overlay_bg', 'hero_title_text', 'hero_subtitle_text',
                'hero_button_bg', 'hero_button_hover_bg', 'hero_button_text',
                'category_badge_bg', 'category_badge_text', 'category_badge_hover_bg',
                'price_color', 'sale_price_color',
                'stock_available_text', 'stock_unavailable_text',
                'search_input_bg', 'search_input_text', 'search_input_border', 'search_input_focus_border',
                'rating_star_color', 'rating_star_empty_color',
                'newsletter_bg', 'newsletter_text', 'newsletter_button_bg', 'newsletter_button_text',
            ]);
        });
    }
};
