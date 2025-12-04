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
        Schema::create('review_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('product_reviews')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('session_id', 255)->nullable();
            $table->enum('vote_type', ['helpful','not_helpful']);
            $table->timestamps();

            // Indexes
            $table->index(['review_id']);
            $table->unique(['review_id', 'session_id', 'vote_type']);
            $table->unique(['review_id', 'user_id', 'vote_type']);
            $table->index(['session_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_votes');
    }
};
