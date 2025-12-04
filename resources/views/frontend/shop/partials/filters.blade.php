<div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
    <!-- Mobile Filter Toggle -->
    <button @click="showFilters = !showFilters" 
            class="lg:hidden w-full mb-4 px-4 py-2 bg-green-600 text-white rounded-lg flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
        </svg>
        Filters
    </button>

    <div x-show="showFilters" 
         x-transition
         class="space-y-6"
         :class="{'hidden lg:block': !showFilters}">
        
        <!-- Search -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">Search</label>
            <input type="text" 
                   x-model="filters.search"
                   @input.debounce.500ms="applyFilters()"
                   placeholder="Search products..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
        </div>

        <!-- Categories -->
        <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Categories</h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($categories as $category)
                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                    <input type="checkbox" 
                           value="{{ $category->id }}"
                           x-model="filters.category"
                           @change="applyFilters()"
                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                    <span class="ml-auto text-xs text-gray-500">({{ $category->products_count }})</span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Brands -->
        <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Brands</h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($brands as $brand)
                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                    <input type="checkbox" 
                           value="{{ $brand->id }}"
                           x-model="filters.brand"
                           @change="applyFilters()"
                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">{{ $brand->name }}</span>
                    <span class="ml-auto text-xs text-gray-500">({{ $brand->products_count }})</span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Price Range -->
        <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Price Range</h3>
            <div class="space-y-3">
                <div class="flex items-center space-x-2">
                    <input type="number" 
                           x-model="filters.min_price"
                           @change="applyFilters()"
                           placeholder="Min"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <span class="text-gray-500">-</span>
                    <input type="number" 
                           x-model="filters.max_price"
                           @change="applyFilters()"
                           placeholder="Max"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                @if($priceRange)
                <p class="text-xs text-gray-500">
                    Range: {{ currency_format($priceRange->min_price) }} - {{ currency_format($priceRange->max_price) }}
                </p>
                @endif
            </div>
        </div>

        <!-- Rating -->
        <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Rating</h3>
            <div class="space-y-2">
                @for($i = 5; $i >= 1; $i--)
                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                    <input type="radio" 
                           name="rating"
                           value="{{ $i }}"
                           x-model="filters.rating"
                           @change="applyFilters()"
                           class="border-gray-300 text-green-600 focus:ring-green-500">
                    <span class="ml-2 flex items-center">
                        @for($j = 1; $j <= 5; $j++)
                        <svg class="w-4 h-4 {{ $j <= $i ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        @endfor
                        <span class="ml-1 text-sm text-gray-600">& Up</span>
                    </span>
                </label>
                @endfor
            </div>
        </div>

        <!-- Availability -->
        <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Availability</h3>
            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                <input type="checkbox" 
                       value="1"
                       x-model="filters.in_stock"
                       @change="applyFilters()"
                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                <span class="ml-2 text-sm text-gray-700">In Stock Only</span>
            </label>
            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                <input type="checkbox" 
                       value="1"
                       x-model="filters.on_sale"
                       @change="applyFilters()"
                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                <span class="ml-2 text-sm text-gray-700">On Sale</span>
            </label>
        </div>

        <!-- Clear Filters -->
        <button @click="clearFilters()" 
                class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
            Clear All Filters
        </button>
    </div>
</div>
