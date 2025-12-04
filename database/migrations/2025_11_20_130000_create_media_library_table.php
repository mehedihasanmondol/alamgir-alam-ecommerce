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
        Schema::create('media_library', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('original_filename');
            $table->string('filename'); // stored filename
            $table->string('mime_type');
            $table->string('extension', 10);
            $table->unsignedInteger('size'); // bytes
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->decimal('aspect_ratio', 10, 4)->nullable();
            $table->string('disk', 20)->default('public');
            $table->string('path'); // storage path
            $table->string('large_path')->nullable(); // Large size path
            $table->string('medium_path')->nullable(); // Medium size path
            $table->string('small_path')->nullable(); // Small size path
            $table->json('metadata')->nullable(); // Additional metadata
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->enum('scope', ['user', 'global'])->default('global');
            $table->timestamps();
            
            $table->index(['user_id', 'scope']);
            $table->index('mime_type');
            $table->index('created_at');
        });

        Schema::create('image_upload_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('text'); // text, number, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_library');
        Schema::dropIfExists('image_upload_settings');
    }
};
