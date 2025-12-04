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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('payment_method', 255);
            $table->string('transaction_id', 255)->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending','processing','completed','failed','refunded'])->default('pending');
            $table->longText('gateway_response')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->string('refund_transaction_id', 255)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('refund_reason')->nullable();
            $table->string('payer_name', 255)->nullable();
            $table->string('payer_email', 255)->nullable();
            $table->string('payer_phone', 255)->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['order_id']);
            $table->index(['status']);
            $table->index(['transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
