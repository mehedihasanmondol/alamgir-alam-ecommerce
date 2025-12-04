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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->string('reviewer_name', 255)->nullable();
            $table->string('reviewer_email', 255)->nullable();
            $table->tinyInteger('rating')->unsigned();
            $table->string('title', 255)->nullable();
            $table->text('review');
            $table->longText('images')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->bigInteger('approved_by')->nullable()->unsigned();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['is_verified_purchase']);
            $table->index(['product_id', 'status']);
            $table->index(['rating']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
