<?php

namespace App\Livewire\Admin\Blog;

use App\Models\Media;
use Livewire\Component;

class PostImageHandler extends Component
{
    public $post;
    public $media_id;
    public $selectedImage = null;

    protected $listeners = [
        'imageSelected' => 'handleImageSelected',
        'imageUploaded' => 'handleImageUploaded',
    ];

    public function mount($post = null)
    {
        $this->post = $post;
        
        // Load existing image from media library
        if ($post && $post->media_id) {
            $media = Media::find($post->media_id);
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

        if (!$media || !$field || $field !== 'post_featured_image') {
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

    public function handleImageUploaded($data = [])
    {
        $media = $data['media'] ?? null;
        $field = $data['field'] ?? null;

        if (!$media || !$field || $field !== 'post_featured_image') {
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
        return view('livewire.admin.blog.post-image-handler');
    }
}
