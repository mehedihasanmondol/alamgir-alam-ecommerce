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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 255);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('customer_name', 255);
            $table->string('customer_email', 255);
            $table->string('customer_phone', 255);
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('coupon_code', 255)->nullable();
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->enum('payment_method', ['cod','bkash','nagad','rocket','card','bank'])->default('cod');
            $table->bigInteger('payment_gateway_id')->nullable()->unsigned();
            $table->enum('payment_status', ['pending','paid','failed','refunded'])->default('pending');
            $table->string('transaction_id', 255)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->enum('status', ['pending','processing','confirmed','shipped','delivered','cancelled','refunded'])->default('pending');
            $table->string('tracking_number', 255)->nullable();
            $table->string('carrier', 255)->nullable();
            $table->foreignId('delivery_method_id')->nullable()->constrained('delivery_methods')->onDelete('set null');
            $table->foreignId('delivery_zone_id')->nullable()->constrained('delivery_zones')->onDelete('set null');
            $table->string('delivery_method_name', 255)->nullable();
            $table->string('delivery_zone_name', 255)->nullable();
            $table->string('estimated_delivery', 255)->nullable();
            $table->decimal('base_shipping_cost', 10, 2)->default(0.00);
            $table->decimal('handling_fee', 10, 2)->default(0.00);
            $table->decimal('insurance_fee', 10, 2)->default(0.00);
            $table->decimal('cod_fee', 10, 2)->default(0.00);
            $table->enum('delivery_status', ['pending','processing','picked_up','in_transit','out_for_delivery','delivered','failed','returned'])->default('pending');
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('in_transit_at')->nullable();
            $table->timestamp('out_for_delivery_at')->nullable();
            $table->string('courier_name', 255)->nullable();
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('coupon_discount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->string('ip_address', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['created_at']);
            $table->index(['delivery_method_id']);
            $table->index(['delivery_status']);
            $table->index(['delivery_zone_id']);
            $table->index(['order_number']);
            $table->index(['payment_status']);
            $table->index(['status']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
