@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create Delivery Method</h1>
                <p class="mt-1 text-sm text-gray-600">Add a new shipping method with pricing configuration</p>
            </div>
            <a href="{{ route('admin.delivery.methods.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.delivery.methods.store') }}" method="POST">
        @csrf

        <div class="space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                
                <div class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Method Name <span class="text-red-500">*</span>
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

                    <!-- Calculation Type -->
                    <div>
                        <label for="calculation_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Calculation Type <span class="text-red-500">*</span>
                        </label>
                        <select name="calculation_type" 
                                id="calculation_type"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('calculation_type') border-red-500 @enderror">
                            <option value="">Select Type</option>
                            <option value="flat_rate" {{ old('calculation_type') == 'flat_rate' ? 'selected' : '' }}>Flat Rate</option>
                            <option value="weight_based" {{ old('calculation_type') == 'weight_based' ? 'selected' : '' }}>Weight Based</option>
                            <option value="price_based" {{ old('calculation_type') == 'price_based' ? 'selected' : '' }}>Price Based</option>
                            <option value="item_based" {{ old('calculation_type') == 'item_based' ? 'selected' : '' }}>Item Based</option>
                            <option value="free" {{ old('calculation_type') == 'free' ? 'selected' : '' }}>Free Shipping</option>
                        </select>
                        @error('calculation_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Carrier Name & Estimated Days -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="carrier_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Carrier Name <span class="text-xs text-gray-500">(Optional)</span>
                            </label>
                            <input type="text" 
                                   name="carrier_name" 
                                   id="carrier_name" 
                                   value="{{ old('carrier_name') }}"
                                   placeholder="FedEx, DHL, UPS"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('carrier_name') border-red-500 @enderror">
                            @error('carrier_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="estimated_days" class="block text-sm font-medium text-gray-700 mb-1">
                                Estimated Days <span class="text-xs text-gray-500">(Optional)</span>
                            </label>
                            <input type="text" 
                                   name="estimated_days" 
                                   id="estimated_days" 
                                   value="{{ old('estimated_days') }}"
                                   placeholder="2-3 business days"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('estimated_days') border-red-500 @enderror">
                            @error('estimated_days')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
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

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.delivery.methods.index') }}" 
                   class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Create Method
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
