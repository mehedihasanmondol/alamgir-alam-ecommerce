<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Media;
use App\Models\ImageUploadSetting;
use App\Services\ImageService;
use Exception;

class UniversalImageUploader extends Component
{
    use WithFileUploads, WithPagination;

    // Component configuration
    public $multiple = false;
    public $disk = 'public';
    public $maxFileSize = 5; // MB
    public $maxWidth = 4000;
    public $maxHeight = 4000;
    public $preserveOriginal = false;
    public $defaultCompression = 70;
    public $libraryScope = 'global';
    public $targetField; // Field name to update on parent component
    
    // Modal state
    public $showModal = false;
    public $activeTab = 'library'; // library, upload, settings
    
    // Upload tab
    public $uploadedFiles = [];
    public $croppedImages = []; // Store base64 cropped images
    public $currentEditIndex = null;
    
    // Library tab
    public $search = '';
    public $mimeFilter = '';
    public $startDate = '';
    public $endDate = '';
    public $selectedMedia = [];
    public $perPage = 20;
    
    // Settings tab
    public $settingsCompression;
    public $settingsLargeWidth;
    public $settingsLargeHeight;
    public $settingsMediumWidth;
    public $settingsMediumHeight;
    public $settingsSmallWidth;
    public $settingsSmallHeight;
    public $settingsMaxFileSize;
    public $settingsMaxWidth;
    public $settingsMaxHeight;
    public $settingsEnableOptimizer;
    
    protected $queryString = ['search', 'mimeFilter', 'startDate', 'endDate'];
    
    protected $listeners = [
        'open-uploader-modal' => 'openModal',
        'openMediaLibrary' => 'openLibraryModal',
        'openUploader' => 'openUploadModal',
    ];
    
    public function mount(
        $multiple = false,
        $disk = 'public',
        $maxFileSize = 5,
        $maxWidth = 4000,
        $maxHeight = 4000,
        $preserveOriginal = false,
        $defaultCompression = 70,
        $libraryScope = 'global',
        $targetField = null
    ) {
        $this->multiple = $multiple;
        $this->disk = $disk;
        $this->maxFileSize = $maxFileSize;
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        $this->preserveOriginal = $preserveOriginal;
        $this->defaultCompression = $defaultCompression;
        $this->libraryScope = $libraryScope;
        $this->targetField = $targetField;
        
        $this->loadSettings();
    }
    
    public function loadSettings()
    {
        $this->settingsCompression = ImageUploadSetting::get('default_compression', 70);
        $this->settingsLargeWidth = ImageUploadSetting::get('size_large_width', 1920);
        $this->settingsLargeHeight = ImageUploadSetting::get('size_large_height', 1920);
        $this->settingsMediumWidth = ImageUploadSetting::get('size_medium_width', 1200);
        $this->settingsMediumHeight = ImageUploadSetting::get('size_medium_height', 1200);
        $this->settingsSmallWidth = ImageUploadSetting::get('size_small_width', 600);
        $this->settingsSmallHeight = ImageUploadSetting::get('size_small_height', 600);
        $this->settingsMaxFileSize = ImageUploadSetting::get('max_file_size', 5);
        $this->settingsMaxWidth = ImageUploadSetting::get('max_width', 4000);
        $this->settingsMaxHeight = ImageUploadSetting::get('max_height', 4000);
        $this->settingsEnableOptimizer = ImageUploadSetting::get('enable_optimizer', true);
    }
    
    public function openModal()
    {
        $this->showModal = true;
        $this->activeTab = 'library';
        $this->resetPage();
    }
    
    public function openLibraryModal(...$params)
    {
        // Handle Livewire dispatch which can pass object as first param
        if (count($params) > 0 && is_array($params[0])) {
            // Extract field and multiple from array
            $field = $params[0]['field'] ?? null;
            $multiple = $params[0]['multiple'] ?? null;
        } else {
            // Individual parameters
            $field = $params[0] ?? null;
            $multiple = $params[1] ?? null;
        }
        
        // Set configuration
        if ($field) {
            $this->targetField = $field;
        }
        if (!is_null($multiple)) {
            $this->multiple = $multiple;
        }
        
        $this->showModal = true;
        $this->activeTab = 'library';
        $this->resetPage();
    }
    
    public function openUploadModal(...$params)
    {
        // Handle Livewire dispatch which can pass object as first param
        if (count($params) > 0 && is_array($params[0])) {
            // Extract field and multiple from array
            $field = $params[0]['field'] ?? null;
            $multiple = $params[0]['multiple'] ?? null;
        } else {
            // Individual parameters
            $field = $params[0] ?? null;
            $multiple = $params[1] ?? null;
        }
        
        // Set configuration
        if ($field) {
            $this->targetField = $field;
        }
        if (!is_null($multiple)) {
            $this->multiple = $multiple;
        }
        
        $this->showModal = true;
        $this->activeTab = 'upload';
        $this->resetPage();
    }
    
    public function closeModal()
    {
        // If target field is ckeditor_upload, dispatch cancel event
        if ($this->targetField === 'ckeditor_upload') {
            $this->js("window.dispatchEvent(new CustomEvent('ckeditor-upload-cancelled'))");
        }
        
        $this->showModal = false;
        $this->uploadedFiles = [];
        $this->croppedImages = [];
        $this->selectedMedia = [];
        $this->currentEditIndex = null;
    }
    
    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }
    
    public function updatedUploadedFiles()
    {
        // Switch to upload tab when files are added
        $this->activeTab = 'upload';
    }
    
    public function removeUploadedFile($index)
    {
        unset($this->uploadedFiles[$index]);
        unset($this->croppedImages[$index]);
        $this->uploadedFiles = array_values($this->uploadedFiles);
        $this->croppedImages = array_values($this->croppedImages);
    }
    
    public function saveCroppedImage($index, $imageData)
    {
        $this->croppedImages[$index] = $imageData;
        $this->currentEditIndex = null;
    }
    
    public function uploadImages()
    {
        try {
            $uploadedMedia = [];
            
            // Process cropped images if available, otherwise use uploaded files
            $filesToProcess = !empty($this->croppedImages) ? $this->croppedImages : $this->uploadedFiles;
            
            foreach ($filesToProcess as $index => $file) {
                $options = [
                    'disk' => $this->disk,
                    'compression' => $this->defaultCompression,
                    'scope' => $this->libraryScope,
                ];
                
                $media = ImageService::processUniversalUpload($file, $options);
                $uploadedMedia[] = $media;
            }
            
            // Convert media to array for JavaScript
            $mediaArray = collect($uploadedMedia)->map(function($item) {
                return [
                    'id' => $item->id,
                    'filename' => $item->filename,
                    'original_filename' => $item->original_filename,
                    'large_url' => $item->large_url,
                    'medium_url' => $item->medium_url,
                    'small_url' => $item->small_url,
                    'mime_type' => $item->mime_type,
                    'size' => $item->size,
                    'width' => $item->width,
                    'height' => $item->height,
                ];
            })->toArray();
            
            // Emit event to parent component and browser (wrap in array for reliable cross-component dispatch)
            $this->dispatch('imageUploaded', [
                'media' => $mediaArray,
                'field' => $this->targetField,
            ]);
            
            // Also dispatch browser event for Alpine.js with properly formatted data
            $this->js("window.dispatchEvent(new CustomEvent('imageUploaded', { detail: " . json_encode([
                'media' => $mediaArray,
                'field' => $this->targetField,
            ]) . " }))");
            
            session()->flash('success', count($uploadedMedia) . ' image(s) uploaded successfully!');
            
            $this->closeModal();
            $this->reset(['uploadedFiles', 'croppedImages']);
            
        } catch (Exception $e) {
            session()->flash('error', 'Upload failed: ' . $e->getMessage());
        }
    }
    
    public function selectFromLibrary()
    {
        if (empty($this->selectedMedia)) {
            session()->flash('error', 'Please select at least one image.');
            return;
        }
        
        $media = Media::whereIn('id', $this->selectedMedia)->get();
        
        // Convert media to array for JavaScript
        $mediaArray = $media->map(function($item) {
            return [
                'id' => $item->id,
                'filename' => $item->filename,
                'original_filename' => $item->original_filename,
                'large_url' => $item->large_url,
                'medium_url' => $item->medium_url,
                'small_url' => $item->small_url,
                'mime_type' => $item->mime_type,
                'size' => $item->size,
                'width' => $item->width,
                'height' => $item->height,
            ];
        })->toArray();
        
        // Emit event to parent component (wrap in array for reliable cross-component dispatch)
        $this->dispatch('imageSelected', [
            'media' => $mediaArray,
            'field' => $this->targetField,
        ]);
        
        // Also dispatch browser event for Alpine.js with properly formatted data
        $this->js("window.dispatchEvent(new CustomEvent('imageSelected', { detail: " . json_encode([
            'media' => $mediaArray,
            'field' => $this->targetField,
        ]) . " }))");
        
        session()->flash('success', count($media) . ' image(s) selected!');
        
        $this->closeModal();
        $this->selectedMedia = [];
    }
    
    public function toggleMediaSelection($mediaId)
    {
        if (in_array($mediaId, $this->selectedMedia)) {
            $this->selectedMedia = array_diff($this->selectedMedia, [$mediaId]);
        } else {
            if ($this->multiple) {
                $this->selectedMedia[] = $mediaId;
            } else {
                $this->selectedMedia = [$mediaId];
            }
        }
    }
    
    public function deleteMedia($mediaId)
    {
        try {
            $media = Media::findOrFail($mediaId);
            
            // Check permission (only delete own images or if admin)
            if ($media->user_id !== auth()->id() && !auth()->user()->is_admin) {
                session()->flash('error', 'You do not have permission to delete this image.');
                return;
            }
            
            ImageService::deleteMedia($media);
            session()->flash('success', 'Image deleted successfully!');
            
        } catch (Exception $e) {
            session()->flash('error', 'Failed to delete image: ' . $e->getMessage());
        }
    }
    
    public function saveSettings()
    {
        try {
            ImageUploadSetting::set('default_compression', $this->settingsCompression, 'number');
            ImageUploadSetting::set('size_large_width', $this->settingsLargeWidth, 'number');
            ImageUploadSetting::set('size_large_height', $this->settingsLargeHeight, 'number');
            ImageUploadSetting::set('size_medium_width', $this->settingsMediumWidth, 'number');
            ImageUploadSetting::set('size_medium_height', $this->settingsMediumHeight, 'number');
            ImageUploadSetting::set('size_small_width', $this->settingsSmallWidth, 'number');
            ImageUploadSetting::set('size_small_height', $this->settingsSmallHeight, 'number');
            ImageUploadSetting::set('max_file_size', $this->settingsMaxFileSize, 'number');
            ImageUploadSetting::set('max_width', $this->settingsMaxWidth, 'number');
            ImageUploadSetting::set('max_height', $this->settingsMaxHeight, 'number');
            ImageUploadSetting::set('enable_optimizer', $this->settingsEnableOptimizer, 'boolean');
            
            session()->flash('success', 'Settings saved successfully!');
            
        } catch (Exception $e) {
            session()->flash('error', 'Failed to save settings: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        $mediaQuery = Media::query()
            ->when($this->libraryScope === 'user', function ($query) {
                $query->forUser(auth()->id());
            })
            ->when($this->libraryScope === 'global', function ($query) {
                $query->global();
            })
            ->search($this->search)
            ->byMimeType($this->mimeFilter)
            ->dateRange($this->startDate, $this->endDate)
            ->latest();
        
        $mediaLibrary = $mediaQuery->paginate($this->perPage);
        
        $aspectRatioPresets = ImageUploadSetting::get('aspect_ratio_presets', []);
        
        return view('livewire.universal-image-uploader', [
            'mediaLibrary' => $mediaLibrary,
            'aspectRatioPresets' => $aspectRatioPresets,
        ]);
    }
}
