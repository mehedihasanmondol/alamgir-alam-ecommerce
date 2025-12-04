<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Populate slug for existing author profiles
     */
    public function up(): void
    {
        $authorProfiles = DB::table('author_profiles')
            ->join('users', 'author_profiles.user_id', '=', 'users.id')
            ->select('author_profiles.id', 'author_profiles.slug', 'users.name')
            ->get();

        foreach ($authorProfiles as $profile) {
            // Only generate slug if it's empty
            if (empty($profile->slug)) {
                $slug = Str::slug($profile->name);
                $originalSlug = $slug;
                $count = 1;

                // Check for uniqueness
                while (DB::table('author_profiles')->where('slug', $slug)->where('id', '!=', $profile->id)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }

                DB::table('author_profiles')
                    ->where('id', $profile->id)
                    ->update(['slug' => $slug]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert slugs
    }
};
