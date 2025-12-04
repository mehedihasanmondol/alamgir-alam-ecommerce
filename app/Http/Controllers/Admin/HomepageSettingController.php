<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSetting;
use App\Models\HeroSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageSettingController extends Controller
{
    /**
     * Display homepage settings
     */
    public function index()
    {
        $settings = HomepageSetting::orderBy('group')->orderBy('order')->get()->groupBy('group');
        $sliders = HeroSlider::orderBy('order')->get();

        return view('admin.homepage-settings.index', compact('settings', 'sliders'));
    }

    /**
     * Update homepage settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = HomepageSetting::where('key', $key)->first();
            
            if ($setting) {
                if ($setting->type === 'image' && $request->hasFile("settings.{$key}")) {
                    // Delete old image
                    if ($setting->value && !filter_var($setting->value, FILTER_VALIDATE_URL)) {
                        Storage::delete($setting->value);
                    }
                    
                    // Store new image
                    $path = $request->file("settings.{$key}")->store('homepage', 'public');
                    $value = $path;
                } elseif ($setting->type === 'json') {
                    $value = json_encode($value);
                }

                $setting->update(['value' => $value]);
            }
        }

        HomepageSetting::clearCache();

        return redirect()->route('admin.homepage-settings.index')
            ->with('success', 'Homepage settings updated successfully!');
    }

    /**
     * Update specific group settings
     */
    public function updateGroup(Request $request, string $group)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = HomepageSetting::where('key', $key)
                ->where('group', $group)
                ->first();
            
            if ($setting) {
                // Handle image uploads
                if ($setting->type === 'image' && $request->hasFile("settings.{$key}")) {
                    // Delete old image
                    if ($setting->value && !filter_var($setting->value, FILTER_VALIDATE_URL)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    
                    // Store new image
                    $path = $request->file("settings.{$key}")->store('homepage-settings', 'public');
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

        HomepageSetting::clearCache();

        return redirect()->route('admin.homepage-settings.index')
            ->with('success', ucfirst(str_replace('_', ' ', $group)) . ' settings updated successfully!');
    }

    /**
     * Store new slider
     */
    public function storeSlider(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'nullable|url',
            'button_text' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('sliders', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? HeroSlider::max('order') + 1;

        HeroSlider::create($validated);

        return redirect()->route('admin.homepage-settings.index')
            ->with('success', 'Slider added successfully!');
    }

    /**
     * Update slider
     */
    public function updateSlider(Request $request, HeroSlider $slider)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'nullable|url',
            'button_text' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($slider->image && !filter_var($slider->image, FILTER_VALIDATE_URL)) {
                Storage::delete($slider->image);
            }
            
            $validated['image'] = $request->file('image')->store('sliders', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $slider->update($validated);

        return redirect()->route('admin.homepage-settings.index')
            ->with('success', 'Slider updated successfully!');
    }

    /**
     * Delete slider
     */
    public function destroySlider(HeroSlider $slider)
    {
        $slider->delete();

        return redirect()->route('admin.homepage-settings.index')
            ->with('success', 'Slider deleted successfully!');
    }

    /**
     * Reorder sliders
     */
    public function reorderSliders(Request $request)
    {
        $validated = $request->validate([
            'sliders' => 'required|array',
            'sliders.*.id' => 'required|exists:hero_sliders,id',
            'sliders.*.order' => 'required|integer',
        ]);

        foreach ($validated['sliders'] as $sliderData) {
            HeroSlider::where('id', $sliderData['id'])->update(['order' => $sliderData['order']]);
        }

        return response()->json(['success' => true, 'message' => 'Sliders reordered successfully!']);
    }
}
