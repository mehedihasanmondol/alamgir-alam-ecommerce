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
        Schema::create('promotional_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('subtitle')->nullable();
            $table->dateTime('countdown_end')->nullable();
            $table->string('background_color', 255)->default('#16a34a');
            $table->string('text_color', 255)->default('#ffffff');
            $table->string('link_url', 255)->nullable();
            $table->string('link_text', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('show_countdown')->default(true);
            $table->boolean('is_dismissible')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['is_active']);
            $table->index(['sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotional_banners');
    }
};
