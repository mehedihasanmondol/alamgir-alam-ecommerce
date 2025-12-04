{{-- 
/**
 * Dynamic Mega Menu Component
 * Purpose: Display categories and subcategories in a mega menu dropdown
 * 
 * @param Collection $megaMenuCategories - Parent categories with children
 */
--}}

@props(['megaMenuCategories' => collect(), 'categoryTrendingBrands' => [], 'globalTrendingBrands' => collect()])

<!-- Navigation Menu with Mega Dropdown -->
<nav class="relative flex-1" x-data="{ activeMenu: null, closeTimeout: null }">
    <ul class="flex items-center space-x-1 py-3 overflow-x-auto scrollbar-hide">
        @forelse($megaMenuCategories as $category)
            <!-- Category Menu Item -->
            <li class="relative static" 
                @mouseenter="clearTimeout(closeTimeout); activeMenu = '{{ $category->slug }}'" 
                @mouseleave="closeTimeout = setTimeout(() => { activeMenu = null }, 200)">
                <a href="{{ $category->getUrl() }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-md transition whitespace-nowrap">
                    {{ $category->name }}
                </a>
            </li>
            
            <!-- Mega Menu Dropdown -->
            @if($category->activeChildren->isNotEmpty())
            <div x-show="activeMenu === '{{ $category->slug }}'" 
                 @mouseenter="clearTimeout(closeTimeout); activeMenu = '{{ $category->slug }}'" 
                 @mouseleave="closeTimeout = setTimeout(() => { activeMenu = null }, 200)"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1"
                 class="absolute left-0 right-0 top-full -mt-1 w-full bg-white shadow-2xl rounded-lg border border-gray-200 z-[100]"
                 style="display: none;">
                <div class="container mx-auto px-4">
                    <div class="p-8">
                        <div class="flex gap-8 max-w-7xl">
                            <!-- Categories Section (Left) -->
                            <div class="flex-1 grid grid-cols-4 gap-0">
                            @foreach($category->activeChildren as $subcategory)
                                <div class="pr-6">
                                    <h3 class="text-sm font-bold text-blue-600 mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                        <a href="{{ $subcategory->getUrl() }}" class="hover:text-green-600 transition">
                                            {{ $subcategory->name }}
                                        </a>
                                    </h3>
                                    
                                    @if($subcategory->activeChildren->isNotEmpty())
                                        <ul class="space-y-2">
                                            @foreach($subcategory->activeChildren->take(8) as $subSubcategory)
                                                <li>
                                                    <a href="{{ $subSubcategory->getUrl() }}" 
                                                       class="text-sm text-gray-700 hover:text-green-600 transition block">
                                                        {{ $subSubcategory->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                            
                                            @if($subcategory->activeChildren->count() > 8)
                                                <li>
                                                    <a href="{{ $subcategory->getUrl() }}" 
                                                       class="text-sm text-green-600 hover:text-green-700 font-medium transition block">
                                                        View All →
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    @else
                                        <p class="text-sm text-gray-500 italic">No subcategories</p>
                                    @endif
                                </div>
                            @endforeach
                            </div>
                            
                            <!-- Trending Brands Section (Right) - Category Specific -->
                            @php
                                $categoryBrands = $categoryTrendingBrands[$category->id] ?? collect();
                            @endphp
                            @if($categoryBrands->isNotEmpty())
                            <div class="w-48 border-l border-gray-200 pl-8 flex-shrink-0">
                                <h3 class="text-sm font-bold text-gray-900 mb-4">Trending brands</h3>
                                <div class="space-y-3">
                                    @foreach($categoryBrands->take(6) as $brand)
                                        <a href="{{ route('brands.show', $brand->slug) }}" 
                                           class="block group">
                                            @if($brand->media || $brand->logo)
                                                <div class="w-full h-16 flex items-center justify-center border border-gray-200 group-hover:border-green-500 transition-all p-2">
                                                    <img src="{{ $brand->getThumbnailUrl() }}" 
                                                         alt="{{ $brand->name }}" 
                                                         class="max-w-full max-h-full object-contain">
                                                </div>
                                            @else
                                                <div class="w-full h-16 flex items-center justify-center border border-gray-200 group-hover:border-green-500 transition-all p-2">
                                                    <span class="text-xs font-semibold text-gray-600">{{ $brand->name }}</span>
                                                </div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @empty
            <!-- Fallback if no categories -->
            <li class="px-4 py-2 text-sm text-gray-500">
                No categories available
            </li>
        @endforelse
        
        <!-- Brands A-Z Menu Item -->
        <li class="relative static" 
            @mouseenter="clearTimeout(closeTimeout); activeMenu = 'brands-az'" 
            @mouseleave="closeTimeout = setTimeout(() => { activeMenu = null }, 200)">
            <a href="{{ route('brands.index') }}" 
               class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-md transition whitespace-nowrap">
                Brands A-Z
            </a>
        </li>
        
        <!-- Brands A-Z Mega Menu Dropdown -->
        <div x-show="activeMenu === 'brands-az'" 
             @mouseenter="clearTimeout(closeTimeout); activeMenu = 'brands-az'" 
             @mouseleave="closeTimeout = setTimeout(() => { activeMenu = null }, 200)"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-1"
             class="absolute left-0 right-0 top-full -mt-1 w-full bg-white shadow-2xl rounded-lg border border-gray-200 z-[100]"
             style="display: none;">
            <div class="container mx-auto px-4">
                <div class="p-8">
                    <div class="flex gap-8 max-w-7xl">
                        <!-- Brands Grid (Left) -->
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Shop by Brand</h3>
                            <div class="grid grid-cols-6 gap-0">
                                @php
                                    $allBrands = \App\Modules\Ecommerce\Brand\Models\Brand::where('is_active', true)
                                        ->orderBy('name')
                                        ->get()
                                        ->groupBy(function($brand) {
                                            return strtoupper(substr($brand->name, 0, 1));
                                        });
                                @endphp
                                
                                @foreach(range('A', 'Z') as $letter)
                                    <div class="pr-4">
                                        <h4 class="text-sm font-bold text-blue-600 mb-2">{{ $letter }}</h4>
                                        @if(isset($allBrands[$letter]))
                                            <ul class="space-y-1">
                                                @foreach($allBrands[$letter]->take(8) as $brand)
                                                    <li>
                                                        <a href="{{ route('brands.show', $brand->slug) }}" 
                                                           class="text-sm text-gray-700 hover:text-green-600 transition block">
                                                            {{ $brand->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                                @if($allBrands[$letter]->count() > 8)
                                                    <li>
                                                        <a href="{{ route('brands.index', ['letter' => $letter]) }}" 
                                                           class="text-sm text-green-600 hover:text-green-700 font-medium transition block">
                                                            View All →
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        @else
                                            <p class="text-xs text-gray-400">-</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Trending Brands Section (Right) - Global for Brands A-Z -->
                        @if($globalTrendingBrands->isNotEmpty())
                        <div class="w-48 border-l border-gray-200 pl-8 flex-shrink-0">
                            <h3 class="text-sm font-bold text-gray-900 mb-4">Trending brands</h3>
                            <div class="space-y-3">
                                @foreach($globalTrendingBrands->take(6) as $brand)
                                    <a href="{{ route('brands.show', $brand->slug) }}" 
                                       class="block group">
                                        @if($brand->media || $brand->logo)
                                            <div class="w-full h-16 flex items-center justify-center border border-gray-200 group-hover:border-green-500 transition-all p-2">
                                                <img src="{{ $brand->getThumbnailUrl() }}" 
                                                     alt="{{ $brand->name }}" 
                                                     class="max-w-full max-h-full object-contain">
                                            </div>
                                        @else
                                            <div class="w-full h-16 flex items-center justify-center border border-gray-200 group-hover:border-green-500 transition-all p-2">
                                                <span class="text-xs font-semibold text-gray-600">{{ $brand->name }}</span>
                                            </div>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </ul>
</nav>
