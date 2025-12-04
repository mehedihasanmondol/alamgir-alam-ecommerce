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
        // Modify the role enum to include 'author'
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin', 'customer', 'author') DEFAULT 'customer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'author' from the enum (only if no users have this role)
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin', 'customer') DEFAULT 'customer'");
    }
};
