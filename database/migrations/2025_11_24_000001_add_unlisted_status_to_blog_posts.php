<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the status enum to include 'unlisted'
        DB::statement("ALTER TABLE blog_posts MODIFY COLUMN status ENUM('draft', 'published', 'scheduled', 'unlisted') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        // First, update any 'unlisted' posts to 'draft'
        DB::table('blog_posts')->where('status', 'unlisted')->update(['status' => 'draft']);
        
        // Then modify the enum back
        DB::statement("ALTER TABLE blog_posts MODIFY COLUMN status ENUM('draft', 'published', 'scheduled') DEFAULT 'draft'");
    }
};
