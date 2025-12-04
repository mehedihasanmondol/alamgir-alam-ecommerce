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
        // Drop existing unique constraint on email if it exists
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['email']);
            });
        } catch (\Exception $e) {
            // Constraint might not exist, continue
        }

        // Add unique constraint that allows multiple NULL values
        // In MySQL, NULL values are not considered duplicates in unique indexes
        Schema::table('users', function (Blueprint $table) {
            $table->unique('email');
        });

        // Update any existing empty string emails to NULL
        DB::table('users')
            ->where('email', '')
            ->update(['email' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert as we're just fixing the constraint
    }
};
