<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterSetting;
use App\Models\FooterLink;
use App\Models\FooterBlogPost;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FooterManagementController extends Controller
{
    public function index()
    {
        $settings = FooterSetting::all()->groupBy('group');
        $blogPosts = FooterBlogPost::orderBy('sort_order')->get();

        return view('admin.footer-management.index', compact('settings', 'blogPosts'));
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->except(['_token', 'qr_code_image']) as $key => $value) {
            FooterSetting::where('key', $key)->update(['value' => $value]);
        }

        // Handle QR code image upload with WebP compression
        if ($request->hasFile('qr_code_image')) {
            $imageService = app(ImageCompressionService::class);
            
            // Delete old QR code if exists
            $oldQrCode = FooterSetting::where('key', 'qr_code_image')->first();
            if ($oldQrCode && $oldQrCode->value) {
                Storage::disk('public')->delete($oldQrCode->value);
            }
            
            // Compress and store as WebP
            $path = $imageService->compressAndStore(
                $request->file('qr_code_image'),
                'footer/qr-codes',
                'public'
            );
            
            FooterSetting::where('key', 'qr_code_image')->update(['value' => $path]);
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }


    public function storeBlogPost(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        // Compress and store image as WebP
        if ($request->hasFile('image')) {
            $imageService = app(ImageCompressionService::class);
            $validated['image'] = $imageService->compressAndStore(
                $request->file('image'),
                'footer/blog',
                'public'
            );
        }

        $validated['sort_order'] = FooterBlogPost::max('sort_order') + 1;
        FooterBlogPost::create($validated);

        return redirect()->back()->with('success', 'Blog post added successfully!');
    }

    public function deleteBlogPost(FooterBlogPost $blogPost)
    {
        if ($blogPost->image) {
            Storage::disk('public')->delete($blogPost->image);
        }
        $blogPost->delete();
        return redirect()->back()->with('success', 'Blog post deleted successfully!');
    }

    /**
     * Toggle footer section visibility
     */
    public function toggleSection(Request $request)
    {
        try {
            $validated = $request->validate([
                'section_key' => 'required|string',
                'enabled' => 'required|boolean',
            ]);

            FooterSetting::updateOrCreate(
                ['key' => $validated['section_key']],
                [
                    'value' => $validated['enabled'] ? '1' : '0',
                    'group' => 'footer_sections',
                    'type' => 'boolean',
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Section visibility updated successfully',
                'enabled' => $validated['enabled']
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Footer toggle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
