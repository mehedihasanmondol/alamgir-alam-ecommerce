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
        Schema::table('blog_categories', function (Blueprint $table) {
            // Add media_id for new media library system
            $table->unsignedBigInteger('media_id')->nullable()->after('parent_id');
            
            // Make image_path nullable for media library support
            $table->string('image_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_categories', function (Blueprint $table) {
            // Remove media_id column
            $table->dropColumn('media_id');
            
            // Revert image_path to non-nullable (might fail if null values exist)
            $table->string('image_path')->nullable(false)->change();
        });
    }
};
