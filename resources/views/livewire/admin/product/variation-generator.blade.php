<div class="space-y-4">
    {{-- Flash Messages --}}
    @if (session()->has('success'))
    <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if (session()->has('error'))
    <div class="p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        @if(!$showGenerator && count($generatedVariations) == 0)
        <button wire:click="toggleGenerator" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
            + Generate Variations
        </button>
        @endif
    </div>

    {{-- Variation Generator --}}
    @if($showGenerator || count($variations) > 0)
    <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-6">
        <div class="flex items-center justify-between border-b border-gray-200 pb-4">
            <h4 class="font-semibold text-gray-900">Variation Generator</h4>
            @if($showGenerator)
            <button wire:click="toggleGenerator" 
                    class="text-sm text-gray-600 hover:text-gray-800">
                Cancel
            </button>
            @endif
        </div>

        {{-- Step 1: Select Attributes --}}
        @if(count($variations) == 0)
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <label class="block text-sm font-semibold text-gray-700">Select Attributes</label>
                <button wire:click="addAttribute" 
                        class="px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                    + Add Attribute
                </button>
            </div>

            @foreach($selectedAttributes as $index => $selected)
            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex-1 space-y-3">
                    {{-- Attribute Selection --}}
                    <div>
                        <select wire:model.live="selectedAttributes.{{ $index }}.attribute_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Attribute</option>
                            @foreach($attributes as $attribute)
                            <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Values Selection --}}
                    @if(!empty($selectedAttributes[$index]['attribute_id']))
                    <div>
                        <label class="block text-xs text-gray-600 mb-2">Select Values:</label>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $selectedAttr = $attributes->find($selectedAttributes[$index]['attribute_id']);
                            @endphp
                            @if($selectedAttr)
                                @foreach($selectedAttr->values as $value)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" 
                                           wire:model="selectedAttributes.{{ $index }}.value_ids" 
                                           value="{{ $value->id }}"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $value->value }}</span>
                                </label>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <button wire:click="removeAttribute({{ $index }})" 
                        class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @endforeach

            @if(count($selectedAttributes) == 0)
            <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                <p class="text-gray-600">Click "Add Attribute" to start creating variations</p>
            </div>
            @endif

            {{-- Generate Button --}}
            @if(count($selectedAttributes) > 0)
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button wire:click="generateVariations" 
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Generate Variations
                </button>
            </div>
            @endif
        </div>
        @endif

        {{-- Step 2: Edit Generated Variations --}}
        @if(count($variations) > 0)
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h5 class="font-medium text-gray-900">Generated Variations ({{ count($variations) }})</h5>
                <div class="flex items-center gap-3">
                    <button wire:click="$set('variations', [])" 
                            class="text-sm text-gray-600 hover:text-gray-800">
                        Start Over
                    </button>
                </div>
            </div>

            <div class="space-y-4" 
                 x-data="{ expandedIndex: null }" 
                 @variation-saved.window="expandedIndex = null"
                 @variations-generated.window="expandedIndex = null">
                @foreach($variations as $index => $variation)
                <div class="border border-gray-200 rounded-lg {{ !$variation['enabled'] ? 'opacity-50' : '' }}">
                    {{-- Header --}}
                    <div class="p-4 flex items-center gap-4">
                        <input type="checkbox" 
                               wire:model="variations.{{ $index }}.enabled" 
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ $variation['name'] }}</div>
                            <div class="text-sm text-gray-500">
                                Price: @currencySymbol{{ $variation['price'] ?? '0.00' }} | Stock: {{ $variation['stock_quantity'] ?? '0' }}
                            </div>
                        </div>

                        <button @click="expandedIndex = expandedIndex === {{ $index }} ? null : {{ $index }}" 
                                type="button"
                                class="px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                            <span x-show="expandedIndex !== {{ $index }}">Edit Details</span>
                            <span x-show="expandedIndex === {{ $index }}">Hide Details</span>
                        </button>
                    </div>

                    {{-- Expanded Details --}}
                    <div x-show="expandedIndex === {{ $index }}" 
                         x-collapse
                         class="border-t border-gray-200 p-4 bg-gray-50 space-y-4">
                        
                        {{-- Basic Info --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Variation Name *</label>
                                <input type="text" 
                                       wire:model="variations.{{ $index }}.name" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="e.g., Small - Red">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                                <input type="text" 
                                       wire:model="variations.{{ $index }}.sku" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="Auto-generated">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
                                <input type="number" 
                                       wire:model="variations.{{ $index }}.stock_quantity" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="0">
                            </div>
                        </div>

                        {{-- Pricing --}}
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Pricing</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Regular Price ($) *</label>
                                    <input type="number" 
                                           wire:model="variations.{{ $index }}.price" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                           placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sale Price ($)</label>
                                    <input type="number" 
                                           wire:model="variations.{{ $index }}.sale_price" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                           placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cost Price ($)</label>
                                    <input type="number" 
                                           wire:model="variations.{{ $index }}.cost_price" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                           placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        {{-- Inventory --}}
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Inventory</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Low Stock Alert</label>
                                    <input type="number" 
                                           wire:model="variations.{{ $index }}.low_stock_alert" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                           placeholder="5">
                                </div>
                            </div>
                        </div>

                        {{-- Shipping --}}
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Shipping Information</h4>
                            <div class="grid grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                                    <input type="number" 
                                           wire:model="variations.{{ $index }}.weight" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                           placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Length (cm)</label>
                                    <input type="number" 
                                           wire:model="variations.{{ $index }}.length" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                           placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Width (cm)</label>
                                    <input type="number" 
                                           wire:model="variations.{{ $index }}.width" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                           placeholder="0.00">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Height (cm)</label>
                                    <input type="number" 
                                           wire:model="variations.{{ $index }}.height" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                           placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Save Variations --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button wire:click="$set('variations', [])" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="saveVariations" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Save Variations
                </button>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Existing Variations --}}
    @if(count($editingVariations) > 0)
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h4 class="font-semibold text-gray-900">Existing Variations ({{ count($editingVariations) }})</h4>
            <button wire:click="toggleGenerator" 
                    class="px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                + Add More
            </button>
        </div>

        <div class="space-y-4 p-4" 
             x-data="{ expandedIndex: null }"
             @variation-updated.window="expandedIndex = null">
            @foreach($editingVariations as $index => $variation)
            <div class="border border-gray-200 rounded-lg">
                {{-- Header --}}
                <div class="p-4 flex items-center gap-4">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">{{ $variation['name'] }}</div>
                        <div class="text-sm text-gray-500">
                            Price: ${{ $variation['price'] ?? '0.00' }} | Stock: {{ $variation['stock_quantity'] ?? '0' }}
                        </div>
                    </div>

                    <button @click="expandedIndex = expandedIndex === {{ $index }} ? null : {{ $index }}" 
                            type="button"
                            class="px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                        <span x-show="expandedIndex !== {{ $index }}">Edit Details</span>
                        <span x-show="expandedIndex === {{ $index }}">Hide Details</span>
                    </button>
                    
                    <button wire:click="deleteVariation({{ $variation['id'] }})" 
                            wire:confirm="Are you sure you want to delete this variation?"
                            class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>

                {{-- Expanded Details --}}
                <div x-show="expandedIndex === {{ $index }}" 
                     x-collapse
                     class="border-t border-gray-200 p-4 bg-gray-50 space-y-4">
                    
                    {{-- Basic Info --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Variation Name *</label>
                            <input type="text" 
                                   wire:model="editingVariations.{{ $index }}.name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                   placeholder="e.g., Small - Red">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                            <input type="text" 
                                   wire:model="editingVariations.{{ $index }}.sku" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                   placeholder="Auto-generated">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
                            <input type="number" 
                                   wire:model="editingVariations.{{ $index }}.stock_quantity" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                   placeholder="0">
                        </div>
                    </div>

                    {{-- Pricing --}}
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Pricing</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Regular Price ($) *</label>
                                <input type="number" 
                                       wire:model="editingVariations.{{ $index }}.price" 
                                       step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sale Price ($)</label>
                                <input type="number" 
                                       wire:model="editingVariations.{{ $index }}.sale_price" 
                                       step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cost Price ($)</label>
                                <input type="number" 
                                       wire:model="editingVariations.{{ $index }}.cost_price" 
                                       step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    {{-- Inventory --}}
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Inventory</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Low Stock Alert</label>
                                <input type="number" 
                                       wire:model="editingVariations.{{ $index }}.low_stock_alert" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="5">
                            </div>
                        </div>
                    </div>

                    {{-- Shipping --}}
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Shipping Information</h4>
                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                                <input type="number" 
                                       wire:model="editingVariations.{{ $index }}.weight" 
                                       step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Length (cm)</label>
                                <input type="number" 
                                       wire:model="editingVariations.{{ $index }}.length" 
                                       step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Width (cm)</label>
                                <input type="number" 
                                       wire:model="editingVariations.{{ $index }}.width" 
                                       step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Height (cm)</label>
                                <input type="number" 
                                       wire:model="editingVariations.{{ $index }}.height" 
                                       step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    {{-- Variation Image --}}
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Variation Image</h4>
                        
                        {{-- Current/Preview Image --}}
                        @if(isset($editingVariations[$index]['preview_url']) || (isset($editingVariations[$index]['image']) && $editingVariations[$index]['image']))
                        <div class="mb-4">
                            <div class="relative inline-block">
                                @if(isset($editingVariations[$index]['preview_url']))
                                    {{-- New Image Preview --}}
                                    <img src="{{ $editingVariations[$index]['preview_url'] }}" 
                                         alt="New Variation Image" 
                                         class="w-40 h-40 object-cover rounded-lg border-2 border-blue-500">
                                    <span class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">New</span>
                                    <button wire:click="removeNewVariationImage({{ $index }})" 
                                            type="button"
                                            class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1.5 hover:bg-red-700 shadow-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                @elseif(isset($editingVariations[$index]['image']) && $editingVariations[$index]['image'])
                                    {{-- Existing Image --}}
                                    <img src="{{ asset('storage/' . $editingVariations[$index]['image']) }}" 
                                         alt="Current Variation Image" 
                                         class="w-40 h-40 object-cover rounded-lg border-2 border-gray-300">
                                    <span class="absolute top-2 left-2 bg-gray-600 text-white text-xs px-2 py-1 rounded">Current</span>
                                    <button wire:click="removeVariationImage({{ $index }})" 
                                            type="button"
                                            class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1.5 hover:bg-red-700 shadow-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Upload Input --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                @if(isset($editingVariations[$index]['preview_url']) || (isset($editingVariations[$index]['image']) && $editingVariations[$index]['image']))
                                    Replace Image
                                @else
                                    Upload Image
                                @endif
                            </label>
                            <input type="file" 
                                   wire:model="variationImages.{{ $index }}" 
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            
                            {{-- Loading Indicator --}}
                            <div wire:loading wire:target="variationImages.{{ $index }}" class="mt-2">
                                <div class="flex items-center gap-2 text-sm text-blue-600">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Uploading...</span>
                                </div>
                            </div>
                            
                            <p class="text-xs text-gray-500 mt-1">
                                @if(isset($editingVariations[$index]['preview_url']) || (isset($editingVariations[$index]['image']) && $editingVariations[$index]['image']))
                                    Choose a new image to replace the current one
                                @else
                                    Upload a specific image for this variation
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    {{-- Update Button --}}
                    <div class="flex justify-end pt-4 border-t border-gray-200">
                        <button wire:click="updateExistingVariation({{ $index }})" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Update Variation
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Empty State --}}
    @if(!$showGenerator && count($variations) == 0 && count($generatedVariations) == 0)
    <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Variations Yet</h3>
        <p class="text-gray-600 mb-4">Create product variations based on attributes like size, color, material, etc.</p>
        <button wire:click="toggleGenerator" 
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Generate Variations
        </button>
    </div>
    @endif
</div>
