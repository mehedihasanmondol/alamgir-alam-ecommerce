@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create Delivery Zone</h1>
                <p class="mt-1 text-sm text-gray-600">Add a new geographic shipping zone</p>
            </div>
            <a href="{{ route('admin.delivery.zones.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.delivery.zones.store') }}" method="POST" id="zoneForm">
        @csrf

        <div class="space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                
                <div class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Zone Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Code -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                            Code <span class="text-xs text-gray-500">(Auto-generated if empty)</span>
                        </label>
                        <input type="text" 
                               name="code" 
                               id="code" 
                               value="{{ old('code') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sort Order & Status -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                                Sort Order
                            </label>
                            <input type="number" 
                                   name="sort_order" 
                                   id="sort_order" 
                                   value="{{ old('sort_order', 0) }}"
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('sort_order') border-red-500 @enderror">
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Status
                            </label>
                            <label class="inline-flex items-center mt-2">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Geographic Coverage -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-globe mr-2 text-blue-600"></i>
                    Geographic Coverage
                </h2>
                
                <div class="space-y-4">
                    <!-- Countries -->
                    <div>
                        <label for="countries" class="block text-sm font-medium text-gray-700 mb-1">
                            Countries <span class="text-xs text-gray-500">(Comma-separated country codes)</span>
                        </label>
                        <input type="text" 
                               name="countries" 
                               id="countries" 
                               value="{{ is_array(old('countries')) ? implode(', ', old('countries')) : old('countries') }}"
                               placeholder="BD, IN, US"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('countries') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Example: BD, IN, US, UK</p>
                        @error('countries')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- States -->
                    <div>
                        <label for="states" class="block text-sm font-medium text-gray-700 mb-1">
                            States/Provinces <span class="text-xs text-gray-500">(Comma-separated)</span>
                        </label>
                        <input type="text" 
                               name="states" 
                               id="states" 
                               value="{{ is_array(old('states')) ? implode(', ', old('states')) : old('states') }}"
                               placeholder="Dhaka, California, Maharashtra"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('states') border-red-500 @enderror">
                        @error('states')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cities -->
                    <div>
                        <label for="cities" class="block text-sm font-medium text-gray-700 mb-1">
                            Cities <span class="text-xs text-gray-500">(Comma-separated)</span>
                        </label>
                        <input type="text" 
                               name="cities" 
                               id="cities" 
                               value="{{ is_array(old('cities')) ? implode(', ', old('cities')) : old('cities') }}"
                               placeholder="Dhaka City, Los Angeles, Mumbai"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('cities') border-red-500 @enderror">
                        @error('cities')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Postal Codes -->
                    <div>
                        <label for="postal_codes" class="block text-sm font-medium text-gray-700 mb-1">
                            Postal Codes <span class="text-xs text-gray-500">(Comma-separated)</span>
                        </label>
                        <input type="text" 
                               name="postal_codes" 
                               id="postal_codes" 
                               value="{{ is_array(old('postal_codes')) ? implode(', ', old('postal_codes')) : old('postal_codes') }}"
                               placeholder="1000, 1200, 90001"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('postal_codes') border-red-500 @enderror">
                        @error('postal_codes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.delivery.zones.index') }}" 
                   class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Create Zone
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('zoneForm').addEventListener('submit', function(e) {
    // Convert comma-separated strings to arrays
    const fields = ['countries', 'states', 'cities', 'postal_codes'];
    
    fields.forEach(field => {
        const input = document.querySelector(`input[name="${field}"]`);
        if (input && input.value) {
            // Split by comma, trim whitespace, filter empty values
            const values = input.value.split(',')
                .map(v => v.trim())
                .filter(v => v.length > 0);
            
            // Remove original input
            input.remove();
            
            // Add hidden inputs for each value
            values.forEach((value, index) => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = `${field}[]`;
                hidden.value = value;
                this.appendChild(hidden);
            });
        }
    });
});
</script>
@endpush
@endsection
