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
        Schema::create('question_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('product_questions')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('session_id', 255)->nullable();
            $table->enum('vote_type', ['helpful','not_helpful']);
            $table->timestamps();

            // Indexes
            $table->index(['question_id']);
            $table->unique(['question_id', 'session_id', 'vote_type']);
            $table->unique(['question_id', 'user_id', 'vote_type']);
            $table->index(['session_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_votes');
    }
};
