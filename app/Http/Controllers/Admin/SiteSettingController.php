<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * ModuleName: Site Settings Controller
 * Purpose: Manage site-wide settings in admin panel
 * 
 * @category Controllers
 * @package  App\Http\Controllers\Admin
 * @created  2025-11-11
 */
class SiteSettingController extends Controller
{
    /**
     * Display site settings
     */
    public function index()
    {
        $settings = SiteSetting::getAllGrouped();

        // Filter out internal groups that are managed elsewhere
        // Use forget() to remove specific groups from the collection
        $settings->forget('internal_section_control');
        $settings->forget('feedback_sites'); // YouTube settings managed in Feedback > YouTube Imports

        // Get authors for homepage settings
        $authors = User::where('role', 'author')
            ->orWhereHas('authorProfile')
            ->with('authorProfile')
            ->orderBy('name')
            ->get();

        // Get homepage types from config
        $homepageTypes = config('homepage.types', []);

        return view('admin.site-settings.index', compact('settings', 'authors', 'homepageTypes'));
    }

    /**
     * Update site settings
     */
    public function update(Request $request, ImageCompressionService $imageService)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = SiteSetting::where('key', $key)->first();

            if ($setting) {
                // Handle image uploads with WebP compression
                if ($setting->type === 'image' && $request->hasFile("settings.{$key}")) {
                    // Delete old image
                    if ($setting->value && !filter_var($setting->value, FILTER_VALIDATE_URL)) {
                        Storage::disk('public')->delete($setting->value);
                    }

                    // Compress and store as WebP
                    $path = $imageService->compressAndStore(
                        $request->file("settings.{$key}"),
                        'site-settings',
                        'public'
                    );
                    $value = $path;
                }
                // Handle boolean values
                elseif ($setting->type === 'boolean') {
                    $value = $request->has("settings.{$key}") ? '1' : '0';
                }
                // Handle textarea and text
                else {
                    $value = $value ?? '';
                }

                $setting->update(['value' => $value]);
            }
        }

        SiteSetting::clearCache();

        return redirect()->route('admin.site-settings.index')
            ->with('success', 'Site settings updated successfully!');
    }

    /**
     * Update specific group settings
     */
    public function updateGroup(Request $request, string $group, ImageCompressionService $imageService)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = SiteSetting::where('key', $key)
                ->where('group', $group)
                ->first();

            if ($setting) {
                // Handle image uploads with WebP compression
                if ($setting->type === 'image' && $request->hasFile("settings.{$key}")) {
                    // Delete old image
                    if ($setting->value && !filter_var($setting->value, FILTER_VALIDATE_URL)) {
                        Storage::disk('public')->delete($setting->value);
                    }

                    // Compress and store as WebP
                    $path = $imageService->compressAndStore(
                        $request->file("settings.{$key}"),
                        'site-settings',
                        'public'
                    );
                    $value = $path;
                }
                // Handle boolean values
                elseif ($setting->type === 'boolean') {
                    $value = $request->has("settings.{$key}") ? '1' : '0';
                }
                // Handle textarea and text
                else {
                    $value = $value ?? '';
                }

                $setting->update(['value' => $value]);
            }
        }

        SiteSetting::clearCache();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => ucfirst(str_replace('_', ' ', $group)) . ' settings updated successfully!'
            ]);
        }

        return redirect()->route('admin.site-settings.index')
            ->with('success', ucfirst(str_replace('_', ' ', $group)) . ' settings updated successfully!');
    }

    /**
     * Remove logo image
     */
    public function removeLogo(Request $request)
    {
        $key = $request->input('key');
        $setting = SiteSetting::where('key', $key)->first();

        if ($setting && $setting->type === 'image' && $setting->value) {
            // Delete the image file
            if (!filter_var($setting->value, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($setting->value);
            }

            // Clear the value
            $setting->update(['value' => null]);
            SiteSetting::clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Logo removed successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Logo not found!'
        ], 404);
    }
}
