<?php

namespace App\Modules\Contact\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use App\Models\ContactFaq;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ContactSettingController extends Controller
{
    /**
     * Display contact settings and FAQs
     */
    public function index(): View
    {
        $settings = ContactSetting::orderBy('group')->orderBy('order')->get()->groupBy('group');
        $faqs = ContactFaq::ordered()->get();
        
        return view('admin.contact.settings.index', compact('settings', 'faqs'));
    }

    /**
     * Update contact settings
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string',
            'seo_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max
        ]);

        try {
            // Handle image upload for SEO image
            if ($request->hasFile('seo_image')) {
                // Delete old image if exists
                $oldImage = ContactSetting::get('seo_image');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
                
                // Upload new image with WebP compression
                $path = ImageService::processAndStore(
                    $request->file('seo_image'),
                    'contact/seo',
                    85 // Quality: 85 for good balance
                );
                
                // Update SEO image setting
                ContactSetting::where('key', 'seo_image')->update(['value' => $path]);
            }

            // Update other settings
            foreach ($validated['settings'] as $key => $value) {
                ContactSetting::where('key', $key)->update(['value' => $value]);
            }

            ContactSetting::clearCache();

            return redirect()
                ->route('admin.contact.settings.index')
                ->with('success', 'Contact settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while updating settings: ' . $e->getMessage());
        }
    }

    /**
     * Store a new FAQ
     */
    public function storeFaq(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        ContactFaq::create($validated);

        return redirect()
            ->route('admin.contact.settings.index')
            ->with('success', 'FAQ added successfully.');
    }

    /**
     * Update an existing FAQ
     */
    public function updateFaq(Request $request, ContactFaq $faq): RedirectResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $faq->update($validated);

        return redirect()
            ->route('admin.contact.settings.index')
            ->with('success', 'FAQ updated successfully.');
    }

    /**
     * Delete a FAQ
     */
    public function destroyFaq(ContactFaq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()
            ->route('admin.contact.settings.index')
            ->with('success', 'FAQ deleted successfully.');
    }

    /**
     * Toggle FAQ status
     */
    public function toggleFaq(ContactFaq $faq): RedirectResponse
    {
        $faq->update(['is_active' => !$faq->is_active]);

        return redirect()
            ->route('admin.contact.settings.index')
            ->with('success', 'FAQ status updated successfully.');
    }
}
