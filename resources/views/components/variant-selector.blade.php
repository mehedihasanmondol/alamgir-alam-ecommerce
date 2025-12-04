@props(['product'])

@php
    // Group attributes by attribute name
    $attributes = collect();
    foreach ($product->variants as $variant) {
        foreach ($variant->attributeValues as $attributeValue) {
            $attrName = $attributeValue->attribute->name;
            if (!$attributes->has($attrName)) {
                $attributes->put($attrName, [
                    'attribute' => $attributeValue->attribute,
                    'values' => collect()
                ]);
            }
            if (!$attributes->get($attrName)['values']->contains('id', $attributeValue->id)) {
                $attributes->get($attrName)['values']->push($attributeValue);
            }
        }
    }
@endphp

<div x-data="{
    selectedAttributes: {},
    variants: {{ json_encode($product->variants->map(function($variant) {
        return [
            'id' => $variant->id,
            'sku' => $variant->sku,
            'price' => $variant->price,
            'sale_price' => $variant->sale_price,
            'stock_quantity' => $variant->stock_quantity,
            'image_path' => $variant->image_path,
            'attributes' => $variant->attributeValues->pluck('id')->toArray()
        ];
    })) }},
    selectedVariant: null,
    
    init() {
        // Initialize with first variant
        if (this.variants.length > 0) {
            this.selectedVariant = this.variants[0];
        }
    },
    
    selectAttribute(attributeName, valueId) {
        this.selectedAttributes[attributeName] = valueId;
        this.findMatchingVariant();
        this.$dispatch('variant-changed', this.selectedVariant);
    },
    
    findMatchingVariant() {
        const selectedValues = Object.values(this.selectedAttributes);
        
        // Find variant that matches all selected attributes
        const matchingVariant = this.variants.find(variant => {
            return selectedValues.every(valueId => variant.attributes.includes(valueId));
        });
        
        if (matchingVariant) {
            this.selectedVariant = matchingVariant;
        }
    },
    
    isAttributeSelected(attributeName, valueId) {
        return this.selectedAttributes[attributeName] === valueId;
    },
    
    isAttributeAvailable(attributeName, valueId) {
        // Check if selecting this value would result in an available variant
        const testSelection = { ...this.selectedAttributes, [attributeName]: valueId };
        const testValues = Object.values(testSelection);
        
        return this.variants.some(variant => {
            return testValues.every(id => variant.attributes.includes(id)) && variant.stock_quantity > 0;
        });
    }
}" class="space-y-4">

    @foreach($attributes as $attributeName => $data)
        @php
            $attribute = $data['attribute'];
            $values = $data['values'];
        @endphp
        
        <div>
            <!-- Attribute Label -->
            <div class="flex items-center justify-between mb-3">
                <label class="text-sm font-medium text-gray-900">
                    {{ $attribute->name }}:
                    <span x-show="selectedAttributes['{{ $attributeName }}']" class="text-green-600 font-semibold">
                        <template x-for="value in {{ json_encode($values) }}" :key="value.id">
                            <span x-show="selectedAttributes['{{ $attributeName }}'] === value.id" x-text="value.value"></span>
                        </template>
                    </span>
                </label>
            </div>

            <!-- Attribute Values -->
            <div class="flex flex-wrap gap-2">
                @foreach($values as $value)
                    @if($attribute->type === 'color')
                        <!-- Color Swatch -->
                        <button 
                            @click="selectAttribute('{{ $attributeName }}', {{ $value->id }})"
                            :class="{
                                'ring-2 ring-green-600 ring-offset-2': isAttributeSelected('{{ $attributeName }}', {{ $value->id }}),
                                'ring-1 ring-gray-300': !isAttributeSelected('{{ $attributeName }}', {{ $value->id }}),
                                'opacity-50 cursor-not-allowed': !isAttributeAvailable('{{ $attributeName }}', {{ $value->id }})
                            }"
                            :disabled="!isAttributeAvailable('{{ $attributeName }}', {{ $value->id }})"
                            class="relative w-10 h-10 rounded-full transition-all hover:scale-110"
                            style="background-color: {{ $value->color_code ?? '#cccccc' }};"
                            title="{{ $value->value }}">
                            
                            <!-- Selected Check Mark -->
                            <span x-show="isAttributeSelected('{{ $attributeName }}', {{ $value->id }})" 
                                  class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            
                            <!-- Out of Stock Indicator -->
                            <span x-show="!isAttributeAvailable('{{ $attributeName }}', {{ $value->id }})" 
                                  class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </span>
                        </button>
                    @else
                        <!-- Button/Text Style -->
                        <button 
                            @click="selectAttribute('{{ $attributeName }}', {{ $value->id }})"
                            :class="{
                                'bg-green-600 text-white border-green-600': isAttributeSelected('{{ $attributeName }}', {{ $value->id }}),
                                'bg-white text-gray-700 border-gray-300 hover:border-green-600': !isAttributeSelected('{{ $attributeName }}', {{ $value->id }}),
                                'opacity-50 cursor-not-allowed line-through': !isAttributeAvailable('{{ $attributeName }}', {{ $value->id }})
                            }"
                            :disabled="!isAttributeAvailable('{{ $attributeName }}', {{ $value->id }})"
                            class="px-4 py-2 border-2 rounded-lg font-medium transition-all text-sm">
                            {{ $value->value }}
                        </button>
                    @endif
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- Selected Variant Info -->
    <div x-show="selectedVariant" class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-600">SKU:</span>
                <span class="font-medium text-gray-900 ml-2" x-text="selectedVariant?.sku"></span>
            </div>
            <div>
                <span class="text-gray-600">Stock:</span>
                <span class="font-medium ml-2" 
                      :class="selectedVariant?.stock_quantity > 0 ? 'text-green-600' : 'text-red-600'"
                      x-text="selectedVariant?.stock_quantity > 0 ? selectedVariant.stock_quantity + ' available' : 'Out of Stock'">
                </span>
            </div>
        </div>
    </div>
</div>
