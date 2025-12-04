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
        Schema::create('delivery_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_zone_id')->constrained('delivery_zones')->onDelete('cascade');
            $table->foreignId('delivery_method_id')->constrained('delivery_methods')->onDelete('cascade');
            $table->decimal('base_rate', 10, 2)->default(0.00);
            $table->decimal('weight_from', 10, 2)->nullable();
            $table->decimal('weight_to', 10, 2)->nullable();
            $table->decimal('rate_per_kg', 10, 2)->nullable();
            $table->decimal('price_from', 10, 2)->nullable();
            $table->decimal('price_to', 10, 2)->nullable();
            $table->decimal('rate_percentage', 5, 2)->nullable();
            $table->integer('item_from')->nullable();
            $table->integer('item_to')->nullable();
            $table->decimal('rate_per_item', 10, 2)->nullable();
            $table->decimal('handling_fee', 10, 2)->default(0.00);
            $table->decimal('insurance_fee', 10, 2)->default(0.00);
            $table->decimal('cod_fee', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['delivery_zone_id', 'delivery_method_id']);
            $table->index(['is_active']);
            $table->unique(['delivery_zone_id', 'delivery_method_id', 'weight_from', 'price_from', 'item_from'], 'delivery_rates_b554ec1cba_unq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_rates');
    }
};
