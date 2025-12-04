<?php

namespace App\Livewire\Admin\Product;

use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductAttribute;
use App\Modules\Ecommerce\Product\Services\ProductService;
use Livewire\Component;

class VariantManager extends Component
{
    public Product $product;
    public $variants = [];
    public $attributes = [];
    public $selectedAttributes = [];
    public $generatedVariants = [];
    public $showGenerator = false;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->loadVariants();
        $this->attributes = ProductAttribute::active()->with('values')->get();
    }

    public function loadVariants()
    {
        $this->variants = $this->product->variants()
            ->with('attributeValues')
            ->get()
            ->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'name' => $variant->name,
                    'price' => $variant->price,
                    'sale_price' => $variant->sale_price,
                    'stock_quantity' => $variant->stock_quantity,
                    'is_default' => $variant->is_default,
                    'is_active' => $variant->is_active,
                ];
            })
            ->toArray();
    }

    public function generateVariants()
    {
        if (empty($this->selectedAttributes)) {
            session()->flash('error', 'Please select at least one attribute.');
            return;
        }

        $combinations = $this->generateCombinations();
        
        $this->generatedVariants = collect($combinations)->map(function ($combination) {
            return [
                'name' => implode(' - ', array_column($combination, 'value')),
                'sku' => '',
                'price' => '',
                'sale_price' => '',
                'stock_quantity' => 0,
                'attributes' => $combination,
            ];
        })->toArray();

        $this->showGenerator = true;
    }

    protected function generateCombinations()
    {
        $attributeValues = [];
        
        foreach ($this->selectedAttributes as $attributeId) {
            $attribute = $this->attributes->find($attributeId);
            if ($attribute) {
                $attributeValues[] = $attribute->values->map(function ($value) use ($attribute) {
                    return [
                        'attribute_id' => $attribute->id,
                        'attribute_name' => $attribute->name,
                        'value_id' => $value->id,
                        'value' => $value->value,
                    ];
                })->toArray();
            }
        }

        return $this->cartesianProduct($attributeValues);
    }

    protected function cartesianProduct($arrays)
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

    public function saveVariants(ProductService $service)
    {
        foreach ($this->generatedVariants as $variantData) {
            if (empty($variantData['price'])) {
                continue;
            }

            $variant = $service->createVariant($this->product, [
                'name' => $variantData['name'],
                'sku' => $variantData['sku'] ?: null,
                'price' => $variantData['price'],
                'sale_price' => $variantData['sale_price'] ?: null,
                'stock_quantity' => $variantData['stock_quantity'] ?? 0,
            ]);

            // Attach attributes
            foreach ($variantData['attributes'] as $attr) {
                $variant->attributes()->attach($attr['attribute_id'], [
                    'attribute_value_id' => $attr['value_id'],
                ]);
            }
        }

        $this->loadVariants();
        $this->reset(['generatedVariants', 'selectedAttributes', 'showGenerator']);
        session()->flash('success', 'Variants created successfully!');
    }

    public function deleteVariant($variantId, ProductService $service)
    {
        $variant = $this->product->variants()->find($variantId);
        if ($variant) {
            $service->deleteVariant($variant);
            $this->loadVariants();
            session()->flash('success', 'Variant deleted successfully!');
        }
    }

    public function render()
    {
        return view('livewire.admin.product.variant-manager');
    }
}
