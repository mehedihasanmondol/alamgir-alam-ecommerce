<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Product Variants</h3>
        <button wire:click="$toggle('showGenerator')" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Generate Variants
        </button>
    </div>

    {{-- Variant Generator --}}
    @if($showGenerator)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h4 class="text-md font-semibold text-blue-900 mb-4">Variant Generator</h4>
        
        {{-- Select Attributes --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Attributes</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($attributes as $attribute)
                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all
                    {{ in_array($attribute->id, $selectedAttributes) ? 'border-blue-600 bg-white' : 'border-gray-200 hover:border-gray-300' }}">
                    <input type="checkbox" 
                           wire:model="selectedAttributes" 
                           value="{{ $attribute->id }}" 
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">{{ $attribute->name }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <button wire:click="generateVariants" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Generate Combinations
        </button>

        {{-- Generated Variants --}}
        @if(!empty($generatedVariants))
        <div class="mt-6 space-y-4">
            <h5 class="text-sm font-semibold text-gray-900">Generated Variants ({{ count($generatedVariants) }})</h5>
            
            @foreach($generatedVariants as $index => $variant)
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Variant Name</label>
                        <input type="text" 
                               wire:model="generatedVariants.{{ $index }}.name" 
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg"
                               readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">SKU</label>
                        <input type="text" 
                               wire:model="generatedVariants.{{ $index }}.sku" 
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg"
                               placeholder="Auto">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Price *</label>
                        <input type="number" 
                               wire:model="generatedVariants.{{ $index }}.price" 
                               step="0.01"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg"
                               placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Stock</label>
                        <input type="number" 
                               wire:model="generatedVariants.{{ $index }}.stock_quantity" 
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg"
                               placeholder="0">
                    </div>
                </div>
            </div>
            @endforeach

            <button wire:click="saveVariants" 
                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                Save All Variants
            </button>
        </div>
        @endif
    </div>
    @endif

    {{-- Existing Variants List --}}
    @if(!empty($variants))
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Variant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($variants as $variant)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $variant['name'] ?? 'Default' }}</div>
                        @if($variant['is_default'])
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            Default
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $variant['sku'] }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ currency_format($variant['price']) }}</div>
                        @if($variant['sale_price'])
                        <div class="text-xs text-gray-500">{{ currency_format($variant['sale_price']) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $variant['stock_quantity'] }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $variant['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $variant['is_active'] ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="deleteVariant({{ $variant['id'] }})" 
                                class="text-red-600 hover:text-red-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-12 text-center">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
        <p class="text-gray-600 font-medium">No variants yet</p>
        <p class="text-sm text-gray-500 mt-1">Click "Generate Variants" to create product variations</p>
    </div>
    @endif
</div>
