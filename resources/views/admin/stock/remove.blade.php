@extends('layouts.admin')

@section('title', 'Remove Stock')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Remove Stock</h1>
                    <p class="text-gray-600 mt-1">Record damaged, lost, or other stock reductions</p>
                </div>
                <a href="{{ route('admin.stock.movements') }}" class="text-blue-600 hover:text-blue-700">
                    ← Back to Movements
                </a>
            </div>
        </div>


        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('admin.stock.remove.store') }}">
                @csrf

                <div class="mb-6">
                    @livewire('stock.product-selector', ['preSelectedProductId' => old('product_id'), 'preSelectedVariantId' => old('variant_id')])
                    @error('product_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('variant_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse *</label>
                    <select name="warehouse_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select warehouse</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                    @error('warehouse_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Removal Type *</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition">
                            <input type="radio" name="type" value="out" {{ old('type') == 'out' ? 'checked' : '' }} required class="sr-only peer">
                            <div class="peer-checked:border-blue-600 peer-checked:bg-blue-50 absolute inset-0 rounded-lg border-2"></div>
                            <div class="relative flex flex-col items-center justify-center w-full">
                                <svg class="w-8 h-8 text-gray-600 peer-checked:text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                                <span class="text-sm font-medium">Stock Out</span>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-orange-500 transition">
                            <input type="radio" name="type" value="damaged" {{ old('type') == 'damaged' ? 'checked' : '' }} required class="sr-only peer">
                            <div class="peer-checked:border-orange-600 peer-checked:bg-orange-50 absolute inset-0 rounded-lg border-2"></div>
                            <div class="relative flex flex-col items-center justify-center w-full">
                                <svg class="w-8 h-8 text-gray-600 peer-checked:text-orange-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span class="text-sm font-medium">Damaged</span>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-500 transition">
                            <input type="radio" name="type" value="lost" {{ old('type') == 'lost' ? 'checked' : '' }} required class="sr-only peer">
                            <div class="peer-checked:border-gray-600 peer-checked:bg-gray-50 absolute inset-0 rounded-lg border-2"></div>
                            <div class="relative flex flex-col items-center justify-center w-full">
                                <svg class="w-8 h-8 text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span class="text-sm font-medium">Lost</span>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input type="number" name="quantity" value="{{ old('quantity') }}" min="1" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Enter quantity to remove">
                    @error('quantity')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason *</label>
                    <input type="text" name="reason" value="{{ old('reason') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., Water damage, Expired, Missing">
                    @error('reason')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Additional details (optional)">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.stock.movements') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                        Remove Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
