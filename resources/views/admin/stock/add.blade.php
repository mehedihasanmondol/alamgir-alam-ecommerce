@extends('layouts.admin')

@section('title', 'Add Stock')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Add Stock</h1>
                    <p class="text-gray-600 mt-1">Receive new stock from suppliers or returns</p>
                </div>
                <a href="{{ route('admin.stock.movements') }}" class="text-blue-600 hover:text-blue-700">
                    ← Back to Movements
                </a>
            </div>
        </div>


        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('admin.stock.add.store') }}" x-data="stockForm()">
                @csrf

                <!-- Product Selection with Livewire -->
                <div class="mb-6">
                    @livewire('stock.product-selector', ['preSelectedProductId' => old('product_id'), 'preSelectedVariantId' => old('variant_id')])
                    @error('product_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('variant_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Warehouse Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse *</label>
                    <select name="warehouse_id" x-model="warehouseId" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select warehouse</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                    @error('warehouse_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantity -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input type="number" name="quantity" value="{{ old('quantity') }}" x-model="quantity" @input="calculateTotal" min="1" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter quantity">
                    @error('quantity')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unit Cost -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Cost</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500">৳</span>
                        <input type="number" name="unit_cost" value="{{ old('unit_cost') }}" x-model="unitCost" @input="calculateTotal" step="0.01" min="0"
                               class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="0.00">
                    </div>
                    @error('unit_cost')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    
                    <!-- Total Cost Display -->
                    <div x-show="totalCost > 0" class="mt-2 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-900">
                            <span class="font-medium">Total Cost:</span> 
                            <span class="text-lg font-bold" x-text="'৳' + totalCost.toFixed(2)"></span>
                        </p>
                    </div>
                </div>

                <!-- Supplier -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                    <select name="supplier_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select supplier (optional)</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }} ({{ $supplier->code }})</option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reason -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <input type="text" name="reason" value="{{ old('reason') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Purchase from supplier, Customer return">
                    @error('reason')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Additional notes (optional)">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.stock.movements') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function stockForm() {
    return {
        productId: '',
        warehouseId: '',
        quantity: 0,
        unitCost: 0,
        totalCost: 0,
        
        onProductChange() {
            // You can fetch variant options here if needed
            console.log('Product changed:', this.productId);
        },
        
        calculateTotal() {
            this.totalCost = this.quantity * this.unitCost;
        }
    }
}
</script>
@endpush
@endsection
