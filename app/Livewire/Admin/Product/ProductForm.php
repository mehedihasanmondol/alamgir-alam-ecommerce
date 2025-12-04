<?php

namespace App\Livewire\Admin\Product;

use App\Modules\Ecommerce\Brand\Models\Brand;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Services\ProductService;
use App\Services\ImageService;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductForm extends Component
{
    use WithFileUploads;

    public ?Product $product = null;
    public bool $isEdit = false;
    public bool $isNewProduct = true;

    // Product Info
    public $name = '';
    public $slug = '';
    public $description = '';
    public $short_description = '';
    public $category_ids = []; // Multiple categories
    public $brand_id = '';
    public $product_type = 'simple';
    public $is_featured = false;
    public $is_active = true;
    public $status = 'draft';

    // Affiliate
    public $external_url = '';
    public $button_text = 'Buy Now';

    // SEO
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';

    // Variant (for simple products)
    public $variant = [
        'sku' => '',
        'price' => '',
        'sale_price' => '',
        'cost_price' => '',
        'stock_quantity' => 0,
        'low_stock_alert' => 5,
        'weight' => '',
        'length' => '',
        'width' => '',
        'height' => '',
        'shipping_class' => '',
    ];

    // Images - OLD system (keeping for backward compatibility)
    public $images = [];
    public $existingImages = [];
    public $primaryImageIndex = null;
    
    // Images - NEW media library system
    public $selectedImages = []; // Array of media IDs with metadata
    public $primaryImageMediaId = null;

    // Grouped Products
    public $selectedChildProducts = [];
    public $childProductSearch = '';

    // Variable Products - Temporary Variations
    public $tempVariations = [];

    // UI State
    public $currentStep = 1;
    public $showVariantSection = false;
    
    // Quick Add Modals
    public $showCategoryModal = false;
    public $showBrandModal = false;
    public $newCategoryName = '';
    public $newBrandName = '';
    
    // Upload size limits
    public $maxUploadSize;
    public $maxUploadSizeFormatted;

    protected $listeners = [
        'variationAdded' => 'addTempVariation',
        'variationUpdated' => 'updateTempVariation',
        'variationDeleted' => 'deleteTempVariation',
        'imageSelected' => 'handleImageSelected',
        'imageUploaded' => 'handleImageUploaded',
        'reorderImages' => 'handleReorderImages',
    ];

    protected function rules()
    {
        $maxSize = ImageService::getMaxUploadSize() / 1024; // Convert to KB for Laravel validation
        
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . ($this->product->id ?? 'NULL'),
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'product_type' => 'required|in:simple,grouped,affiliate,variable',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'images.*' => "nullable|image|max:{$maxSize}", // Dynamic max size from PHP ini
        ];

        if ($this->product_type === 'simple' || $this->product_type === 'grouped') {
            $rules['variant.price'] = 'required|numeric|min:0';
            $rules['variant.sale_price'] = 'nullable|numeric|min:0|lt:variant.price';
            
            // Only require stock validation if manual stock updates are enabled
            $manualStockEnabled = \App\Models\SiteSetting::get('manual_stock_update_enabled', '0') === '1';
            if ($manualStockEnabled) {
                $rules['variant.stock_quantity'] = 'required|integer|min:0';
                $rules['variant.low_stock_alert'] = 'nullable|integer|min:0';
            }
            
            $rules['variant.sku'] = 'nullable|string|max:100';
            $rules['variant.cost_price'] = 'nullable|numeric|min:0';
            $rules['variant.weight'] = 'nullable|numeric|min:0';
            $rules['variant.length'] = 'nullable|numeric|min:0';
            $rules['variant.width'] = 'nullable|numeric|min:0';
            $rules['variant.height'] = 'nullable|numeric|min:0';
        }

        if ($this->product_type === 'affiliate') {
            $rules['external_url'] = 'required|url';
            $rules['button_text'] = 'required|string|max:50';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'variant.price.required' => 'Regular price is required.',
            'variant.sale_price.lt' => 'Sale price must be less than regular price.',
            'variant.stock_quantity.required' => 'Stock quantity is required.',
            'images.*.image' => 'Each file must be an image (JPEG, PNG, GIF, BMP, or WebP).',
            'images.*.max' => 'Each image must not exceed ' . ImageService::getMaxUploadSizeFormatted() . '. Your server PHP settings limit uploads to this size.',
        ];
    }

    public function mount(?Product $product = null)
    {
        // Initialize upload size limits
        $this->maxUploadSize = ImageService::getMaxUploadSize();
        $this->maxUploadSizeFormatted = ImageService::getMaxUploadSizeFormatted();
        
        if ($product && $product->exists) {
            $this->isEdit = true;
            $this->isNewProduct = false;
            $this->product = $product;
            $this->fill([
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'short_description' => $product->short_description,
                'brand_id' => $product->brand_id,
                'product_type' => $product->product_type,
                'is_featured' => $product->is_featured,
                'is_active' => $product->is_active,
                'external_url' => $product->external_url,
                'button_text' => $product->button_text ?? 'Buy Now',
                'meta_title' => $product->meta_title,
                'meta_description' => $product->meta_description,
                'meta_keywords' => $product->meta_keywords,
            ]);
            
            // Load existing categories
            $this->category_ids = $product->categories->pluck('id')->toArray();

            if ($product->product_type === 'simple' || $product->product_type === 'grouped') {
                $defaultVariant = $product->variants->where('is_default', true)->first();
                if ($defaultVariant) {
                    $this->variant = [
                        'sku' => $defaultVariant->sku,
                        'price' => $defaultVariant->price,
                        'sale_price' => $defaultVariant->sale_price,
                        'cost_price' => $defaultVariant->cost_price,
                        'stock_quantity' => $defaultVariant->stock_quantity,
                        'low_stock_alert' => $defaultVariant->low_stock_alert,
                        'weight' => $defaultVariant->weight,
                        'length' => $defaultVariant->length,
                        'width' => $defaultVariant->width,
                        'height' => $defaultVariant->height,
                        'shipping_class' => $defaultVariant->shipping_class,
                    ];
                }
            }
            
            // Load grouped product child products
            if ($product->product_type === 'grouped') {
                $this->selectedChildProducts = $product->childProducts->pluck('id')->toArray();
            }

            // Load existing images (both old and new system)
            $images = $product->images()->orderBy('sort_order')->get();
            foreach ($images as $index => $image) {
                // OLD system (keep for backward compatibility)
                $this->existingImages[] = [
                    'id' => $image->id,
                    'path' => $image->image_path,
                    'is_primary' => $image->is_primary,
                ];
                if ($image->is_primary) {
                    $this->primaryImageIndex = $index;
                }
                
                // NEW system - load from media library
                if ($image->media_id) {
                    $this->selectedImages[] = [
                        'id' => $image->id, // ProductImage ID
                        'media_id' => $image->media_id,
                        'url' => $image->getMediumUrl(),
                        'thumbnail_url' => $image->getThumbnailUrl(),
                        'is_primary' => $image->is_primary,
                        'sort_order' => $image->sort_order ?? $index,
                    ];
                    
                    if ($image->is_primary) {
                        $this->primaryImageMediaId = $image->media_id;
                    }
                }
            }
            
            $this->status = $product->status ?? 'draft';
        } else {
            // Create draft product for new products
            $this->createDraftProduct();
        }
    }

    protected function createDraftProduct()
    {
        $draftProduct = Product::create([
            'name' => 'Draft Product - ' . now()->format('Y-m-d H:i:s'),
            'slug' => 'draft-product-' . uniqid(),
            'product_type' => 'simple',
            'status' => 'draft',
            'is_active' => false,
            'is_featured' => false,
        ]);

        $this->product = $draftProduct;
        $this->isEdit = true;
    }

    public function updatedName($value)
    {
        // Only auto-update slug for new products
        if ($this->isNewProduct) {
            // Use Bangla-compatible slug generation
            $this->slug = generate_slug($value);
            
            // Fallback to Laravel's Str::slug if generate_slug returns empty
            if (empty($this->slug)) {
                $this->slug = \Illuminate\Support\Str::slug($value);
            }
        }
    }
    
    public function generatePermalink()
    {
        if (empty($this->name)) {
            session()->flash('error', 'Please enter a product name first.');
            return;
        }
        
        // Use Bangla-compatible slug generation
        $baseSlug = generate_slug($this->name);
        
        // Fallback to Laravel's Str::slug if generate_slug returns empty
        if (empty($baseSlug)) {
            $baseSlug = \Illuminate\Support\Str::slug($this->name);
        }
        
        $slug = $baseSlug;
        $counter = 1;
        
        // Check for uniqueness
        while (Product::where('slug', $slug)
            ->when($this->product, function($query) {
                return $query->where('id', '!=', $this->product->id);
            })
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        $this->slug = $slug;
        session()->flash('success', 'Permalink generated successfully!');
    }

    public function updatedProductType($value)
    {
        $this->showVariantSection = $value === 'simple';
    }

    public function save(ProductService $service)
    {
        try {
            $this->validate();

            $data = [
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'short_description' => $this->short_description,
                'category_ids' => $this->category_ids,
                'brand_id' => $this->brand_id ?: null,
                'product_type' => $this->product_type,
                'is_featured' => $this->is_featured,
                'is_active' => $this->is_active,
                'status' => $this->status,
                'external_url' => $this->external_url,
                'button_text' => $this->button_text,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
            ];

            if ($this->product_type === 'simple' || $this->product_type === 'grouped') {
                $variantData = $this->variant;
                
                // Remove stock fields if manual stock update is disabled
                $manualStockEnabled = \App\Models\SiteSetting::get('manual_stock_update_enabled', '0') === '1';
                if (!$manualStockEnabled) {
                    unset($variantData['stock_quantity']);
                    unset($variantData['low_stock_alert']);
                }
                
                $data['variant'] = $variantData;
            }

            if ($this->product_type === 'grouped') {
                $data['child_products'] = $this->selectedChildProducts;
            }

            if ($this->product_type === 'variable' && !empty($this->tempVariations)) {
                $data['temp_variations'] = $this->tempVariations;
            }

            // Handle images - NEW media library system
            if (!empty($this->selectedImages)) {
                $data['selected_images'] = $this->selectedImages;
                $data['primary_image_media_id'] = $this->primaryImageMediaId;
            }
            // OLD system (keeping for backward compatibility)
            elseif (!empty($this->images)) {
                $data['images'] = $this->images;
                $data['primary_image_index'] = $this->primaryImageIndex;
            }

            // Always update since we create draft on mount
            $product = $service->update($this->product, $data);
            
            if ($this->status === 'published') {
                session()->flash('success', 'Product published successfully!');
            } else {
                session()->flash('success', 'Product saved as draft!');
            }

            return redirect()->route('admin.products.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions so Livewire can handle them
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Product save error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Error saving product: ' . $e->getMessage());
            $this->dispatch('error', message: 'Error saving product: ' . $e->getMessage());
        }
    }

    public function publish(ProductService $service)
    {
        $this->status = 'published';
        return $this->save($service);
    }

    public function nextStep()
    {
        $this->currentStep++;
    }

    public function previousStep()
    {
        $this->currentStep--;
    }

    public function addChildProduct($productId)
    {
        if (!in_array($productId, $this->selectedChildProducts)) {
            $this->selectedChildProducts[] = $productId;
            $this->childProductSearch = '';
        }
    }

    public function removeChildProduct($index)
    {
        unset($this->selectedChildProducts[$index]);
        $this->selectedChildProducts = array_values($this->selectedChildProducts);
    }

    // Temporary Variation Management
    public function addTempVariation($variationData)
    {
        // If it's an array of variations, merge them
        if (is_array($variationData) && isset($variationData[0])) {
            $this->tempVariations = array_merge($this->tempVariations, $variationData);
        } else {
            $this->tempVariations[] = $variationData;
        }
    }

    public function updateTempVariation($index, $variationData)
    {
        if (isset($this->tempVariations[$index])) {
            $this->tempVariations[$index] = $variationData;
        }
    }

    public function deleteTempVariation($index)
    {
        unset($this->tempVariations[$index]);
        $this->tempVariations = array_values($this->tempVariations);
    }

    public function getTempVariationsProperty()
    {
        return $this->tempVariations;
    }

    // Image Management Methods
    public function removeImage($index)
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images);
            
            // Adjust primary image index if needed
            if ($this->primaryImageIndex === $index) {
                $this->primaryImageIndex = null;
            } elseif ($this->primaryImageIndex > $index) {
                $this->primaryImageIndex--;
            }
        }
    }

    public function removeExistingImage($index)
    {
        if (isset($this->existingImages[$index])) {
            unset($this->existingImages[$index]);
            $this->existingImages = array_values($this->existingImages);
        }
    }

    public function setPrimaryImage($index)
    {
        $this->primaryImageIndex = $index;
    }
    
    // NEW Media Library Image Management Methods
    public function handleImageSelected($data = [])
    {
        $this->js("console.log('handleImageSelected called with data:', " . json_encode($data) . ")");
        
        // Extract media and field from data array
        $media = $data['media'] ?? null;
        $field = $data['field'] ?? null;
        
        if (!$media || !$field) {
            $this->js("console.log('handleImageSelected: Missing media or field in data')");
            return;
        }
        
        $this->js("console.log('handleImageSelected: field=' + '{$field}' + ', media count=' + " . count($media) . ")");
        
        if ($field === 'product_images') {
            
            foreach ($media as $mediaItem) {
                // Check if already selected
                $exists = collect($this->selectedImages)->contains('media_id', $mediaItem['id']);
                
                if (!$exists) {
                    $this->selectedImages[] = [
                        'media_id' => $mediaItem['id'],
                        'url' => $mediaItem['medium_url'] ?? $mediaItem['large_url'],
                        'thumbnail_url' => $mediaItem['small_url'] ?? $mediaItem['medium_url'],
                        'is_primary' => empty($this->selectedImages), // First image is primary
                        'sort_order' => count($this->selectedImages),
                    ];
                }
            }
            
            // Set first image as primary if none set
            if (!empty($this->selectedImages) && is_null($this->primaryImageMediaId)) {
                $this->primaryImageMediaId = $this->selectedImages[0]['media_id'];
                $this->selectedImages[0]['is_primary'] = true;
            }
        }
    }
    
    public function handleImageUploaded($data = [])
    {
        $this->js("console.log('handleImageUploaded called with data:', " . json_encode($data) . ")");
        
        // Extract media and field from data array
        $media = $data['media'] ?? null;
        $field = $data['field'] ?? null;
        
        if (!$media || !$field) {
            $this->js("console.log('handleImageUploaded: Missing media or field in data')");
            return;
        }
        
        $this->js("console.log('handleImageUploaded: field=' + '{$field}' + ', media count=' + " . count($media) . ")");
        
        if ($field === 'product_images') {
            
            foreach ($media as $mediaItem) {
                $this->selectedImages[] = [
                    'media_id' => $mediaItem['id'],
                    'url' => $mediaItem['medium_url'] ?? $mediaItem['large_url'],
                    'thumbnail_url' => $mediaItem['small_url'] ?? $mediaItem['medium_url'],
                    'is_primary' => empty($this->selectedImages), // First image is primary
                    'sort_order' => count($this->selectedImages),
                ];
            }
            
            // Set first image as primary if none set
            if (!empty($this->selectedImages) && is_null($this->primaryImageMediaId)) {
                $this->primaryImageMediaId = $this->selectedImages[0]['media_id'];
                $this->selectedImages[0]['is_primary'] = true;
            }
        }
    }
    
    public function removeSelectedImage($index)
    {
        if (isset($this->selectedImages[$index])) {
            $removedMediaId = $this->selectedImages[$index]['media_id'];
            unset($this->selectedImages[$index]);
            $this->selectedImages = array_values($this->selectedImages);
            
            // Update sort order
            foreach ($this->selectedImages as $i => $image) {
                $this->selectedImages[$i]['sort_order'] = $i;
            }
            
            // Reset primary if removed image was primary
            if ($removedMediaId === $this->primaryImageMediaId) {
                if (!empty($this->selectedImages)) {
                    $this->primaryImageMediaId = $this->selectedImages[0]['media_id'];
                    $this->selectedImages[0]['is_primary'] = true;
                } else {
                    $this->primaryImageMediaId = null;
                }
            }
        }
    }
    
    public function setSelectedImageAsPrimary($index)
    {
        // Remove primary from all
        foreach ($this->selectedImages as $i => $image) {
            $this->selectedImages[$i]['is_primary'] = false;
        }
        
        // Set new primary
        if (isset($this->selectedImages[$index])) {
            $this->selectedImages[$index]['is_primary'] = true;
            $this->primaryImageMediaId = $this->selectedImages[$index]['media_id'];
        }
    }
    
    public function handleReorderImages($data = [])
    {
        $this->js("console.log('handleReorderImages called')");
        
        // Extract indices from data array
        $oldIndex = $data['oldIndex'] ?? null;
        $newIndex = $data['newIndex'] ?? null;
        
        if (is_null($oldIndex) || is_null($newIndex)) {
            $this->js("console.log('handleReorderImages: Missing indices')");
            return;
        }
        
        if (isset($this->selectedImages[$oldIndex])) {
            $image = array_splice($this->selectedImages, $oldIndex, 1)[0];
            array_splice($this->selectedImages, $newIndex, 0, [$image]);
            
            // Update sort_order for all images
            foreach ($this->selectedImages as $index => $img) {
                $this->selectedImages[$index]['sort_order'] = $index;
            }
        }
    }
    
    // Quick Add Category
    public function openCategoryModal()
    {
        $this->showCategoryModal = true;
        $this->newCategoryName = '';
    }
    
    public function closeCategoryModal()
    {
        $this->showCategoryModal = false;
        $this->newCategoryName = '';
    }
    
    public function saveCategory()
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255|unique:categories,name',
        ]);
        
        $category = Category::create([
            'name' => $this->newCategoryName,
            'slug' => \Str::slug($this->newCategoryName),
            'is_active' => true,
        ]);
        
        // Add to selected categories
        $this->category_ids[] = $category->id;
        
        $this->closeCategoryModal();
        session()->flash('success', 'Category created successfully!');
    }
    
    // Quick Add Brand
    public function openBrandModal()
    {
        $this->showBrandModal = true;
        $this->newBrandName = '';
    }
    
    public function closeBrandModal()
    {
        $this->showBrandModal = false;
        $this->newBrandName = '';
    }
    
    public function saveBrand()
    {
        $this->validate([
            'newBrandName' => 'required|string|max:255|unique:brands,name',
        ]);
        
        $brand = Brand::create([
            'name' => $this->newBrandName,
            'slug' => \Str::slug($this->newBrandName),
            'is_active' => true,
        ]);
        
        // Select the new brand
        $this->brand_id = $brand->id;
        
        $this->closeBrandModal();
        session()->flash('success', 'Brand created successfully!');
    }

    public function render()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $products = Product::where('id', '!=', $this->product?->id)->get();

        return view('livewire.admin.product.product-form-enhanced', compact('categories', 'brands', 'products'));
    }
}
