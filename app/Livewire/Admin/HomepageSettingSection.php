<?php

namespace App\Livewire\Admin;

use App\Models\HomepageSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * HomepageSettingSection Livewire Component
 * Purpose: Handle individual homepage settings sections with independent save functionality
 */
class HomepageSettingSection extends Component
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
            if ($setting['type'] !== 'image') {
                // Convert boolean strings to actual booleans for proper checkbox binding
                if ($setting['type'] === 'boolean') {
                    $this->settings[$setting['key']] = filter_var($setting['value'], FILTER_VALIDATE_BOOLEAN);
                } else {
                    $this->settings[$setting['key']] = $setting['value'];
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
            foreach ($this->groupSettings as $setting) {
                $homepageSetting = HomepageSetting::where('key', $setting['key'])->first();
                
                if (!$homepageSetting) continue;
                
                // Handle image uploads
                if ($setting['type'] === 'image' && isset($this->images[$setting['key']])) {
                    // Delete old image
                    if ($homepageSetting->value && !filter_var($homepageSetting->value, FILTER_VALIDATE_URL)) {
                        Storage::disk('public')->delete($homepageSetting->value);
                    }
                    
                    // Store new image
                    $path = $this->images[$setting['key']]->store('homepage-settings', 'public');
                    $homepageSetting->update(['value' => $path]);
                    
                    // Clear uploaded image from memory
                    unset($this->images[$setting['key']]);
                }
                // Handle boolean values - Livewire removes unchecked checkboxes from array
                elseif ($setting['type'] === 'boolean') {
                    // Default to false if not in settings array (unchecked)
                    $value = !empty($this->settings[$setting['key']]) ? '1' : '0';
                    $homepageSetting->update(['value' => $value]);
                }
                // Handle text and textarea
                else {
                    $value = $this->settings[$setting['key']] ?? '';
                    $homepageSetting->update(['value' => $value]);
                }
            }

            HomepageSetting::clearCache();

            $this->dispatch('setting-saved', [
                'message' => ucfirst(str_replace('_', ' ', $this->group)) . ' settings saved successfully!',
                'type' => 'success'
            ]);

            // Refresh the component data
            $this->groupSettings = HomepageSetting::where('group', $this->group)
                ->orderBy('order')
                ->get()
                ->toArray();

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
            if ($setting['type'] !== 'image') {
                // Convert boolean strings to actual booleans for proper checkbox binding
                if ($setting['type'] === 'boolean') {
                    $this->settings[$setting['key']] = filter_var($setting['value'], FILTER_VALIDATE_BOOLEAN);
                } else {
                    $this->settings[$setting['key']] = $setting['value'];
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
        $setting = HomepageSetting::where('key', $key)->first();
        
        if ($setting && $setting->type === 'image' && $setting->value) {
            // Delete the image file
            if (!filter_var($setting->value, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($setting->value);
            }
            
            // Clear the value
            $setting->update(['value' => null]);
            HomepageSetting::clearCache();
            
            // Refresh the component
            $this->groupSettings = HomepageSetting::where('group', $this->group)
                ->orderBy('order')
                ->get()
                ->toArray();
            
            $this->dispatch('setting-saved', [
                'message' => 'Image removed successfully!',
                'type' => 'success'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.homepage-setting-section');
    }
}
