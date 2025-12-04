<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.coupons.index') }}" class="hover:text-blue-600">Coupons</a>
            <span>/</span>
            <span class="text-gray-900">Create</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Create New Coupon</h1>
    </div>

    <form wire:submit="save">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    
                    <div class="space-y-4">
                        <!-- Coupon Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Coupon Code <span class="text-red-500">*</span>
                            </label>
                            <div class="flex space-x-2">
                                <input type="text" 
                                       wire:model="code"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase"
                                       placeholder="SUMMER2024">
                                <button type="button" 
                                        wire:click="generateCode"
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                    Generate
                                </button>
                            </div>
                            @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Coupon Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   wire:model="name"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Summer Sale 2024">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea wire:model="description"
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Describe the coupon..."></textarea>
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Discount Configuration -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Discount Configuration</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Discount Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Discount Type <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="type"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (৳)</option>
                            </select>
                            @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Discount Value -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Discount Value <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       wire:model="value"
                                       step="0.01"
                                       min="0"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="{{ $type === 'percentage' ? '10' : '100' }}">
                                <span class="absolute right-3 top-2.5 text-gray-500">
                                    {{ $type === 'percentage' ? '%' : '৳' }}
                                </span>
                            </div>
                            @error('value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Min Purchase Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Minimum Purchase Amount (৳)
                            </label>
                            <input type="number" 
                                   wire:model="min_purchase_amount"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="0.00">
                            @error('min_purchase_amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Max Discount Amount -->
                        @if($type === 'percentage')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Maximum Discount Amount (৳)
                                </label>
                                <input type="number" 
                                       wire:model="max_discount_amount"
                                       step="0.01"
                                       min="0"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="No limit">
                                @error('max_discount_amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Usage Limits -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Usage Limits</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Total Usage Limit
                            </label>
                            <input type="number" 
                                   wire:model="usage_limit"
                                   min="1"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Unlimited">
                            @error('usage_limit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Usage Limit Per User
                            </label>
                            <input type="number" 
                                   wire:model="usage_limit_per_user"
                                   min="1"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Unlimited">
                            @error('usage_limit_per_user') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Validity Period -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Validity Period</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time</label>
                            <input type="datetime-local" 
                                   wire:model="starts_at"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('starts_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date & Time</label>
                            <input type="datetime-local" 
                                   wire:model="expires_at"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('expires_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Product/Category Restrictions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Product & Category Restrictions</h2>
                    
                    <div class="space-y-6">
                        <!-- Applicable Categories -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Applicable Categories
                            </label>
                            <p class="text-xs text-gray-500 mb-3">Leave empty to apply to all categories</p>
                            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2">
                                @forelse($categories as $category)
                                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                        <input type="checkbox" 
                                               wire:model="applicable_categories"
                                               value="{{ $category->id }}"
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-3 text-sm text-gray-700">{{ $category->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500 text-center py-2">No categories available</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Excluded Categories -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Excluded Categories
                            </label>
                            <p class="text-xs text-gray-500 mb-3">Select categories to exclude from this coupon</p>
                            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2">
                                @forelse($categories as $category)
                                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                        <input type="checkbox" 
                                               wire:model="excluded_categories"
                                               value="{{ $category->id }}"
                                               class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                        <span class="ml-3 text-sm text-gray-700">{{ $category->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500 text-center py-2">No categories available</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Applicable Products -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Applicable Products
                            </label>
                            <p class="text-xs text-gray-500 mb-3">Leave empty to apply to all products</p>
                            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2">
                                @forelse($products as $product)
                                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                        <input type="checkbox" 
                                               wire:model="applicable_products"
                                               value="{{ $product->id }}"
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-3 text-sm text-gray-700">{{ $product->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500 text-center py-2">No products available</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Excluded Products -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Excluded Products
                            </label>
                            <p class="text-xs text-gray-500 mb-3">Select products to exclude from this coupon</p>
                            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2">
                                @forelse($products as $product)
                                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                        <input type="checkbox" 
                                               wire:model="excluded_products"
                                               value="{{ $product->id }}"
                                               class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                        <span class="ml-3 text-sm text-gray-700">{{ $product->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500 text-center py-2">No products available</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Status & Options -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Status & Options</h2>
                    
                    <div class="space-y-4">
                        <!-- Active Status -->
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700">Active</label>
                            <button type="button"
                                    wire:click="$toggle('is_active')"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $is_active ? 'bg-blue-600' : 'bg-gray-200' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </div>

                        <!-- First Order Only -->
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700">First Order Only</label>
                            <button type="button"
                                    wire:click="$toggle('first_order_only')"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $first_order_only ? 'bg-blue-600' : 'bg-gray-200' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $first_order_only ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </div>

                        <!-- Free Shipping -->
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700">Free Shipping</label>
                            <button type="button"
                                    wire:click="$toggle('free_shipping')"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $free_shipping ? 'bg-blue-600' : 'bg-gray-200' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $free_shipping ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="space-y-3">
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Create Coupon
                        </button>
                        <a href="{{ route('admin.coupons.index') }}" 
                           class="block w-full px-4 py-2 bg-gray-100 text-gray-700 text-center rounded-lg hover:bg-gray-200 transition">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
