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
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->enum('type', ['select','color','button','image'])->default('select');
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_variation')->default(true);
            $table->integer('position')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['is_variation']);
            $table->index(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
