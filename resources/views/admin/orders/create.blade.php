@extends('layouts.admin')

@section('title', 'Create Order')

@section('content')
<div class="space-y-4" x-data="orderForm()" x-cloak @product-selected.window="addSelectedProduct($event.detail.productData || $event.detail)">
    <!-- Sticky Header with Live Summary & Actions -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 shadow-lg sticky top-16 z-20">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Left: Title & Back -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.orders.index') }}" 
                       class="p-2 hover:bg-white/20 rounded-lg transition-colors text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div class="text-white">
                        <h1 class="text-lg font-bold">Create New Order</h1>
                        <p class="text-sm text-blue-100">Quick order creation</p>
                    </div>
                </div>
                
                <!-- Center: Live Order Summary -->
                <div class="flex items-center space-x-6 text-white">
                    <div class="text-center px-4 border-r border-white/20">
                        <p class="text-xs text-blue-100">Items</p>
                        <p class="text-2xl font-bold" x-text="items.length">0</p>
                    </div>
                    <div class="text-center px-4 border-r border-white/20">
                        <p class="text-xs text-blue-100">Subtotal</p>
                        <p class="text-2xl font-bold" x-text="'৳' + calculateSubtotal().toFixed(2)">৳0.00</p>
                    </div>
                    <div class="text-center px-4">
                        <p class="text-xs text-blue-100">Total</p>
                        <p class="text-3xl font-bold" x-text="'৳' + calculateTotal().toFixed(2)">৳0.00</p>
                    </div>
                </div>

                <!-- Right: Action Buttons -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.orders.index') }}"
                       class="px-6 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/30 rounded-lg transition-colors font-medium">
                        Cancel
                    </a>
                    <button type="submit" form="order-form"
                            class="px-6 py-2.5 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors shadow-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Confirmation Modal -->
    <div x-show="showProductModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Modal Content (no backdrop) -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-2xl max-w-md w-full p-6 pointer-events-auto border-2 border-gray-200"
                 @click.outside="closeProductModal()"
                 @click.stop>
                <!-- Close Button -->
                <button @click="closeProductModal()" 
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <template x-if="tempProduct">
                    <div>
                        <!-- Header -->
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Confirm Product Details</h3>
                        
                        <!-- Product Info -->
                        <div class="flex items-start space-x-3 mb-4 p-3 bg-gray-50 rounded-lg">
                            <template x-if="tempProduct.image">
                                <img :src="tempProduct.image" :alt="tempProduct.name" 
                                     class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                            </template>
                            <template x-if="!tempProduct.image">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </template>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900" x-text="tempProduct.name"></h4>
                                <template x-if="tempProduct.variant_name">
                                    <p class="text-xs text-gray-600" x-text="'Variant: ' + tempProduct.variant_name"></p>
                                </template>
                                <p class="text-xs text-gray-500" x-text="'SKU: ' + tempProduct.sku"></p>
                                <template x-if="stockRestrictionEnabled && tempProduct.stock_quantity !== undefined">
                                    <p class="text-xs mt-1" :class="tempProduct.stock_quantity > 0 ? 'text-green-600' : 'text-red-600'">
                                        Stock: <span x-text="tempProduct.stock_quantity"></span>
                                    </p>
                                </template>
                            </div>
                        </div>

                        <!-- Quantity Input -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   x-model.number="tempProduct.quantity" 
                                   min="1" 
                                   :max="stockRestrictionEnabled ? (tempProduct.stock_quantity || 999) : 9999"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1" x-show="stockRestrictionEnabled" style="display: none;">
                                Available: <span x-text="tempProduct.stock_quantity || 'Unlimited'"></span>
                            </p>
                        </div>

                        <!-- Price Input -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Unit Price (৳) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   x-model.number="tempProduct.price" 
                                   step="0.01" 
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Subtotal Display -->
                        <div class="mb-6 p-3 bg-blue-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Subtotal:</span>
                                <span class="text-lg font-bold text-blue-600" 
                                      x-text="'৳' + (tempProduct.quantity * tempProduct.price).toFixed(2)">৳0.00</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-3">
                            <button type="button" 
                                    @click="closeProductModal()"
                                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="button" 
                                    @click="confirmAddProduct()"
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <span x-text="tempProduct.existingIndex !== -1 ? 'Update' : 'Add to Order'"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <form id="order-form" action="{{ route('admin.orders.store') }}" method="POST" @submit="if(items.length === 0) { window.dispatchEvent(new CustomEvent('alert-toast', { detail: { type: 'error', message: 'Please add at least one product to the order' } })); $event.preventDefault(); return false; }">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Left Column: Order Items & Customer -->
            <div class="lg:col-span-2 space-y-4">
                
                <!-- Order Items Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <!-- Card Header with Title -->
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                Order Items
                            </h3>
                            <span class="text-xs text-gray-500 bg-blue-50 px-2 py-1 rounded" x-show="items.length > 0">
                                <span x-text="items.length"></span> item<span x-show="items.length !== 1">s</span>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Sticky Search Bar (sticks below page header when scrolling) -->
                    <div class="sticky top-[140px] z-10 p-4 border-b border-gray-200 bg-white shadow-sm">
                        <label class="block text-xs font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search & Add Products
                        </label>
                        @livewire('order.product-selector')
                    </div>
                    
                    <div class="p-4">
                        <!-- Selected Items List -->
                        <div x-show="items.length > 0" class="space-y-3">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-blue-300 transition-colors">
                                    <!-- Hidden inputs for form submission -->
                                    <input type="hidden" :name="'items['+index+'][product_id]'" x-model="item.product_id">
                                    <input type="hidden" :name="'items['+index+'][variant_id]'" x-model="item.variant_id">
                                    <input type="hidden" :name="'items['+index+'][price]'" x-model="item.price">
                                    <input type="hidden" :name="'items['+index+'][quantity]'" x-model="item.quantity">
                                    
                                    <div class="flex items-start space-x-4">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0">
                                            <template x-if="item.image">
                                                <img :src="item.image" :alt="item.name" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                            </template>
                                            <template x-if="!item.image">
                                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Product Details -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h4 class="text-sm font-semibold text-gray-900" x-text="item.name"></h4>
                                                    <template x-if="item.variant_name">
                                                        <p class="text-xs text-gray-600 mt-0.5">Variant: <span x-text="item.variant_name"></span></p>
                                                    </template>
                                                    <p class="text-xs text-gray-500 mt-0.5">SKU: <span x-text="item.sku"></span></p>
                                                    <template x-if="stockRestrictionEnabled && item.stock_quantity !== undefined">
                                                        <p class="text-xs mt-1" :class="item.stock_quantity > 0 ? 'text-green-600' : 'text-red-600'">
                                                            Stock: <span x-text="item.stock_quantity"></span>
                                                        </p>
                                                    </template>
                                                </div>
                                                
                                                <!-- Remove Button -->
                                                <button type="button" @click="removeItem(index)" 
                                                        class="ml-3 p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                        title="Remove Item">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            
                                            <!-- Quantity and Price Controls -->
                                            <div class="grid grid-cols-3 gap-3 mt-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Quantity</label>
                                                    <input type="number" 
                                                           x-model.number="item.quantity" 
                                                           min="1" 
                                                           :max="stockRestrictionEnabled ? (item.stock_quantity || 999) : 9999"
                                                           class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-center">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Unit Price (৳)</label>
                                                    <input type="number" 
                                                           x-model.number="item.price" 
                                                           step="0.01" 
                                                           min="0"
                                                           class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-center">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Subtotal</label>
                                                    <div class="bg-blue-50 px-3 py-2 rounded-lg text-center">
                                                        <span class="text-sm font-bold text-blue-600" 
                                                              x-text="'৳' + (item.quantity * item.price).toFixed(2)">৳0.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Empty State -->
                        <div x-show="items.length === 0" class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <p class="text-sm font-medium">No items added yet</p>
                            <p class="text-xs text-gray-400 mt-1">Search and select products above to add them to the order</p>
                        </div>
                    </div>
                </div>

                <!-- Customer Information Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Customer Details
                        </h3>
                    </div>
                    
                    <div class="p-4 space-y-3">
                        <!-- Quick Customer Select -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Quick Select Customer (Optional)</label>
                            <select name="user_id" 
                                    @change="fillCustomerData($event)"
                                    class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">-- New Customer --</option>
                                @php
                                    $users = \App\Models\User::orderBy('name')->limit(100)->get();
                                @endphp
                                @foreach($users as $user)
                                    @php
                                        $nameParts = explode(' ', $user->name, 2);
                                        $firstName = $nameParts[0] ?? '';
                                        $lastName = $nameParts[1] ?? '';
                                    @endphp
                                    <option value="{{ $user->id }}" 
                                            data-name="{{ $user->name }}" 
                                            data-email="{{ $user->email }}" 
                                            data-phone="{{ $user->mobile ?? '' }}"
                                            data-first-name="{{ $firstName }}"
                                            data-last-name="{{ $lastName }}"
                                            data-address="{{ $user->address ?? '' }}"
                                            data-city="{{ $user->city ?? 'Dhaka' }}"
                                            data-postal="{{ $user->postal_code ?? '' }}"
                                            data-country="{{ $user->country ?? 'Bangladesh' }}">
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @if($users->isEmpty())
                                <p class="text-xs text-gray-500 mt-1">No existing customers - Enter new customer details below</p>
                            @else
                                <p class="text-xs text-green-600 mt-1">
                                    <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Selecting a customer will auto-fill their profile info to billing & shipping
                                </p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" required 
                                   value="{{ old('customer_name') }}"
                                   x-init="customer.name = '{{ old('customer_name') }}'"
                                   x-model="customer.name"
                                   class="w-full text-sm px-3 py-2 border {{ $errors->has('customer_name') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('customer_name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                            <!-- Hidden fields for billing (same as customer) -->
                            <input type="hidden" name="billing_first_name" id="billing_first_name" value="{{ old('billing_first_name') }}">
                            <input type="hidden" name="billing_last_name" id="billing_last_name" value="{{ old('billing_last_name') }}">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                                <input type="text" name="customer_phone" id="customer_phone" required 
                                       value="{{ old('customer_phone') }}"
                                       x-init="customer.phone = '{{ old('customer_phone') }}'"
                                       x-model="customer.phone"
                                       class="w-full text-sm px-3 py-2 border {{ $errors->has('customer_phone') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500">
                                @error('customer_phone')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                                <input type="hidden" name="billing_phone" id="billing_phone" value="{{ old('billing_phone') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="customer_email" id="customer_email" required 
                                       value="{{ old('customer_email') }}"
                                       x-init="customer.email = '{{ old('customer_email') }}'"
                                       x-model="customer.email"
                                       class="w-full text-sm px-3 py-2 border {{ $errors->has('customer_email') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500">
                                @error('customer_email')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                                <input type="hidden" name="billing_email" id="billing_email" value="{{ old('billing_email') }}">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_address" id="customer_address" required 
                                   value="{{ old('customer_address') }}"
                                   x-init="customer.address = '{{ old('customer_address') }}'"
                                   x-model="customer.address"
                                   class="w-full text-sm px-3 py-2 border {{ $errors->has('customer_address') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500"
                                   placeholder="House/Flat, Street, Area">
                            @error('customer_address')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Customer Notes</label>
                            <textarea name="customer_notes" rows="2"
                                      class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="Any special instructions or notes...">{{ old('customer_notes') }}</textarea>
                        </div>

                        <!-- Update Customer Info Button (Shows when customer selected and data changed) -->
                        <div x-show="selectedUserId && customerDataChanged" 
                             x-transition
                             class="pt-3 border-t border-gray-200">
                            <button type="button" 
                                    @click="updateCustomerInfo()"
                                    class="w-full px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-colors flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Update Customer Profile Permanently
                            </button>
                            <p class="text-xs text-gray-500 mt-2 text-center">
                                This will save the changes to the customer's profile for future orders
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Shipping Address
                            </h3>
                            <div class="flex items-center space-x-3">
                                @livewire('admin.order.customer-address-selector')
                                <label class="flex items-center text-sm cursor-pointer">
                                    <input type="checkbox" name="same_as_billing" id="same_as_billing" value="1"
                                           @change="toggleShippingAddress()"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mr-2">
                                    <span class="text-gray-700">Same as customer</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 space-y-3" id="shipping-address-fields" style="display: block;">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="shipping_name" id="shipping_name"
                                   value="{{ old('shipping_name') }}"
                                   class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   placeholder="Recipient name">
                            <!-- Hidden fields to match backend expectations -->
                            <input type="hidden" name="shipping_first_name" id="shipping_first_name" value="{{ old('shipping_first_name') }}">
                            <input type="hidden" name="shipping_last_name" id="shipping_last_name" value="{{ old('shipping_last_name') }}">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" name="shipping_phone" id="shipping_phone"
                                       value="{{ old('shipping_phone') }}"
                                       class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="Recipient phone">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="shipping_email" id="shipping_email"
                                       value="{{ old('shipping_email') }}"
                                       class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="Recipient email">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="shipping_address_line_1" id="shipping_address_line_1"
                                   value="{{ old('shipping_address_line_1') }}"
                                   class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   placeholder="House/Flat, Street, Area">
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <p class="text-xs text-blue-700">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Uncheck "Same as customer info" to ship to a different address
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary & Payment -->
            <div class="space-y-4">
                
                <!-- Order Summary Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Order Summary
                        </h3>
                    </div>
                    
                    <div class="p-4 space-y-3">
                        <!-- Subtotal -->
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold" x-text="'৳' + calculateSubtotal().toFixed(2)">৳0.00</span>
                        </div>

                        <!-- Shipping -->
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Shipping</span>
                            <input type="number" name="shipping_cost" 
                                   value="{{ old('shipping_cost', 60) }}"
                                   x-init="shipping = {{ old('shipping_cost', 60) }}"
                                   x-model.number="shipping" step="0.01" min="0" required
                                   class="w-24 text-right text-sm px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Discount -->
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Discount</span>
                            <input type="number" name="discount_amount" 
                                   value="{{ old('discount_amount', 0) }}"
                                   x-init="discount = {{ old('discount_amount', 0) }}"
                                   x-model.number="discount" step="0.01" min="0"
                                   class="w-24 text-right text-sm px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-semibold text-gray-900">Total</span>
                                <span class="text-2xl font-bold text-blue-600" x-text="'৳' + calculateTotal().toFixed(2)">৳0.00</span>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="pt-3 border-t border-gray-200">
                            <label class="block text-xs font-medium text-gray-700 mb-2">Payment Method <span class="text-red-500">*</span></label>
                            <select name="payment_method" required
                                    class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                                <option value="bkash" {{ old('payment_method') == 'bkash' ? 'selected' : '' }}>bKash</option>
                                <option value="nagad" {{ old('payment_method') == 'nagad' ? 'selected' : '' }}>Nagad</option>
                                <option value="rocket" {{ old('payment_method') == 'rocket' ? 'selected' : '' }}>Rocket</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            </select>
                        </div>

                        <!-- Payment Status -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Payment Status <span class="text-red-500">*</span></label>
                            <select name="payment_status" required
                                    class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="pending" {{ old('payment_status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>

                        <!-- Admin Notes -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Admin Notes</label>
                            <textarea name="admin_notes" rows="2"
                                      class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="Internal notes...">{{ old('admin_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function orderForm() {
    return {
        // Check if stock restriction is enabled
        stockRestrictionEnabled: {{ \App\Modules\Ecommerce\Product\Models\ProductVariant::isStockRestrictionEnabled() ? 'true' : 'false' }},
        items: @json(old('items', [])),
        shipping: {{ old('shipping_cost', 60) }},
        discount: {{ old('discount', 0) }},
        showProductModal: false,
        tempProduct: null,
        customer: {
            name: '{{ old('customer_name', '') }}',
            email: '{{ old('customer_email', '') }}',
            phone: '{{ old('customer_phone', '') }}',
            address: '{{ old('customer_address', '') }}'
        },
        originalCustomer: {
            name: '{{ old('customer_name', '') }}',
            email: '{{ old('customer_email', '') }}',
            phone: '{{ old('customer_phone', '') }}',
            address: '{{ old('customer_address', '') }}'
        },
        selectedUserId: null,
        customerDataChanged: false,
        
        init() {
            // Watch customer fields and sync with billing hidden fields
            this.$watch('customer.name', value => {
                const nameParts = value.split(' ', 2);
                document.getElementById('billing_first_name').value = nameParts[0] || '';
                document.getElementById('billing_last_name').value = nameParts[1] || '';
            });
            this.$watch('customer.phone', value => {
                document.getElementById('billing_phone').value = value;
            });
            this.$watch('customer.email', value => {
                document.getElementById('billing_email').value = value;
            });
            this.$watch('customer.address', value => {
                document.getElementById('billing_address_line_1').value = value;
            });
            
            // Watch for changes to detect if customer data has been modified
            this.$watch('customer', value => {
                if (this.selectedUserId) {
                    this.customerDataChanged = 
                        value.name !== this.originalCustomer.name ||
                        value.email !== this.originalCustomer.email ||
                        value.phone !== this.originalCustomer.phone ||
                        value.address !== this.originalCustomer.address;
                }
            }, { deep: true });
        },
        
        addSelectedProduct(productData) {
            console.log('Product selected:', productData);
            
            if (!productData || !productData.product_id) {
                console.error('Invalid product data received:', productData);
                return;
            }
            
            // Check if product already exists in items
            const existingIndex = this.items.findIndex(item => 
                item.product_id === productData.product_id && 
                item.variant_id === productData.variant_id
            );
            
            if (existingIndex !== -1) {
                // If product exists, open modal to edit it
                this.tempProduct = {...this.items[existingIndex], existingIndex: existingIndex};
                this.showProductModal = true;
            } else {
                // Show modal for new product
                this.tempProduct = {
                    product_id: productData.product_id,
                    variant_id: productData.variant_id || null,
                    name: productData.name,
                    variant_name: productData.variant_name || null,
                    sku: productData.sku,
                    price: productData.sale_price || productData.price,
                    quantity: 1,
                    stock_quantity: productData.stock_quantity,
                    image: productData.image,
                    existingIndex: -1
                };
                this.showProductModal = true;
            }
        },
        
        confirmAddProduct() {
            if (this.tempProduct.existingIndex !== -1) {
                // Update existing product
                this.items[this.tempProduct.existingIndex].quantity = this.tempProduct.quantity;
                this.items[this.tempProduct.existingIndex].price = this.tempProduct.price;
                console.log('Updated existing product');
            } else {
                // Add new product
                const newProduct = {...this.tempProduct};
                delete newProduct.existingIndex;
                this.items.push(newProduct);
                console.log('Added new product to items:', this.items);
            }
            this.closeProductModal();
        },
        
        closeProductModal() {
            this.showProductModal = false;
            this.tempProduct = null;
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
        },
        
        fillCustomerData(event) {
            const selectedOption = event.target.options[event.target.selectedIndex];
            if (selectedOption.value) {
                // Store selected user ID
                this.selectedUserId = selectedOption.value;
                
                // Fill customer info (will auto-sync with billing via watchers)
                this.customer.name = selectedOption.dataset.name || '';
                this.customer.email = selectedOption.dataset.email || '';
                this.customer.phone = selectedOption.dataset.phone || '';
                this.customer.address = selectedOption.dataset.address || '';
                
                // Store original values for comparison
                this.originalCustomer = {
                    name: this.customer.name,
                    email: this.customer.email,
                    phone: this.customer.phone,
                    address: this.customer.address
                };
                
                // Reset change flag
                this.customerDataChanged = false;
                
                // Trigger Livewire to load customer addresses
                window.Livewire.dispatch('customerSelected', { userId: selectedOption.value });
                
                // If shipping is different, also fill shipping fields
                const sameAsBilling = document.getElementById('same_as_billing');
                if (!sameAsBilling.checked) {
                    const nameParts = (selectedOption.dataset.name || '').split(' ', 2);
                    document.getElementById('shipping_name').value = selectedOption.dataset.name || '';
                    document.getElementById('shipping_first_name').value = nameParts[0] || '';
                    document.getElementById('shipping_last_name').value = nameParts[1] || '';
                    document.getElementById('shipping_phone').value = selectedOption.dataset.phone || '';
                    document.getElementById('shipping_email').value = selectedOption.dataset.email || '';
                    document.getElementById('shipping_address_line_1').value = selectedOption.dataset.address || '';
                }
            } else {
                // Clear all fields if "New Customer" is selected
                this.selectedUserId = null;
                this.customerDataChanged = false;
                this.customer.name = '';
                this.customer.email = '';
                this.customer.phone = '';
                this.customer.address = '';
                
                // Clear Livewire component
                window.Livewire.dispatch('customerSelected', { userId: null });
            }
        },
        
        updateCustomerInfo() {
            if (!this.selectedUserId) return;
            
            // Send AJAX request to update customer info
            fetch(`/admin/customers/${this.selectedUserId}/update-info`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: this.customer.name,
                    email: this.customer.email,
                    phone: this.customer.phone,
                    address: this.customer.address
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update original values
                    this.originalCustomer = {
                        name: this.customer.name,
                        email: this.customer.email,
                        phone: this.customer.phone,
                        address: this.customer.address
                    };
                    this.customerDataChanged = false;
                    
                    // Show success message
                    window.dispatchEvent(new CustomEvent('alert-toast', { 
                        detail: { type: 'success', message: 'Customer profile updated successfully!' } 
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('alert-toast', { 
                        detail: { type: 'error', message: 'Failed to update customer profile: ' + (data.message || 'Unknown error') } 
                    }));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('alert-toast', { 
                    detail: { type: 'error', message: 'Failed to update customer profile. Please try again.' } 
                }));
            });
        },
        
        calculateSubtotal() {
            return this.items.reduce((sum, item) => {
                return sum + (item.quantity * item.price);
            }, 0);
        },
        
        calculateTotal() {
            return this.calculateSubtotal() + this.shipping - this.discount;
        },
        
        toggleShippingAddress() {
            const checkbox = document.getElementById('same_as_billing');
            const shippingFields = document.getElementById('shipping-address-fields');
            
            if (checkbox.checked) {
                shippingFields.style.display = 'none';
            } else {
                shippingFields.style.display = 'block';
            }
        }
    }
}

// Livewire event listeners
window.addEventListener('customerDataLoaded', event => {
    const data = event.detail;
    
    // Get the Alpine component
    const alpineComponent = Alpine.$data(document.querySelector('[x-data]'));
    
    if (alpineComponent && data.phone) {
        // Update customer phone if it's empty or different
        if (!alpineComponent.customer.phone) {
            alpineComponent.customer.phone = data.phone;
        }
        
        // Sync originalCustomer with actual database values to prevent false change detection
        alpineComponent.originalCustomer.phone = data.phone;
        
        // Reset change flag since we're syncing with actual database values
        alpineComponent.customerDataChanged = false;
    }
});

window.addEventListener('addressSelected', event => {
    console.log('Address selected event:', event.detail);
    
    // Get address data from event detail
    const { name, phone, email, address } = event.detail;
    
    // Populate shipping form fields with selected address
    const nameInput = document.getElementById('shipping_name');
    const phoneInput = document.getElementById('shipping_phone');
    const emailInput = document.getElementById('shipping_email');
    const addressInput = document.getElementById('shipping_address_line_1');
    
    if (nameInput) nameInput.value = name || '';
    if (phoneInput) phoneInput.value = phone || '';
    if (emailInput) emailInput.value = email || '';
    if (addressInput) addressInput.value = address || '';
    
    // Also update hidden fields for first/last name
    const nameParts = (name || '').split(' ', 2);
    const firstNameInput = document.getElementById('shipping_first_name');
    const lastNameInput = document.getElementById('shipping_last_name');
    if (firstNameInput) firstNameInput.value = nameParts[0] || '';
    if (lastNameInput) lastNameInput.value = nameParts[1] || '';
    
    // Show success notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-20 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg z-50';
    notification.innerHTML = '✓ Address selected successfully';
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 2000);
});
</script>
@endpush
@endsection
