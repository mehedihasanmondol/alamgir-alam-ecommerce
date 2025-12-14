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
        Schema::table('feedback', function (Blueprint $table) {
            // Source tracking: where the feedback came from (manual, youtube, facebook, etc.)
            $table->string('source')->default('manual')->after('feedback');

            // External reference ID for deduplication (e.g., YouTube comment ID)
            $table->string('source_reference_id')->nullable()->unique()->after('source');

            // JSON metadata for source-specific information (video title, URL, etc.)
            $table->json('source_metadata')->nullable()->after('source_reference_id');

            // Add index for efficient filtering by source
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn(['source', 'source_reference_id', 'source_metadata']);
        });
    }
};
