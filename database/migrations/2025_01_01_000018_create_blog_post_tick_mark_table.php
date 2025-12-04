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
        Schema::create('blog_post_tick_mark', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_post_id')->constrained('blog_posts')->onDelete('cascade');
            $table->foreignId('blog_tick_mark_id')->constrained('blog_tick_marks')->onDelete('cascade');
            $table->bigInteger('added_by')->nullable()->unsigned();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['blog_post_id']);
            $table->index(['blog_tick_mark_id']);
            $table->unique(['blog_post_id', 'blog_tick_mark_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_post_tick_mark');
    }
};
