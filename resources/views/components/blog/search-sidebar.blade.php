@props([
    'query' => '',
    'categories' => collect()
])

<div class="space-y-8">
    <!-- Search Tips -->
    <div class="bg-blue-50 rounded-lg shadow-md border border-blue-100" x-data="{ 
        tipsOpen: window.innerWidth >= 1024,
        init() {
            this.tipsOpen = window.innerWidth >= 1024;
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    this.tipsOpen = true;
                }
            });
        }
    }">
        <!-- Header with Toggle Button -->
        <div class="p-6 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Search Tips</h3>
            
            <!-- Mobile Toggle Button -->
            <button 
                @click="tipsOpen = !tipsOpen"
                class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg hover:bg-blue-100 transition-colors"
                :class="{ 'bg-blue-100': !tipsOpen }"
                type="button"
                aria-label="Toggle search tips"
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
        
        <!-- Collapsible Content -->
        <div 
            class="px-6 pb-6 overflow-hidden transition-all duration-300 ease-in-out lg:block"
            x-show="tipsOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 max-h-0"
            x-transition:enter-end="opacity-100 max-h-screen"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 max-h-screen"
            x-transition:leave-end="opacity-0 max-h-0"
        >
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Use specific keywords</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Try different word variations</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Check spelling</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Categories -->
    <div class="bg-white rounded-lg shadow-md" x-data="{ 
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
        <div class="p-6 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Browse by Category</h3>
            
            <!-- Mobile Toggle Button -->
            <button 
                @click="categoriesOpen = !categoriesOpen"
                class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 transition-colors"
                :class="{ 'bg-gray-100': !categoriesOpen }"
                type="button"
                aria-label="Toggle categories"
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
        <div 
            class="px-6 pb-6 overflow-hidden transition-all duration-300 ease-in-out lg:block"
            x-show="categoriesOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 max-h-0"
            x-transition:enter-end="opacity-100 max-h-screen"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 max-h-screen"
            x-transition:leave-end="opacity-0 max-h-0"
        >
            <ul class="space-y-2">
                @foreach($categories as $category)
                <li>
                    <a href="{{ route('blog.category', $category->slug) }}" 
                       class="flex items-center justify-between text-gray-700 hover:text-blue-600">
                        <span>{{ $category->name }}</span>
                        @if($category->published_posts_count > 0)
                        <span class="text-sm text-gray-500">{{ $category->published_posts_count }}</span>
                        @endif
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Back to Blog -->
    <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-lg shadow-md border border-gray-200" x-data="{ 
        backOpen: window.innerWidth >= 1024,
        init() {
            this.backOpen = window.innerWidth >= 1024;
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    this.backOpen = true;
                }
            });
        }
    }">
        <!-- Header with Toggle Button -->
        <div class="p-6 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Browse All Posts</h3>
            
            <!-- Mobile Toggle Button -->
            <button 
                @click="backOpen = !backOpen"
                class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 transition-colors"
                :class="{ 'bg-gray-100': !backOpen }"
                type="button"
                aria-label="Toggle browse section"
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
        
        <!-- Collapsible Content -->
        <div 
            class="px-6 pb-6 overflow-hidden transition-all duration-300 ease-in-out lg:block"
            x-show="backOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 max-h-0"
            x-transition:enter-end="opacity-100 max-h-screen"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 max-h-screen"
            x-transition:leave-end="opacity-0 max-h-0"
        >
            <p class="text-sm text-gray-600 mb-4">Explore our complete collection of articles</p>
            <a href="{{ route('blog.index') }}" 
               class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                View All Posts
            </a>
        </div>
    </div>
</div>
