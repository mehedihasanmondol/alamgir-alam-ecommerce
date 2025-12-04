<div class="space-y-4">
    {{-- Success Message --}}
    @if (session()->has('success'))
    <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <button wire:click="toggleAddAttribute" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
            {{ $showAddAttribute ? '✕ Cancel' : '+ Add New Attribute' }}
        </button>
    </div>

    {{-- Add Attribute Form --}}
    @if($showAddAttribute)
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-4">
        <h4 class="font-medium text-gray-900">Create New Attribute</h4>
        
        {{-- Attribute Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Attribute Name *</label>
            <input type="text" 
                   wire:model="newAttribute.name" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="e.g., Size, Color, Material">
            @error('newAttribute.name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Attribute Type --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Display Type *</label>
            <select wire:model="newAttribute.type" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="select">Dropdown Select</option>
                <option value="button">Button/Swatch</option>
                <option value="color">Color Picker</option>
                <option value="image">Image Swatch</option>
            </select>
        </div>

        {{-- Attribute Values --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Values *</label>
            <div class="space-y-2">
                @foreach($newAttribute['values'] as $index => $value)
                <div class="flex items-center gap-2">
                    <input type="text" 
                           wire:model="newAttribute.values.{{ $index }}" 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="e.g., Small, Medium, Large">
                    @if(count($newAttribute['values']) > 1)
                    <button wire:click="removeValueField({{ $index }})" 
                            type="button"
                            class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    @endif
                </div>
                @endforeach
            </div>
            <button wire:click="addValueField" 
                    type="button"
                    class="mt-2 px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                + Add Value
            </button>
            @error('newAttribute.values') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2 pt-2">
            <button wire:click="saveAttribute" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                Save Attribute
            </button>
            <button wire:click="toggleAddAttribute" 
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
        </div>
    </div>
    @endif

    {{-- Attributes List --}}
    @if(count($productAttributes) > 0)
    <div class="space-y-3">
        @foreach($productAttributes as $attribute)
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h4 class="font-semibold text-gray-900">{{ $attribute['name'] }}</h4>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ ucfirst($attribute['type']) }}</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($attribute['values'] as $value)
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">
                            {{ $value['value'] }}
                        </span>
                        @endforeach
                    </div>
                </div>
                <button wire:click="deleteAttribute({{ $attribute['id'] }})" 
                        wire:confirm="Are you sure you want to delete this attribute?"
                        class="ml-4 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>
        <p class="text-gray-600">No attributes created yet</p>
        <p class="text-sm text-gray-500 mt-1">Click "Add Attribute" to create your first attribute</p>
    </div>
    @endif
</div>
