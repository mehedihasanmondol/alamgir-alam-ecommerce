<?php

namespace App\Livewire\Admin\SystemSettings;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;

class ConstructionMode extends Component
{
    use WithFileUploads;

    public $maintenanceMode;
    public $maintenanceTitle;
    public $maintenanceMessage;
    public $maintenanceImage;
    public $currentImage;
    public $successMessage = '';

    public function mount()
    {
        $this->maintenanceMode = SystemSetting::get('maintenance_mode', false);
        $this->maintenanceTitle = SystemSetting::get('maintenance_title', 'We\'ll be back soon!');
        $this->maintenanceMessage = SystemSetting::get('maintenance_message', 'Sorry for the inconvenience.');
        $this->currentImage = SystemSetting::get('maintenance_image', '');
    }

    public function toggleMaintenanceMode()
    {
        $this->maintenanceMode = !$this->maintenanceMode;
        SystemSetting::set('maintenance_mode', $this->maintenanceMode ? '1' : '0', 'boolean', 'maintenance');
        
        $this->successMessage = $this->maintenanceMode 
            ? 'Maintenance mode enabled successfully!' 
            : 'Maintenance mode disabled successfully!';
        
        $this->dispatch('maintenance-toggled', enabled: $this->maintenanceMode);
    }

    public function updateSettings()
    {
        $this->validate([
            'maintenanceTitle' => 'required|string|max:255',
            'maintenanceMessage' => 'required|string',
            'maintenanceImage' => 'nullable|image|max:2048', // 2MB max
        ]);

        // Handle image upload
        if ($this->maintenanceImage) {
            // Delete old image
            if ($this->currentImage) {
                Storage::disk('public')->delete($this->currentImage);
            }

            // Store new image
            $path = $this->maintenanceImage->store('maintenance', 'public');
            SystemSetting::set('maintenance_image', $path, 'file', 'maintenance');
            $this->currentImage = $path;
        }

        SystemSetting::set('maintenance_title', $this->maintenanceTitle, 'text', 'maintenance');
        SystemSetting::set('maintenance_message', $this->maintenanceMessage, 'text', 'maintenance');

        $this->successMessage = 'Maintenance settings updated successfully!';
        $this->dispatch('settings-updated');
        $this->maintenanceImage = null; // Reset file input
    }

    public function removeImage()
    {
        if ($this->currentImage) {
            Storage::disk('public')->delete($this->currentImage);
            SystemSetting::set('maintenance_image', '', 'file', 'maintenance');
            $this->currentImage = '';
            $this->successMessage = 'Image removed successfully!';
        }
    }

    public function render()
    {
        return view('livewire.admin.system-settings.construction-mode');
    }
}
