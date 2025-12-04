<div>
    <!-- Mobile Menu Backdrop -->
    <div 
        x-show="$wire.isOpen"
        @click="$wire.closeMenu()"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 lg:hidden"
        style="display: none; background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
    </div>

    <!-- Mobile Menu Sidebar -->
    <div 
        x-show="$wire.isOpen"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed top-0 left-0 bottom-0 w-80 bg-white shadow-xl z-50 overflow-hidden lg:hidden"
        style="display: none;">
        
        <!-- Main Level -->
        <div 
            x-show="$wire.currentLevel === 'main'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="h-full overflow-y-auto">
            
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-4 py-4 flex items-center justify-between">
                @php
                    $siteLogo = \App\Models\SiteSetting::get('site_logo');
                    $siteName = \App\Models\SiteSetting::get('site_name', 'iHerb');
                @endphp
                
                <div class="flex items-center">
                    @if($siteLogo)
                        <img src="{{ asset('storage/' . $siteLogo) }}" 
                             alt="{{ $siteName }}" 
                             class="h-8 w-auto">
                    @else
                        <div class="bg-green-600 text-white font-bold text-lg px-2.5 py-1 rounded">
                            {{ $siteName }}
                        </div>
                    @endif
                </div>
                
                <button wire:click="closeMenu" class="text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- User Section -->
            @auth
                <div class="bg-green-50 mx-4 my-4 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                 alt="{{ auth()->user()->name }}"
                                 class="w-12 h-12 rounded-full object-cover border-2 border-green-200">
                        @else
                            <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-lg">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-600">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-1">
                        <a href="{{ route('customer.orders.index') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white rounded transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            My Orders
                        </a>
                        
                        <a href="{{ route('customer.profile') }}" 
                           class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-white rounded transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile
                        </a>
                    </div>
                </div>
            @else
                <div class="px-4 my-4">
                    <a href="{{ route('login') }}" 
                       class="flex items-center justify-center px-4 py-3 text-white bg-green-600 hover:bg-green-700 rounded-lg transition font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Sign In
                    </a>
                </div>
            @endauth

            <!-- Categories Navigation -->
            <nav class="px-4 py-2 space-y-1">
                @forelse($categories as $category)
                    <button 
                        wire:click="selectCategory({{ $category->id }})"
                        class="w-full flex items-center justify-between px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 rounded-lg transition">
                        <span class="font-medium">{{ $category->name }}</span>
                        @if($category->children()->where('is_active', true)->count() > 0)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        @endif
                    </button>
                @empty
                    <p class="px-4 py-3 text-gray-500">No categories available</p>
                @endforelse

                <!-- Secondary Menu Links -->
                @php
                    $secondaryMenuItems = \App\Models\SecondaryMenuItem::active()->ordered()->get();
                @endphp
                
                <div class="border-t border-gray-200 my-2 pt-2">
                 
                    @if($secondaryMenuItems->isNotEmpty())
                        @foreach($secondaryMenuItems as $item)
                            @if($item->type === 'link')
                                <a href="{{ $item->url }}" 
                                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 rounded-lg transition font-medium"
                                   @if($item->open_new_tab) target="_blank" rel="noopener noreferrer" @endif>
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                    {{ $item->label }}
                                </a>
                            @elseif($item->type === 'dropdown')
                                <!-- Dropdown items as individual links for mobile -->
                                <div class="ml-4 border-l-2 border-gray-100 pl-4 space-y-1">
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        {{ $item->label }}
                                    </div>
                                    <a href="/about" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-600 rounded transition">
                                        About Us
                                    </a>
                                    <a href="/contact" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-600 rounded transition">
                                        Contact
                                    </a>
                                    <a href="/faq" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-600 rounded transition">
                                        FAQ
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                @auth
                    <div class="border-t border-gray-200 my-2 pt-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition font-medium">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                @endauth
            </nav>
        </div>

        <!-- Subcategory Level -->
        <div 
            x-show="$wire.currentLevel === 'subcategory'"
            x-transition:enter="transition ease-out duration-200 transform"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="h-full overflow-y-auto absolute inset-0 bg-white">
            
            <!-- Subcategory Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-4 py-4">
                <div class="flex items-center justify-between mb-3">
                    <button wire:click="goBack" class="flex items-center text-gray-600 hover:text-gray-800 transition">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="text-sm font-medium">Back</span>
                    </button>
                    
                    <button wire:click="closeMenu" class="text-gray-500 hover:text-gray-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                @if($selectedCategory)
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">{{ $selectedCategory->name }}</h3>
                        <a href="{{ route('categories.show', $selectedCategory->slug) }}" 
                           class="text-sm text-green-600 hover:text-green-700 font-medium">
                            Shop all
                        </a>
                    </div>
                @endif
            </div>

            <!-- Subcategories List -->
            <nav class="px-4 py-4 space-y-1">
                @forelse($subcategories as $subcategory)
                    <a href="{{ route('categories.show', $subcategory->slug) }}" 
                       class="block px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 rounded-lg transition font-medium">
                        {{ $subcategory->name }}
                    </a>
                @empty
                    <p class="px-4 py-3 text-gray-500">No subcategories available</p>
                @endforelse
            </nav>
        </div>
    </div>
</div>
