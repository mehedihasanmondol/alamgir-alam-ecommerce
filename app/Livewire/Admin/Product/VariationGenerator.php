<?php

namespace App\Livewire\Admin\Product;

use App\Modules\Ecommerce\Product\Models\ProductAttribute;
use App\Modules\Ecommerce\Product\Models\ProductVariant;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class VariationGenerator extends Component
{
    use WithFileUploads;

    public $productId;
    public $tempMode = false;
    public $selectedAttributes = [];
    public $selectedValues = [];
    public $variations = [];
    public $generatedVariations = [];
    public $showGenerator = false;
    public $editingVariations = [];
    public $variationImages = [];

    protected $listeners = ['variationsGenerated' => 'loadVariations'];

    public function mount($productId = null, $tempMode = false)
    {
        $this->productId = $productId;
        $this->tempMode = $tempMode;
        if ($productId) {
            $this->loadVariations();
        }
    }

    public function loadVariations()
    {
        if ($this->productId) {
            $variants = ProductVariant::where('product_id', $this->productId)
                ->where('is_default', false)
                ->with('attributeValues.attribute')
                ->get();
            
            $this->editingVariations = [];
            foreach ($variants as $variant) {
                $this->editingVariations[] = [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'sale_price' => $variant->sale_price,
                    'cost_price' => $variant->cost_price,
                    'stock_quantity' => $variant->stock_quantity,
                    'low_stock_alert' => $variant->low_stock_alert,
                    'weight' => $variant->weight,
                    'length' => $variant->length,
                    'width' => $variant->width,
                    'height' => $variant->height,
                    'image' => $variant->image,
                    'enabled' => true,
                ];
            }
        }
    }

    public function removeVariationImage($index)
    {
        if (isset($this->editingVariations[$index])) {
            $this->editingVariations[$index]['image'] = null;
        }
    }

    public function updatedVariationImages($value, $key)
    {
        // Extract the index from the key (e.g., "0" from "variationImages.0")
        $index = (int) $key;
        
        // Store the uploaded file temporarily
        if (isset($this->variationImages[$index])) {
            $this->editingVariations[$index]['new_image'] = $this->variationImages[$index];
            $this->editingVariations[$index]['preview_url'] = $this->variationImages[$index]->temporaryUrl();
        }
    }

    public function removeNewVariationImage($index)
    {
        if (isset($this->variationImages[$index])) {
            unset($this->variationImages[$index]);
        }
        if (isset($this->editingVariations[$index])) {
            unset($this->editingVariations[$index]['new_image']);
            unset($this->editingVariations[$index]['preview_url']);
        }
    }

    public function toggleGenerator()
    {
        $this->showGenerator = !$this->showGenerator;
    }

    public function addAttribute()
    {
        $this->selectedAttributes[] = [
            'attribute_id' => '',
            'value_ids' => []
        ];
    }

    public function removeAttribute($index)
    {
        unset($this->selectedAttributes[$index]);
        $this->selectedAttributes = array_values($this->selectedAttributes);
    }

    public function generateVariations()
    {
        $this->validate([
            'selectedAttributes' => 'required|array|min:1',
            'selectedAttributes.*.attribute_id' => 'required|exists:product_attributes,id',
            'selectedAttributes.*.value_ids' => 'required|array|min:1',
        ]);

        // Generate all combinations
        $combinations = $this->generateCombinations();
        
        $this->variations = [];
        foreach ($combinations as $combination) {
            $variantName = implode(' - ', array_column($combination, 'value'));
            $this->variations[] = [
                'name' => $variantName,
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
                'attributes' => $combination,
                'enabled' => true,
            ];
        }

        session()->flash('success', count($this->variations) . ' variations generated!');
        
        // Dispatch event to collapse all variations
        $this->dispatch('variations-generated');
    }

    private function generateCombinations()
    {
        $attributeValues = [];
        
        foreach ($this->selectedAttributes as $selected) {
            $attribute = ProductAttribute::with('values')->find($selected['attribute_id']);
            $values = $attribute->values->whereIn('id', $selected['value_ids'])->toArray();
            
            $attributeValues[] = array_map(function($value) use ($attribute) {
                return [
                    'attribute_id' => $attribute->id,
                    'attribute_name' => $attribute->name,
                    'value_id' => $value['id'],
                    'value' => $value['value'],
                ];
            }, $values);
        }

        return $this->cartesianProduct($attributeValues);
    }

    private function cartesianProduct($arrays)
    {
        $result = [[]];
        foreach ($arrays as $key => $values) {
            $append = [];
            foreach ($result as $product) {
                foreach ($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }
            $result = $append;
        }
        return $result;
    }

    public function saveVariations()
    {
        // If in temp mode (no product ID), emit to parent component
        if ($this->tempMode && !$this->productId) {
            $tempVariations = [];
            foreach ($this->variations as $variation) {
                if (!$variation['enabled']) continue;
                
                $tempVariations[] = [
                    'name' => $variation['name'],
                    'sku' => $variation['sku'] ?: '',
                    'price' => $variation['price'] ?: 0,
                    'sale_price' => $variation['sale_price'] ?: null,
                    'cost_price' => null,
                    'stock_quantity' => $variation['stock_quantity'] ?: 0,
                    'low_stock_alert' => 5,
                    'weight' => null,
                    'length' => null,
                    'width' => null,
                    'height' => null,
                    'attributes' => $variation['attributes'] ?? [],
                ];
            }
            
            // Dispatch to parent ProductForm component
            $this->dispatch('variationAdded', $tempVariations)->to(ProductForm::class);
            
            $this->reset('variations', 'selectedAttributes', 'showGenerator');
            session()->flash('success', 'Variations added! They will be saved when you publish the product.');
            
            // Dispatch event to collapse all variations
            $this->dispatch('variation-saved');
            return;
        }

        // Normal mode - save to database
        if (!$this->productId) {
            session()->flash('error', 'Please save the product first before adding variations.');
            return;
        }

        foreach ($this->variations as $variation) {
            if (!$variation['enabled']) continue;

            $variant = ProductVariant::create([
                'product_id' => $this->productId,
                'name' => $variation['name'],
                'sku' => $variation['sku'] ?: Str::random(8),
                'price' => $variation['price'] ?: 0,
                'sale_price' => $variation['sale_price'] ?: null,
                'cost_price' => $variation['cost_price'] ?: null,
                'stock_quantity' => $variation['stock_quantity'] ?: 0,
                'low_stock_alert' => $variation['low_stock_alert'] ?? 5,
                'weight' => $variation['weight'] ?: null,
                'length' => $variation['length'] ?: null,
                'width' => $variation['width'] ?: null,
                'height' => $variation['height'] ?: null,
                'is_default' => false,
            ]);

            // Attach attribute values if provided
            if (!empty($variation['attributes'])) {
                foreach ($variation['attributes'] as $attribute) {
                    if (isset($attribute['attribute_id']) && isset($attribute['value_id'])) {
                        $variant->attributeValues()->attach($attribute['value_id'], [
                            'product_attribute_id' => $attribute['attribute_id'],
                        ]);
                    }
                }
            }
        }

        $this->reset('variations', 'selectedAttributes', 'showGenerator');
        $this->loadVariations();
        session()->flash('success', 'Variations saved successfully!');
        
        // Dispatch event to collapse all variations
        $this->dispatch('variation-saved');
    }

    public function updateExistingVariation($index)
    {
        if (!isset($this->editingVariations[$index])) {
            return;
        }

        $variationData = $this->editingVariations[$index];
        $variant = ProductVariant::find($variationData['id']);
        
        if ($variant) {
            // Convert empty strings to null
            $nullableFields = ['sale_price', 'cost_price', 'weight', 'length', 'width', 'height'];
            foreach ($nullableFields as $field) {
                if (isset($variationData[$field]) && $variationData[$field] === '') {
                    $variationData[$field] = null;
                }
            }
            
            // Handle image upload
            $imagePath = $variationData['image'];
            if (isset($variationData['new_image']) && $variationData['new_image']) {
                $imagePath = $variationData['new_image']->store('products/variations', 'public');
            }
            
            $variant->update([
                'name' => $variationData['name'],
                'sku' => $variationData['sku'],
                'price' => $variationData['price'] ?: 0,
                'sale_price' => $variationData['sale_price'],
                'cost_price' => $variationData['cost_price'],
                'stock_quantity' => $variationData['stock_quantity'] ?: 0,
                'low_stock_alert' => $variationData['low_stock_alert'] ?? 5,
                'weight' => $variationData['weight'],
                'length' => $variationData['length'],
                'width' => $variationData['width'],
                'height' => $variationData['height'],
                'image' => $imagePath,
            ]);
            
            session()->flash('success', 'Variation updated successfully!');
            $this->loadVariations();
            
            // Dispatch event to collapse the variation
            $this->dispatch('variation-updated');
        }
    }

    public function deleteVariation($variationId)
    {
        ProductVariant::find($variationId)?->delete();
        $this->loadVariations();
        session()->flash('success', 'Variation deleted successfully!');
    }

    public function render()
    {
        $attributes = ProductAttribute::with('values')->active()->orderBy('name')->get();
        
        return view('livewire.admin.product.variation-generator', [
            'attributes' => $attributes,
        ]);
    }
}
