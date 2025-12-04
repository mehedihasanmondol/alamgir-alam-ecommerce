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
        Schema::create('delivery_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('code', 255);
            $table->text('description')->nullable();
            $table->string('estimated_days', 255)->nullable();
            $table->integer('min_days')->nullable();
            $table->integer('max_days')->nullable();
            $table->string('carrier_name', 255)->nullable();
            $table->string('carrier_code', 255)->nullable();
            $table->string('tracking_url', 255)->nullable();
            $table->enum('calculation_type', ['flat_rate','weight_based','price_based','item_based','free'])->default('flat_rate');
            $table->decimal('free_shipping_threshold', 10, 2)->nullable();
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->decimal('max_order_amount', 10, 2)->nullable();
            $table->decimal('max_weight', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('show_on_checkout')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('icon', 255)->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['code']);
            $table->index(['is_active']);
            $table->index(['sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_methods');
    }
};
