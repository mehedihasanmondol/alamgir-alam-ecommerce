<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing category_id data to category_product pivot table
        DB::statement('
            INSERT INTO category_product (category_id, product_id, created_at, updated_at)
            SELECT category_id, id, NOW(), NOW()
            FROM products
            WHERE category_id IS NOT NULL
            AND NOT EXISTS (
                SELECT 1 FROM category_product 
                WHERE category_product.product_id = products.id 
                AND category_product.category_id = products.category_id
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation needed
        // We don't want to delete the pivot table data
    }
};
