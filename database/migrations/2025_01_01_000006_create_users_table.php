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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255)->nullable();
            $table->string('mobile', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->enum('role', ['admin','customer','author'])->default('customer');
            $table->string('google_id', 255)->nullable();
            $table->string('facebook_id', 255)->nullable();
            $table->string('apple_id', 255)->nullable();
            $table->boolean('keep_signed_in')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('email_order_updates')->default(true);
            $table->boolean('email_promotions')->default(false);
            $table->boolean('email_newsletter')->default(false);
            $table->boolean('email_recommendations')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->string('avatar', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 255)->nullable();
            $table->string('state', 255)->nullable();
            $table->string('country', 255)->nullable();
            $table->string('postal_code', 255)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->foreignId('default_delivery_zone_id')->nullable()->constrained('delivery_zones')->onDelete('set null');
            $table->foreignId('default_delivery_method_id')->nullable()->constrained('delivery_methods')->onDelete('set null');
            $table->timestamps();

            // Indexes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
