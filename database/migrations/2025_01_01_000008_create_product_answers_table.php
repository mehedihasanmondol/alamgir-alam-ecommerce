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
        Schema::create('product_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('product_questions')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('answer');
            $table->boolean('is_best_answer')->default(false);
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_rewarded')->default(false);
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            $table->string('user_name', 255)->nullable();
            $table->string('user_email', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['created_at']);
            $table->index(['is_best_answer']);
            $table->index(['question_id']);
            $table->index(['status']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_answers');
    }
};
