<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use App\Models\User;
use App\Services\ImageCompressionService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * SettingSection Livewire Component
 * Purpose: Handle individual settings sections with independent save functionality
 */
class SettingSection extends Component
{
    use WithFileUploads;

    public $group;
    public $groupSettings;
    public $settings = [];
    public $images = [];
    public $loading = false;

    /**
     * Mount component with group data
     */
    public function mount($group, $groupSettings)
    {
        $this->group = $group;
        $this->groupSettings = $groupSettings;
        
        // Initialize settings values
        foreach ($groupSettings as $setting) {
            if ($setting->type !== 'image') {
                // Convert boolean string values to actual boolean for checkboxes
                if ($setting->type === 'boolean') {
                    $this->settings[$setting->key] = $setting->value === '1';
                } else {
                    $this->settings[$setting->key] = $setting->value;
                }
            }
        }
    }

    /**
     * Save settings for this group
     */
    public function save()
    {
        $this->loading = true;

        try {
            $imageService = app(ImageCompressionService::class);
            
            foreach ($this->groupSettings as $setting) {
                // Handle image uploads with WebP compression
                if ($setting->type === 'image') {
                    // Only process if a new image was uploaded for this specific field
                    if (isset($this->images[$setting->key])) {
                        // Delete old image only when uploading a new one
                        if ($setting->value && !filter_var($setting->value, FILTER_VALIDATE_URL)) {
                            Storage::disk('public')->delete($setting->value);
                        }
                        
                        // Compress and store as WebP
                        $path = $imageService->compressAndStore(
                            $this->images[$setting->key],
                            'site-settings',
                            'public'
                        );
                        $setting->update(['value' => $path]);
                        
                        // Clear uploaded image from memory
                        unset($this->images[$setting->key]);
                    }
                    // If no new image uploaded, keep the existing value (don't update)
                    // This prevents clearing other image fields in the same group
                }
                // Handle boolean values
                elseif ($setting->type === 'boolean') {
                    // Livewire checkbox returns boolean true/false
                    $value = !empty($this->settings[$setting->key]) ? '1' : '0';
                    $setting->update(['value' => $value]);
                }
                // Handle tinymce (rich text)
                elseif ($setting->type === 'tinymce') {
                    $value = $this->settings[$setting->key] ?? '';
                    $setting->update(['value' => $value]);
                }
                // Handle ckeditor (rich text)
                elseif ($setting->type === 'ckeditor') {
                    $value = $this->settings[$setting->key] ?? '';
                    $setting->update(['value' => $value]);
                }
                // Handle text and textarea
                else {
                    $value = $this->settings[$setting->key] ?? '';
                    $setting->update(['value' => $value]);
                }
            }

            SiteSetting::clearCache();

            $this->dispatch('setting-saved', [
                'message' => ucfirst(str_replace('_', ' ', $this->group)) . ' settings saved successfully!',
                'type' => 'success'
            ]);

            // Refresh the component data
            $this->groupSettings = SiteSetting::where('group', $this->group)
                ->orderBy('order')
                ->get();

        } catch (\Exception $e) {
            $this->dispatch('setting-saved', [
                'message' => 'Error saving settings: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Reset form to original values
     */
    public function resetForm()
    {
        foreach ($this->groupSettings as $setting) {
            if ($setting->type !== 'image') {
                // Convert boolean string values to actual boolean for checkboxes
                if ($setting->type === 'boolean') {
                    $this->settings[$setting->key] = $setting->value === '1';
                } else {
                    $this->settings[$setting->key] = $setting->value;
                }
            }
        }
        
        $this->images = [];
        
        $this->dispatch('setting-saved', [
            'message' => 'Settings reset to original values',
            'type' => 'info'
        ]);
    }

    /**
     * Remove image
     */
    public function removeImage($key)
    {
        $setting = SiteSetting::where('key', $key)->first();
        
        if ($setting && $setting->type === 'image' && $setting->value) {
            // Delete the image file
            if (!filter_var($setting->value, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($setting->value);
            }
            
            // Clear the value
            $setting->update(['value' => null]);
            SiteSetting::clearCache();
            
            // Refresh the component
            $this->groupSettings = SiteSetting::where('group', $this->group)
                ->orderBy('order')
                ->get();
            
            $this->dispatch('setting-saved', [
                'message' => 'Image removed successfully!',
                'type' => 'success'
            ]);
        }
    }

    public function render()
    {
        $data = [];
        
        // Pass additional data for homepage settings
        if ($this->group === 'homepage') {
            $data['authors'] = User::where('role', 'author')
                ->orWhereHas('authorProfile')
                ->with('authorProfile')
                ->orderBy('name')
                ->get();
            $data['homepageTypes'] = config('homepage.types', []);
        }
        
        return view('livewire.admin.setting-section', $data);
    }
}
