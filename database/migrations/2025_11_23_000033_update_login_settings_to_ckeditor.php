<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Update login page settings from 'tinymce' to 'ckeditor' type
     */
    public function up(): void
    {
        // Update login_page_content setting type from tinymce to ckeditor
        DB::table('site_settings')
            ->where('key', 'login_page_content')
            ->where('group', 'login')
            ->update(['type' => 'ckeditor']);

        // Update login_terms_conditions setting type from tinymce to ckeditor
        DB::table('site_settings')
            ->where('key', 'login_terms_conditions')
            ->where('group', 'login')
            ->update(['type' => 'ckeditor']);
    }

    /**
     * Reverse the migrations.
     * 
     * Revert back to 'tinymce' type
     */
    public function down(): void
    {
        // Revert login_page_content setting type back to tinymce
        DB::table('site_settings')
            ->where('key', 'login_page_content')
            ->where('group', 'login')
            ->update(['type' => 'tinymce']);

        // Revert login_terms_conditions setting type back to tinymce
        DB::table('site_settings')
            ->where('key', 'login_terms_conditions')
            ->where('group', 'login')
            ->update(['type' => 'tinymce']);
    }
};
