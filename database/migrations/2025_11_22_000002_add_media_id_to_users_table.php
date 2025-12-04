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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('media_id')->nullable()->after('avatar');
            
            // Foreign key to media_library table
            $table->foreign('media_id')
                  ->references('id')
                  ->on('media_library')
                  ->onDelete('set null');
            
            $table->index('media_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['media_id']);
            $table->dropIndex(['media_id']);
            $table->dropColumn('media_id');
        });
    }
};
