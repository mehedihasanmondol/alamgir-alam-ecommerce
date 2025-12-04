<?php

namespace App\Modules\Ecommerce\Product\Services;

use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductVariant;
use App\Services\ImageService;
use App\Modules\Ecommerce\Product\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        protected ProductRepository $repository
    ) {}

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Extract categories before creating product
            $categoryIds = $data['category_ids'] ?? [];
            unset($data['category_ids']);

            // Create product
            $product = $this->repository->create($data);

            // Sync categories (many-to-many relationship)
            if (!empty($categoryIds)) {
                $product->categories()->sync($categoryIds);
            }

            // Create default variant for simple and grouped products
            if (($product->product_type === 'simple' || $product->product_type === 'grouped') && !empty($data['variant'])) {
                $this->createDefaultVariant($product, $data['variant']);
            }

            // Handle images
            if (!empty($data['images'])) {
                $this->syncImages($product, $data['images'], $data['primary_image_index'] ?? null);
            }

            // Handle grouped products
            if ($product->product_type === 'grouped' && !empty($data['child_products'])) {
                $this->syncGroupedProducts($product, $data['child_products']);
            }

            // Handle temporary variations for variable products
            if ($product->product_type === 'variable' && !empty($data['temp_variations'])) {
                $this->createTempVariations($product, $data['temp_variations']);
            }

            return $product->load(['variants', 'images', 'categories']);
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            // Extract categories before updating product
            $categoryIds = $data['category_ids'] ?? [];
            unset($data['category_ids']);

            // Update product
            $this->repository->update($product, $data);

            // Sync categories (many-to-many relationship)
            if (isset($categoryIds)) {
                if (!empty($categoryIds)) {
                    $product->categories()->sync($categoryIds);
                } else {
                    $product->categories()->detach();
                }
            }

            // Update variant for simple and grouped products
            if (($product->product_type === 'simple' || $product->product_type === 'grouped') && !empty($data['variant'])) {
                $this->updateDefaultVariant($product, $data['variant']);
            }

            // Handle images - NEW media library system
            if (isset($data['selected_images'])) {
                $this->syncMediaLibraryImages($product, $data['selected_images'], $data['primary_image_media_id'] ?? null);
            }
            // OLD system (keeping for backward compatibility)
            elseif (isset($data['images'])) {
                $this->syncImages($product, $data['images'], $data['primary_image_index'] ?? null);
            }

            // Handle grouped products
            if ($product->product_type === 'grouped' && isset($data['child_products'])) {
                $this->syncGroupedProducts($product, $data['child_products']);
            }

            return $product->fresh(['variants', 'images', 'categories']);
        });
    }

    public function delete(Product $product): bool
    {
        return $this->repository->delete($product);
    }

    protected function createDefaultVariant(Product $product, array $variantData): ProductVariant
    {
        $variantData['is_default'] = true;
        
        // Set variant name (same as product name for simple products)
        if (empty($variantData['name'])) {
            $variantData['name'] = $product->name;
        }
        
        // Auto-generate SKU if not provided
        if (empty($variantData['sku'])) {
            $variantData['sku'] = $this->generateSku($product);
        }
        
        // Set default stock values if manual stock update is disabled
        $manualStockEnabled = \App\Models\SiteSetting::get('manual_stock_update_enabled', '0') === '1';
        if (!$manualStockEnabled) {
            if (!isset($variantData['stock_quantity'])) {
                $variantData['stock_quantity'] = 0;
            }
            if (!isset($variantData['low_stock_alert'])) {
                $variantData['low_stock_alert'] = 5;
            }
        }
        
        // Convert empty strings to null for nullable fields
        $nullableFields = ['sale_price', 'cost_price', 'weight', 'length', 'width', 'height', 'shipping_class'];
        foreach ($nullableFields as $field) {
            if (isset($variantData[$field]) && $variantData[$field] === '') {
                $variantData[$field] = null;
            }
        }

        return $product->variants()->create($variantData);
    }

    protected function updateDefaultVariant(Product $product, array $variantData): void
    {
        // Convert empty strings to null for nullable fields
        $nullableFields = ['sale_price', 'cost_price', 'weight', 'length', 'width', 'height', 'shipping_class'];
        foreach ($nullableFields as $field) {
            if (isset($variantData[$field]) && $variantData[$field] === '') {
                $variantData[$field] = null;
            }
        }
        
        // Remove stock fields if manual stock update is disabled
        $manualStockEnabled = \App\Models\SiteSetting::get('manual_stock_update_enabled', '0') === '1';
        if (!$manualStockEnabled) {
            unset($variantData['stock_quantity']);
            unset($variantData['low_stock_alert']);
        }
        
        // Fix Issue 2: Use the correct relationship method to get the default variant
        $variant = $product->defaultVariant;
        
        if ($variant) {
            // Fix Issue 1: Compare SKU and only update if it has changed
            $updateData = $variantData;
            
            // If SKU is provided and it's the same as current SKU, remove it from update data
            if (isset($variantData['sku']) && $variant->sku === $variantData['sku']) {
                unset($updateData['sku']);
            }
            
            // Only update if there's actually data to update
            if (!empty($updateData)) {
                $variant->update($updateData);
            }
        } else {
            $this->createDefaultVariant($product, $variantData);
        }
    }

    protected function syncImages(Product $product, array $imagesData, ?int $primaryIndex = null): void
    {
        if (empty($imagesData)) {
            return;
        }

        // Handle uploaded files
        $imagePaths = [];
        foreach ($imagesData as $index => $image) {
            if (is_object($image) && method_exists($image, 'store')) {
                // It's an uploaded file - process and convert to WebP
                try {
                    // Validate file size
                    if (!ImageService::validateFileSize($image)) {
                        \Log::warning("Image upload failed: File size exceeds limit", [
                            'max_size' => ImageService::getMaxUploadSizeFormatted()
                        ]);
                        continue;
                    }
                    
                    // Process and convert to WebP with compression (quality: 85)
                    $path = ImageService::processAndStore($image, 'products', 85);
                    
                    $imagePaths[] = [
                        'image_path' => $path,
                        'thumbnail_path' => $path, // Same path, no separate thumbnail
                    ];
                } catch (\Exception $e) {
                    \Log::error("Image processing failed", [
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            } elseif (is_string($image)) {
                // It's already a path
                $imagePaths[] = [
                    'image_path' => $image,
                    'thumbnail_path' => $image,
                ];
            }
        }

        // Delete existing images if we're replacing them
        if (!empty($imagePaths)) {
            $product->images()->delete();

            // Add new images
            foreach ($imagePaths as $index => $imageData) {
                $product->images()->create([
                    'image_path' => $imageData['image_path'],
                    'thumbnail_path' => $imageData['thumbnail_path'] ?? $imageData['image_path'],
                    'is_primary' => $primaryIndex !== null ? ($index === $primaryIndex) : ($index === 0),
                    'sort_order' => $index,
                ]);
            }
        }
    }
    
    protected function syncMediaLibraryImages(Product $product, array $selectedImages, ?int $primaryMediaId = null): void
    {
        if (empty($selectedImages)) {
            return;
        }

        // Delete existing images if we're replacing them
        $product->images()->delete();

        // Add new images from media library
        foreach ($selectedImages as $imageData) {
            $product->images()->create([
                'media_id' => $imageData['media_id'],
                'is_primary' => isset($imageData['is_primary']) ? $imageData['is_primary'] : false,
                'sort_order' => $imageData['sort_order'] ?? 0,
            ]);
        }

        // Ensure at least one image is marked as primary
        if ($product->images()->where('is_primary', true)->count() === 0) {
            $firstImage = $product->images()->orderBy('sort_order')->first();
            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }
    }

    protected function syncGroupedProducts(Product $product, array $childProducts): void
    {
        $syncData = [];
        
        foreach ($childProducts as $index => $childData) {
            // Handle both array format and simple ID format
            if (is_array($childData)) {
                $syncData[$childData['id']] = [
                    'quantity' => $childData['quantity'] ?? 1,
                    'sort_order' => $index,
                ];
            } else {
                // Simple product ID
                $syncData[$childData] = [
                    'quantity' => 1,
                    'sort_order' => $index,
                ];
            }
        }

        $product->childProducts()->sync($syncData);
    }

    protected function generateSku(Product $product): string
    {
        $prefix = strtoupper(substr($product->name, 0, 3));
        $random = strtoupper(Str::random(6));
        
        return $prefix . '-' . $random;
    }

    public function createVariant(Product $product, array $data): ProductVariant
    {
        if (empty($data['sku'])) {
            $data['sku'] = $this->generateSku($product) . '-' . Str::random(4);
        }

        return $product->variants()->create($data);
    }

    public function updateVariant(ProductVariant $variant, array $data): ProductVariant
    {
        // Apply the same SKU comparison logic to prevent duplicate entry errors
        $updateData = $data;
        
        // Remove stock fields if manual stock update is disabled
        $manualStockEnabled = \App\Models\SiteSetting::get('manual_stock_update_enabled', '0') === '1';
        if (!$manualStockEnabled) {
            unset($updateData['stock_quantity']);
            unset($updateData['low_stock_alert']);
        }
        
        // If SKU is provided and it's the same as current SKU, remove it from update data
        if (isset($data['sku']) && $variant->sku === $data['sku']) {
            unset($updateData['sku']);
        }
        
        // Only update if there's actually data to update
        if (!empty($updateData)) {
            $variant->update($updateData);
        }
        
        return $variant->fresh();
    }

    public function deleteVariant(ProductVariant $variant): bool
    {
        // Don't allow deleting the last variant
        if ($variant->product->variants()->count() <= 1) {
            return false;
        }

        return $variant->delete();
    }

    public function toggleFeatured(Product $product): Product
    {
        $product->update(['is_featured' => !$product->is_featured]);
        return $product->fresh();
    }

    public function toggleActive(Product $product): Product
    {
        $product->update(['is_active' => !$product->is_active]);
        return $product->fresh();
    }

    protected function createTempVariations(Product $product, array $variations): void
    {
        foreach ($variations as $index => $variationData) {
            $variantData = [
                'sku' => $variationData['sku'] ?? $this->generateSku($product) . '-' . Str::random(4),
                'price' => $variationData['price'] ?? 0,
                'sale_price' => $variationData['sale_price'] ?? null,
                'cost_price' => $variationData['cost_price'] ?? null,
                'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                'low_stock_alert' => $variationData['low_stock_alert'] ?? 5,
                'weight' => $variationData['weight'] ?? null,
                'length' => $variationData['length'] ?? null,
                'width' => $variationData['width'] ?? null,
                'height' => $variationData['height'] ?? null,
                'is_default' => false,
            ];

            $variant = $product->variants()->create($variantData);

            // Attach attribute values if provided
            if (!empty($variationData['attributes'])) {
                foreach ($variationData['attributes'] as $attribute) {
                    if (isset($attribute['attribute_id']) && isset($attribute['value_id'])) {
                        $variant->attributeValues()->attach($attribute['value_id'], [
                            'product_attribute_id' => $attribute['attribute_id'],
                        ]);
                    }
                }
            }
        }
    }
}
