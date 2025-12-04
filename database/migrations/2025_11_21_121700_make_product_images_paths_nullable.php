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
        Schema::table('product_images', function (Blueprint $table) {
            // Make image_path and thumbnail_path nullable for media library support
            $table->string('image_path')->nullable()->change();
            $table->string('thumbnail_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            // Revert to non-nullable (but this might fail if null values exist)
            $table->string('image_path')->nullable(false)->change();
            $table->string('thumbnail_path')->nullable(false)->change();
        });
    }
};
