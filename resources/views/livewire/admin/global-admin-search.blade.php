<div class="relative" x-data="{ open: @entangle('showResults') }" @click.away="open = false">
    <!-- Search Input -->
    <div class="relative">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="query"
            placeholder="Search admin panel..."
            class="w-full pl-10 pr-10 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
            @focus="if($wire.query.length >= 2) open = true"
        >
        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        
        @if($query)
            <button 
                wire:click="clearSearch" 
                class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
    </div>

    <!-- Search Results Dropdown -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="absolute z-50 w-full md:w-96 mt-2 bg-white rounded-lg shadow-2xl border border-gray-200 max-h-96 overflow-y-auto"
        style="display: none;"
    >
        @if(count($results) > 0)
            <div class="p-2">
                <!-- Results Header -->
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                    {{ count($results) }} {{ count($results) == 1 ? 'Result' : 'Results' }} Found
                </div>

                <!-- Results List -->
                <div class="mt-1">
                    @foreach($results as $result)
                        <a 
                            href="{{ route($result['route']) }}"
                            wire:click="clearSearch"
                            class="flex items-start px-3 py-3 hover:bg-blue-50 rounded-lg transition-colors group cursor-pointer"
                        >
                            <!-- Icon -->
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                <i class="{{ $result['icon'] }} text-blue-600 text-sm"></i>
                            </div>

                            <!-- Content -->
                            <div class="ml-3 flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 truncate group-hover:text-blue-600">
                                        {{ $result['title'] }}
                                    </p>
                                    <span class="ml-2 px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full">
                                        {{ $result['category'] }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                    {{ $result['description'] }}
                                </p>
                            </div>

                            <!-- Chevron -->
                            <svg class="flex-shrink-0 ml-2 w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @endforeach
                </div>

                <!-- Quick Tips -->
                <div class="px-3 py-2 mt-2 text-xs text-gray-500 bg-gray-50 rounded-lg border-t border-gray-100">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Tip: Type to search navigations, features, and settings</span>
                    </div>
                </div>
            </div>
        @elseif($query && count($results) == 0)
            <!-- No Results -->
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No results found</h3>
                <p class="mt-1 text-xs text-gray-500">
                    Try different keywords or check your permissions
                </p>
            </div>
        @else
            <!-- Initial State -->
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Quick Search</h3>
                <p class="mt-1 text-xs text-gray-500">
                    Type to search admin navigations, features, and settings
                </p>
                
                <!-- Popular Searches -->
                <div class="mt-4">
                    <p class="text-xs font-medium text-gray-700 mb-2">Popular:</p>
                    <div class="flex flex-wrap gap-2 justify-center">
                        <button 
                            wire:click="$set('query', 'products')"
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors"
                        >
                            Products
                        </button>
                        <button 
                            wire:click="$set('query', 'orders')"
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors"
                        >
                            Orders
                        </button>
                        <button 
                            wire:click="$set('query', 'settings')"
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors"
                        >
                            Settings
                        </button>
                        <button 
                            wire:click="$set('query', 'stock')"
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors"
                        >
                            Stock
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
