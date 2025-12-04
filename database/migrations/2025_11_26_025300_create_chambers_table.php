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
        Schema::create('chambers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            
            // Operating hours (JSON format)
            // Example: {"sunday": {"open": "09:00", "close": "17:00", "is_open": true}, ...}
            $table->json('operating_hours')->nullable();
            
            // Closed days (JSON array of day names or specific dates)
            // Example: ["friday", "2025-12-25", "2025-01-01"]
            $table->json('closed_days')->nullable();
            
            // Time slot settings
            $table->integer('slot_duration')->default(30); // minutes
            $table->integer('break_start')->nullable(); // minutes from start (e.g., 720 = 12:00 PM)
            $table->integer('break_duration')->nullable(); // minutes
            
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chambers');
    }
};
