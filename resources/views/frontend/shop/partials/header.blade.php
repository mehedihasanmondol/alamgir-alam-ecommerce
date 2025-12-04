<div class="bg-white rounded-lg shadow-sm p-4 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <!-- Results Count -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">All Products</h1>
            <p class="text-sm text-gray-600 mt-1">
                Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results
            </p>
        </div>

        <!-- Sort & View Options -->
        <div class="flex items-center space-x-4">
            <!-- Sort By -->
            <select x-model="filters.sort" 
                    @change="applyFilters()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option value="latest">Latest</option>
                <option value="popular">Most Popular</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
                <option value="name_asc">Name: A to Z</option>
                <option value="name_desc">Name: Z to A</option>
                <option value="rating">Highest Rated</option>
            </select>

            <!-- Per Page -->
            <select x-model="filters.per_page" 
                    @change="applyFilters()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option value="12">12 per page</option>
                <option value="24">24 per page</option>
                <option value="48">48 per page</option>
                <option value="96">96 per page</option>
            </select>

            <!-- View Toggle -->
            <div class="hidden md:flex border border-gray-300 rounded-lg overflow-hidden">
                <button @click="viewMode = 'grid'" 
                        :class="viewMode === 'grid' ? 'bg-green-600 text-white' : 'bg-white text-gray-600'"
                        class="px-3 py-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
                <button @click="viewMode = 'list'" 
                        :class="viewMode === 'list' ? 'bg-green-600 text-white' : 'bg-white text-gray-600'"
                        class="px-3 py-2 border-l border-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Active Filters -->
    <div x-show="hasActiveFilters()" class="mt-4 flex flex-wrap gap-2">
        <span class="text-sm font-medium text-gray-700">Active Filters:</span>
        
        <template x-if="filters.search">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                Search: <span x-text="filters.search" class="ml-1 font-medium"></span>
                <button @click="filters.search = ''; applyFilters()" class="ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
        </template>

        <template x-if="filters.category.length > 0">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                <span x-text="filters.category.length + ' Categories'"></span>
                <button @click="filters.category = []; applyFilters()" class="ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
        </template>

        <template x-if="filters.brand.length > 0">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                <span x-text="filters.brand.length + ' Brands'"></span>
                <button @click="filters.brand = []; applyFilters()" class="ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
        </template>
    </div>
</div>
