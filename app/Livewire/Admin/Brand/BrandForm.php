<?php

namespace App\Livewire\Admin\Brand;

use App\Models\Media;
use App\Modules\Ecommerce\Product\Models\Brand;
use Livewire\Component;

class BrandForm extends Component
{
    public $brand;
    public $media_id;
    public $selectedImage = null;

    protected $listeners = [
        'imageSelected' => 'handleImageSelected',
        'imageUploaded' => 'handleImageUploaded',
    ];

    public function mount($brand = null)
    {
        $this->brand = $brand;
        
        // Load existing image from media library
        if ($brand && $brand->media_id) {
            $media = Media::find($brand->media_id);
            if ($media) {
                $this->media_id = $media->id;
                $this->selectedImage = [
                    'media_id' => $media->id,
                    'url' => $media->large_url,
                    'thumbnail_url' => $media->small_url,
                ];
            }
        }
    }

    public function handleImageSelected($data = [])
    {
        $media = $data['media'] ?? null;
        $field = $data['field'] ?? null;

        if (!$media || !$field || $field !== 'brand_image') {
            $this->js("console.log('handleImageSelected: Invalid data or field mismatch')");
            return;
        }

        // Only take first image (single image)
        $mediaItem = is_array($media) ? $media[0] : $media;
        
        $this->media_id = $mediaItem['id'];
        $this->selectedImage = [
            'media_id' => $mediaItem['id'],
            'url' => $mediaItem['medium_url'] ?? $mediaItem['large_url'],
            'thumbnail_url' => $mediaItem['small_url'] ?? $mediaItem['medium_url'],
        ];
        
        $this->js("console.log('handleImageSelected: media_id set to ' + {$this->media_id})");
    }

    public function handleImageUploaded($data = [])
    {
        $media = $data['media'] ?? null;
        $field = $data['field'] ?? null;

        if (!$media || !$field || $field !== 'brand_image') {
            return;
        }

        // Only take first image (single image)
        $mediaItem = is_array($media) ? $media[0] : $media;
        
        $this->media_id = $mediaItem['id'];
        $this->selectedImage = [
            'media_id' => $mediaItem['id'],
            'url' => $mediaItem['medium_url'] ?? $mediaItem['large_url'],
            'thumbnail_url' => $mediaItem['small_url'] ?? $mediaItem['medium_url'],
        ];
    }

    public function removeImage()
    {
        $this->media_id = null;
        $this->selectedImage = null;
    }

    public function render()
    {
        return view('livewire.admin.brand.brand-form');
    }
}
