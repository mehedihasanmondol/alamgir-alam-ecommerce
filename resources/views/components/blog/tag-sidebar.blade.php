@props([
    'tag',
    'categories' => collect()
])

<aside class="lg:col-span-3">
    <div class="lg:sticky lg:top-8 space-y-6">
        <!-- Tag Info Card -->
        <div class="bg-white rounded-lg shadow-sm" x-data="{ 
            sidebarOpen: window.innerWidth >= 1024,
            init() {
                this.sidebarOpen = window.innerWidth >= 1024;
                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 1024) {
                        this.sidebarOpen = true;
                    }
                });
            }
        }">
            <!-- Header with Toggle Button -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3 flex-1">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $tag->name }}</h2>
                        @if($tag->description)
                            <p class="text-sm text-gray-600 mt-1">{{ $tag->description }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Mobile Toggle Button -->
                <button 
                    @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 transition-colors"
                    :class="{ 'bg-gray-100': !sidebarOpen }"
                    type="button"
                    aria-label="Toggle sidebar menu"
                >
                    <svg 
                        class="w-5 h-5 text-gray-600 transition-transform duration-200"
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
            
            <!-- Collapsible Navigation -->
            <nav 
                class="py-2 overflow-hidden transition-all duration-300 ease-in-out lg:block"
                x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 max-h-0"
                x-transition:enter-end="opacity-100 max-h-screen"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 max-h-screen"
                x-transition:leave-end="opacity-0 max-h-0"
            >
                <!-- Home Link -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-50 transition-colors group">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="font-medium">Home</span>
                </a>

                <!-- All Posts Link -->
                <a href="{{ route('blog.index') }}" class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-50 transition-colors group">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span class="font-medium">All Posts</span>
                </a>

                <!-- Current Tag -->
                <div class="flex items-center gap-3 px-6 py-3 bg-purple-50 text-purple-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span class="font-medium">{{ $tag->name }}</span>
                </div>
            </nav>
        </div>

        <!-- Categories Card -->
        <div class="bg-white rounded-lg shadow-sm" x-data="{ 
            categoriesOpen: window.innerWidth >= 1024,
            init() {
                this.categoriesOpen = window.innerWidth >= 1024;
                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 1024) {
                        this.categoriesOpen = true;
                    }
                });
            }
        }">
            <!-- Header with Toggle Button -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Browse by Category</h3>
                
                <!-- Mobile Toggle Button -->
                <button 
                    @click="categoriesOpen = !categoriesOpen"
                    class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 transition-colors"
                    :class="{ 'bg-gray-100': !categoriesOpen }"
                    type="button"
                    aria-label="Toggle categories menu"
                >
                    <svg 
                        class="w-5 h-5 text-gray-600 transition-transform duration-200"
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
            
            <!-- Collapsible Categories -->
            <nav 
                class="py-2 overflow-hidden transition-all duration-300 ease-in-out lg:block"
                x-show="categoriesOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 max-h-0"
                x-transition:enter-end="opacity-100 max-h-screen"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 max-h-screen"
                x-transition:leave-end="opacity-0 max-h-0"
            >
                @foreach($categories as $category)
                    <a href="{{ route('blog.category', $category->slug) }}" 
                       class="flex items-center justify-between gap-3 px-6 py-3 text-gray-700 hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <!-- Category Image or Fallback Icon -->
                            @if($category->getImageUrl())
                                <div class="w-8 h-8 flex-shrink-0 rounded-md overflow-hidden bg-gray-100">
                                    <img 
                                        src="{{ $category->getThumbnailUrl() ?? $category->getImageUrl() }}" 
                                        alt="{{ $category->name }}"
                                        class="w-full h-full object-cover"
                                    >
                                </div>
                            @else
                                <div class="w-8 h-8 flex-shrink-0 rounded-md bg-gradient-to-br from-green-100 to-blue-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <span class="font-medium truncate">{{ $category->name }}</span>
                        </div>
                        
                        <!-- Post Count Badge -->
                        @if(isset($category->posts_count) && $category->posts_count > 0)
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full flex-shrink-0">
                                {{ $category->posts_count }}
                            </span>
                        @elseif(isset($category->published_posts_count) && $category->published_posts_count > 0)
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full flex-shrink-0">
                                {{ $category->published_posts_count }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>
    </div>
</aside>
