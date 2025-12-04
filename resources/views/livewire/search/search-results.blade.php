<div class="bg-gray-50 min-h-screen">
    <!-- Search Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Search Info -->
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        @if($query)
                            Search Results for "{{ $query }}"
                        @else
                            Search Results
                        @endif
                    </h1>
                    @if($query && $searchStats['total'] > 0)
                        <p class="text-sm text-gray-600 mt-1">
                            Found {{ number_format($searchStats['total']) }} results
                        </p>
                    @endif
                </div>

                <!-- Search Input -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input 
                            type="text" 
                            wire:model.live.debounce.500ms="query"
                            placeholder="Search again..." 
                            class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        >
                        <button 
                            wire:click="search"
                            class="absolute right-2 top-1/2 -translate-y-1/2 bg-orange-500 text-white p-2 rounded-md hover:bg-orange-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($query && strlen($query) >= 2)
        <div class="container mx-auto px-4 py-8">
            <!-- Search Type Tabs -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="flex border-b border-gray-200 overflow-x-auto">
                    <button 
                        wire:click="setSearchType('all')"
                        class="px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors {{ $searchType === 'all' ? 'text-orange-600 border-orange-500' : 'text-gray-600 border-transparent hover:text-gray-800' }}">
                        All Results ({{ $searchStats['total'] }})
                    </button>
                    
                    @if($searchStats['products'] > 0)
                    <button 
                        wire:click="setSearchType('products')"
                        class="px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors {{ $searchType === 'products' ? 'text-orange-600 border-orange-500' : 'text-gray-600 border-transparent hover:text-gray-800' }}">
                        Products ({{ $searchStats['products'] }})
                    </button>
                    @endif

                    @if($searchStats['categories'] > 0)
                    <button 
                        wire:click="setSearchType('categories')"
                        class="px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors {{ $searchType === 'categories' ? 'text-orange-600 border-orange-500' : 'text-gray-600 border-transparent hover:text-gray-800' }}">
                        Categories ({{ $searchStats['categories'] }})
                    </button>
                    @endif

                    @if($searchStats['brands'] > 0)
                    <button 
                        wire:click="setSearchType('brands')"
                        class="px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors {{ $searchType === 'brands' ? 'text-orange-600 border-orange-500' : 'text-gray-600 border-transparent hover:text-gray-800' }}">
                        Brands ({{ $searchStats['brands'] }})
                    </button>
                    @endif

                    @if($searchStats['blogs'] > 0)
                    <button 
                        wire:click="setSearchType('blogs')"
                        class="px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors {{ $searchType === 'blogs' ? 'text-orange-600 border-orange-500' : 'text-gray-600 border-transparent hover:text-gray-800' }}">
                        Blog Posts ({{ $searchStats['blogs'] }})
                    </button>
                    @endif
                </div>

                <!-- Sort Options -->
                @if($searchType === 'products')
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Sort by:</span>
                        <select wire:model.live="sortBy" class="px-3 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option value="relevance">Relevance</option>
                            <option value="name_asc">Name: A to Z</option>
                            <option value="name_desc">Name: Z to A</option>
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                            <option value="latest">Latest</option>
                            <option value="popular">Most Popular</option>
                        </select>
                    </div>
                </div>
                @endif
            </div>

            <!-- Search Results -->
            <div class="space-y-6">
                @if($searchStats['total'] > 0)
                    <!-- Products Results -->
                    @if(($searchType === 'all' || $searchType === 'products') && is_object($results) && $results->count() > 0)
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Products
                                @if($searchType === 'all')
                                    <span class="ml-2 text-sm text-gray-500">(Showing top results)</span>
                                @endif
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                @foreach($results as $product)
                                    <div class="group">
                                        <a href="{{ route('products.show', $product->slug) }}" class="block">
                                            <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 mb-3">
                                                @if($product->getPrimaryThumbnailUrl())
                                                    <img 
                                                        src="{{ $product->getPrimaryThumbnailUrl() }}" 
                                                        alt="{{ $product->name }}"
                                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                    >
                                                @else
                                                    <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <h3 class="font-medium text-gray-900 group-hover:text-orange-600 transition-colors line-clamp-2">
                                                {{ $product->name }}
                                            </h3>
                                            
                                            @if($product->brand)
                                                <p class="text-sm text-gray-600 mt-1">{{ $product->brand->name }}</p>
                                            @endif
                                            
                                            @if($product->defaultVariant)
                                                <div class="mt-2">
                                                    @if($product->defaultVariant->sale_price)
                                                        <span class="text-lg font-bold text-orange-600">{{ currency_format($product->defaultVariant->sale_price) }}</span>
                                                        <span class="text-sm text-gray-500 line-through ml-2">{{ currency_format($product->defaultVariant->price) }}</span>
                                                    @else
                                                        <span class="text-lg font-bold text-gray-900">{{ currency_format($product->defaultVariant->price) }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            @if(method_exists($results, 'links'))
                                <div class="mt-6">
                                    {{ $results->links() }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Categories & Brands Results -->
                    @if($searchType === 'all' && ($categorySuggestions->count() > 0 || $brandSuggestions->count() > 0))
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Categories -->
                            @if($categorySuggestions->count() > 0)
                                <div class="bg-white rounded-lg shadow-sm p-6">
                                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        Categories
                                    </h2>
                                    <div class="space-y-3">
                                        @foreach($categorySuggestions as $category)
                                            <a href="{{ route('categories.show', $category->slug) }}" 
                                               class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                                <div class="flex items-center">
                                                    @if($category->media)
                                                        <img src="{{ $category->getThumbnailUrl() }}" 
                                                             alt="{{ $category->name }}"
                                                             class="w-10 h-10 rounded-lg object-cover mr-3">
                                                    @endif
                                                    <div>
                                                        <h3 class="font-medium text-gray-900 group-hover:text-blue-600">{{ $category->name }}</h3>
                                                        @if($category->description)
                                                            <p class="text-sm text-gray-600 line-clamp-1">{{ $category->description }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="text-sm text-gray-500">{{ $category->products_count }} products</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Brands -->
                            @if($brandSuggestions->count() > 0)
                                <div class="bg-white rounded-lg shadow-sm p-6">
                                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        Brands
                                    </h2>
                                    <div class="space-y-3">
                                        @foreach($brandSuggestions as $brand)
                                            <a href="{{ route('brands.show', $brand->slug) }}" 
                                               class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                                <div class="flex items-center">
                                                    @if($brand->media || $brand->logo)
                                                        <img src="{{ $brand->getThumbnailUrl() }}" 
                                                             alt="{{ $brand->name }}"
                                                             class="w-10 h-10 rounded-lg object-contain bg-white border mr-3">
                                                    @endif
                                                    <div>
                                                        <h3 class="font-medium text-gray-900 group-hover:text-purple-600">{{ $brand->name }}</h3>
                                                        @if($brand->description)
                                                            <p class="text-sm text-gray-600 line-clamp-1">{{ $brand->description }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="text-sm text-gray-500">{{ $brand->products_count }} products</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Blog Results -->
                    @if(($searchType === 'all' || $searchType === 'blogs') && $blogSuggestions->count() > 0)
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                                Blog Posts
                                @if($searchType === 'all')
                                    <span class="ml-2 text-sm text-gray-500">(Showing top results)</span>
                                @endif
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($blogSuggestions as $blog)
                                    <article class="group">
                                        <a href="{{ route('products.show', $blog->slug) }}" class="block">
                                            @if($blog->featured_image)
                                                <div class="aspect-video rounded-lg overflow-hidden bg-gray-100 mb-3">
                                                    <img 
                                                        src="{{ asset('storage/' . $blog->featured_image) }}" 
                                                        alt="{{ $blog->title }}"
                                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                    >
                                                </div>
                                            @endif
                                            
                                            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-2 mb-2">
                                                {{ $blog->title }}
                                            </h3>
                                            
                                            @if($blog->excerpt)
                                                <p class="text-sm text-gray-600 line-clamp-3 mb-3">{{ $blog->excerpt }}</p>
                                            @endif
                                            
                                            <div class="flex items-center text-xs text-gray-500">
                                                <span>{{ $blog->created_at->format('M d, Y') }}</span>
                                                @if($blog->categories->count() > 0)
                                                    <span class="mx-2">•</span>
                                                    <span class="text-indigo-600">{{ $blog->categories->first()->name }}</span>
                                                @endif
                                            </div>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif

                @else
                    <!-- No Results -->
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No results found for "{{ $query }}"</h3>
                        <p class="text-gray-600 mb-6">Try different keywords or browse our categories</p>
                        
                        <div class="flex flex-wrap justify-center gap-3">
                            <a href="{{ route('shop') }}" class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                                Browse Products
                            </a>
                            <a href="{{ route('categories.index') }}" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                Browse Categories
                            </a>
                            <a href="{{ route('blog.index') }}" class="px-6 py-3 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors">
                                Browse Blog
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="container mx-auto px-4 py-16 text-center">
            <svg class="w-20 h-20 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Start your search</h2>
            <p class="text-gray-600 mb-8">Enter a search term to find products, categories, brands, and blog posts</p>
            
            <div class="max-w-md mx-auto">
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.500ms="query"
                        placeholder="Search anything..." 
                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    >
                    <button 
                        wire:click="search"
                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-orange-500 text-white p-2 rounded-md hover:bg-orange-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
