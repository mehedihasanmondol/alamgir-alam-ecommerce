<?php

namespace App\Livewire\Admin\Product;

use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductImage;
use App\Services\ImageService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ImageUploader extends Component
{
    use WithFileUploads;

    public $product;
    public $images = [];
    public $existingImages = [];
    public $primaryImageId = null;
    public $maxUploadSize;
    public $maxUploadSizeFormatted;

    protected function rules()
    {
        $maxSize = ImageService::getMaxUploadSize() / 1024; // Convert to KB for Laravel validation
        
        return [
            'images.*' => "image|max:{$maxSize}",
        ];
    }

    protected function messages()
    {
        return [
            'images.*.image' => 'Each file must be an image (JPEG, PNG, GIF, BMP, or WebP).',
            'images.*.max' => 'Each image must not exceed ' . $this->maxUploadSizeFormatted . '. Your server PHP settings limit uploads to this size.',
        ];
    }

    public function mount($productId = null)
    {
        $this->maxUploadSize = ImageService::getMaxUploadSize();
        $this->maxUploadSizeFormatted = ImageService::getMaxUploadSizeFormatted();
        
        if ($productId) {
            $this->product = Product::findOrFail($productId);
            $this->loadExistingImages();
        }
    }

    public function loadExistingImages()
    {
        $this->existingImages = $this->product->images()
            ->orderBy('sort_order')
            ->get()
            ->toArray();
        
        $primary = $this->product->images()->where('is_primary', true)->first();
        $this->primaryImageId = $primary?->id;
    }

    public function updatedImages()
    {
        $this->validate();
    }

    public function uploadImages()
    {
        $this->validate();

        if (!$this->product) {
            $this->dispatch('error', 'Please save the product first before uploading images.');
            return;
        }

        $sortOrder = $this->product->images()->max('sort_order') ?? 0;
        $uploadedCount = 0;
        $errors = [];

        foreach ($this->images as $index => $image) {
            try {
                // Validate file size against PHP ini limits
                if (!ImageService::validateFileSize($image)) {
                    $errors[] = "Image " . ($index + 1) . " exceeds maximum size of {$this->maxUploadSizeFormatted}";
                    continue;
                }

                // Process and convert to WebP with compression (quality: 85)
                $path = ImageService::processAndStore($image, 'products', 85);
                
                $this->product->images()->create([
                    'image_path' => $path,
                    'thumbnail_path' => $path, // Same path, no separate thumbnail
                    'is_primary' => $this->product->images()->count() === 0,
                    'sort_order' => ++$sortOrder,
                ]);
                
                $uploadedCount++;
            } catch (\Exception $e) {
                $errors[] = "Image " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        $this->images = [];
        $this->loadExistingImages();
        $this->dispatch('images-uploaded');
        
        if ($uploadedCount > 0) {
            session()->flash('success', "{$uploadedCount} image(s) uploaded and converted to WebP successfully!");
        }
        
        if (!empty($errors)) {
            session()->flash('error', implode('. ', $errors));
        }
    }

    public function setPrimary($imageId)
    {
        if (!$this->product) {
            return;
        }

        // Remove primary from all images
        $this->product->images()->update(['is_primary' => false]);
        
        // Set new primary
        $this->product->images()->where('id', $imageId)->update(['is_primary' => true]);
        
        $this->primaryImageId = $imageId;
        $this->loadExistingImages();
        $this->dispatch('primary-updated');
    }

    public function deleteImage($imageId)
    {
        $image = ProductImage::find($imageId);
        
        if ($image && $image->product_id === $this->product->id) {
            // Delete file from storage
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            
            if ($image->thumbnail_path && Storage::disk('public')->exists($image->thumbnail_path)) {
                Storage::disk('public')->delete($image->thumbnail_path);
            }
            
            $image->delete();
            
            // If deleted image was primary, set first image as primary
            if ($image->is_primary && $this->product->images()->count() > 0) {
                $this->product->images()->first()->update(['is_primary' => true]);
            }
            
            $this->loadExistingImages();
            $this->dispatch('image-deleted');
            session()->flash('success', 'Image deleted successfully!');
        }
    }

    public function updateSortOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            ProductImage::where('id', $id)
                ->where('product_id', $this->product->id)
                ->update(['sort_order' => $index + 1]);
        }
        
        $this->loadExistingImages();
        $this->dispatch('sort-updated');
    }

    public function render()
    {
        return view('livewire.admin.product.image-uploader');
    }
}
