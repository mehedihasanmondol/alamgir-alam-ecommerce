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
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // Theme name (e.g., 'default', 'blue', 'red')
            $table->string('label', 100); // Display name (e.g., 'Default Theme')
            $table->boolean('is_active')->default(false); // Currently active theme
            $table->boolean('is_predefined')->default(true); // Predefined vs custom
            
            // Primary Colors (using TEXT to avoid row size limit)
            $table->text('primary_bg')->nullable();
            $table->text('primary_bg_hover')->nullable();
            $table->text('primary_text')->nullable();
            $table->text('primary_border')->nullable();
            
            // Secondary Colors
            $table->text('secondary_bg')->nullable();
            $table->text('secondary_bg_hover')->nullable();
            $table->text('secondary_text')->nullable();
            $table->text('secondary_border')->nullable();
            
            // Success Colors
            $table->text('success_bg')->nullable();
            $table->text('success_bg_hover')->nullable();
            $table->text('success_text')->nullable();
            $table->text('success_border')->nullable();
            
            // Danger Colors
            $table->text('danger_bg')->nullable();
            $table->text('danger_bg_hover')->nullable();
            $table->text('danger_text')->nullable();
            $table->text('danger_border')->nullable();
            
            // Warning Colors
            $table->text('warning_bg')->nullable();
            $table->text('warning_bg_hover')->nullable();
            $table->text('warning_text')->nullable();
            $table->text('warning_border')->nullable();
            
            // Info Colors
            $table->text('info_bg')->nullable();
            $table->text('info_bg_hover')->nullable();
            $table->text('info_text')->nullable();
            $table->text('info_border')->nullable();
            
            // Button Colors
            $table->text('button_primary_bg')->nullable();
            $table->text('button_primary_bg_hover')->nullable();
            $table->text('button_primary_text')->nullable();
            $table->text('button_primary_border')->nullable();
            
            $table->text('button_secondary_bg')->nullable();
            $table->text('button_secondary_bg_hover')->nullable();
            $table->text('button_secondary_text')->nullable();
            $table->text('button_secondary_border')->nullable();
            
            // Card Colors
            $table->text('card_bg')->nullable();
            $table->text('card_text')->nullable();
            $table->text('card_border')->nullable();
            $table->text('card_shadow')->nullable();
            
            // Sidebar Colors (Admin)
            $table->text('sidebar_bg')->nullable();
            $table->text('sidebar_text')->nullable();
            $table->text('sidebar_active_bg')->nullable();
            $table->text('sidebar_active_text')->nullable();
            $table->text('sidebar_hover_bg')->nullable();
            
            // Header Colors
            $table->text('header_bg')->nullable();
            $table->text('header_text')->nullable();
            $table->text('header_border')->nullable();
            
            // Link Colors
            $table->text('link_text')->nullable();
            $table->text('link_hover_text')->nullable();
            $table->text('link_underline')->nullable();
            
            // Badge Colors
            $table->text('badge_primary_bg')->nullable();
            $table->text('badge_primary_text')->nullable();
            $table->text('badge_success_bg')->nullable();
            $table->text('badge_success_text')->nullable();
            $table->text('badge_danger_bg')->nullable();
            $table->text('badge_danger_text')->nullable();
            $table->text('badge_warning_bg')->nullable();
            $table->text('badge_warning_text')->nullable();
            
            // Input/Form Colors
            $table->text('input_bg')->nullable();
            $table->text('input_text')->nullable();
            $table->text('input_border')->nullable();
            $table->text('input_focus_border')->nullable();
            $table->text('input_focus_ring')->nullable();
            
            // Table Colors
            $table->text('table_header_bg')->nullable();
            $table->text('table_header_text')->nullable();
            $table->text('table_row_hover')->nullable();
            $table->text('table_border')->nullable();
            
            // Modal Colors
            $table->text('modal_bg')->nullable();
            $table->text('modal_text')->nullable();
            $table->text('modal_overlay_bg')->nullable();
            $table->text('modal_overlay_opacity')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};
