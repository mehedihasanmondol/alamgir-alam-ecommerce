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
        Schema::table('hero_sliders', function (Blueprint $table) {
            // Add media_id column for media library integration
            $table->unsignedBigInteger('media_id')->nullable()->after('subtitle');
            
            // Add foreign key constraint
            $table->foreign('media_id')
                ->references('id')
                ->on('media_library')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_sliders', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['media_id']);
            $table->dropColumn('media_id');
        });
    }
};
