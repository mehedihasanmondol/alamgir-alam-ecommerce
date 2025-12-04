{{-- 
/**
 * ModuleName: Frontend Header Component
 * Purpose: Main navigation header for public-facing pages with dynamic mega menu
 * 
 * Features:
 * - Top announcement bar with promotional message
 * - Main header with logo, search, and user actions
 * - Dynamic navigation menu with categories from database
 * - Responsive mobile menu
 * 
 * @category Frontend
 * @package  Components
 * @created  2025-11-06
 * @updated  2025-11-06
 */
--}}

<!-- Top Announcement Bar -->
<div class="bg-gradient-to-r from-green-600 to-green-700 text-white text-sm" x-data="topbarSlider()">
    <div class="container mx-auto px-4">
        <!-- Desktop View -->
        <div class="hidden md:flex items-center justify-between py-2">
            <div class="flex items-center space-x-4">
                @php
                    $topHeaderEnabled = \App\Models\HomepageSetting::get('top_header_enabled', true);
                    $link1Text = \App\Models\HomepageSetting::get('top_header_link1_text', 'Special Offers & Coupons');
                    $link1Url = \App\Models\HomepageSetting::get('top_header_link1_url', '/coupons');
                    $link1Icon = \App\Models\HomepageSetting::get('top_header_link1_icon', 'tag');
                    $link2Text = \App\Models\HomepageSetting::get('top_header_link2_text', 'Shop Now');
                    $link2Url = \App\Models\HomepageSetting::get('top_header_link2_url', '/shop');
                    $link2Icon = \App\Models\HomepageSetting::get('top_header_link2_icon', 'clock');
                    
                    $getIconPath = function($iconName) {
                        return match($iconName) {
                            'tag' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
                            'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                            'gift' => 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7',
                            'shopping-bag' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z',
                            'star' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                            default => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'
                        };
                    };
                @endphp
                
                @if($topHeaderEnabled && $link1Text && $link1Url)
                <a href="{{ $link1Url }}" class="flex items-center hover:text-green-100 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $getIconPath($link1Icon) }}"></path>
                    </svg>
                    <span class="font-medium">{{ $link1Text }}</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @endif
                
                @if($topHeaderEnabled && $link2Text && $link2Url)
                <a href="{{ $link2Url }}" class="flex items-center hover:text-green-100 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $getIconPath($link2Icon) }}"></path>
                    </svg>
                    <span class="font-medium">{{ $link2Text }}</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @endif
            </div>
            
            <div class="flex items-center space-x-4">
                @php
                    $sitePhone = \App\Models\SiteSetting::get('site_phone');
                    $siteEmail = \App\Models\SiteSetting::get('site_email');
                @endphp
                
                @if($sitePhone)
                    <a href="tel:{{ $sitePhone }}" class="flex items-center space-x-2 hover:text-green-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>{{ $sitePhone }}</span>
                    </a>
                @endif
                
                @if($sitePhone && $siteEmail)
                    <span>|</span>
                @endif
                
                @if($siteEmail)
                    <a href="mailto:{{ $siteEmail }}" class="flex items-center space-x-2 hover:text-green-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>{{ $siteEmail }}</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Mobile View - Slider -->
        <div class="md:hidden relative py-2">
            <!-- Previous Button -->
            <button 
                @click="prev()" 
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-green-800/50 hover:bg-green-800 p-1.5 rounded-r transition-colors"
                aria-label="Previous">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <!-- Slider Container -->
            <div class="overflow-hidden mx-8">
                <div 
                    class="flex transition-transform duration-300 ease-in-out"
                    :style="`transform: translateX(-${currentSlide * 100}%)`">
                    
                    @if($topHeaderEnabled && $link1Text && $link1Url)
                    <!-- Slide 1: First Link -->
                    <div class="w-full flex-shrink-0 flex justify-center">
                        <a href="{{ $link1Url }}" class="flex items-center hover:text-green-100 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $getIconPath($link1Icon) }}"></path>
                            </svg>
                            <span class="font-medium text-xs sm:text-sm">{{ $link1Text }}</span>
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    @endif

                    @if($topHeaderEnabled && $link2Text && $link2Url)
                    <!-- Slide 2: Second Link -->
                    <div class="w-full flex-shrink-0 flex justify-center">
                        <a href="{{ $link2Url }}" class="flex items-center hover:text-green-100 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $getIconPath($link2Icon) }}"></path>
                            </svg>
                            <span class="font-medium text-xs sm:text-sm">{{ $link2Text }}</span>
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Next Button -->
            <button 
                @click="next()" 
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-green-800/50 hover:bg-green-800 p-1.5 rounded-l transition-colors"
                aria-label="Next">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

        </div>
    </div>
</div>

<script>
function topbarSlider() {
    return {
        currentSlide: 0,
        totalSlides: 2,
        autoplayInterval: null,
        
        init() {
            this.startAutoplay();
        },
        
        next() {
            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
            this.resetAutoplay();
        },
        
        prev() {
            this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
            this.resetAutoplay();
        },
        
        startAutoplay() {
            this.autoplayInterval = setInterval(() => {
                this.next();
            }, 4000); // Change slide every 4 seconds
        },
        
        resetAutoplay() {
            clearInterval(this.autoplayInterval);
            this.startAutoplay();
        }
    }
}
</script>

<!-- Main Header -->
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto px-4">
        @php
            $siteLogo = \App\Models\SiteSetting::get('site_logo');
            $siteName = \App\Models\SiteSetting::get('site_name', 'iHerb');
        @endphp
        
        <!-- Mobile Header Layout - iHerb Clone -->
        <div class="lg:hidden flex items-center py-2.5 px-1">
            <!-- Hamburger Menu (Left) -->
            <button 
                onclick="Livewire.dispatch('openMenu')"
                class="flex items-center justify-center w-10 h-10 text-gray-700 hover:text-green-600 transition flex-shrink-0 mr-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center flex-shrink-0 mr-3">
                @if($siteLogo)
                    <img src="{{ asset('storage/' . $siteLogo) }}" 
                         alt="{{ $siteName }}" 
                         class="h-8 w-auto">
                @else
                    <div class="bg-green-600 text-white font-bold text-lg px-2.5 py-1 rounded">
                        iHerb
                    </div>
                @endif
            </a>

            <!-- Search Field (Flexible width) -->
            <div class="flex-1 min-w-0 mr-3">
                @livewire('search.global-search')
            </div>

            <!-- Cart (Right) -->
            <div class="flex items-center flex-shrink-0">
                @livewire('cart.cart-counter')
            </div>
        </div>

        <!-- Desktop Header Layout -->
        <div class="hidden lg:flex items-center justify-between py-4">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center">
                    @if($siteLogo)
                        <img src="{{ asset('storage/' . $siteLogo) }}" 
                             alt="{{ $siteName }}" 
                             class="h-12 w-auto">
                    @else
                        <div class="bg-green-600 text-white font-bold text-2xl px-3 py-2 rounded">
                            {{ $siteName }}
                        </div>
                    @endif
                </a>
            </div>

            <!-- Search Bar (Desktop) -->
            <div class="flex-1 max-w-3xl mx-8">
                @livewire('search.global-search')
            </div>

            <!-- User Actions -->
            <div class="flex items-center space-x-6">
                @auth
                    <!-- User Dropdown -->
                    <div x-data="{ userMenuOpen: false }" class="relative">
                        <button 
                            @click="userMenuOpen = !userMenuOpen"
                            @click.away="userMenuOpen = false"
                            class="flex items-center text-gray-700 hover:text-green-600 transition">
                            @if(auth()->user()->media)
                                <img src="{{ auth()->user()->media->small_url }}" 
                                     alt="{{ auth()->user()->name }}"
                                     class="w-8 h-8 rounded-full object-cover border-2 border-gray-200">
                            @elseif(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                     alt="{{ auth()->user()->name }}"
                                     class="w-8 h-8 rounded-full object-cover border-2 border-gray-200">
                            @else
                                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="ml-2 text-sm font-medium hidden lg:block">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 ml-1 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div 
                            x-show="userMenuOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                            style="display: none;">
                            
                            <a href="{{ route('customer.orders.index') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    My Orders
                                </div>
                            </a>

                            <a href="{{ route('customer.profile') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile
                                </div>
                            </a>

                            @if(auth()->user()->role !== 'customer')
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Admin Panel
                                    </div>
                                </a>
                            @endif

                            <div class="border-t border-gray-200 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Logout
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="flex items-center text-gray-700 hover:text-green-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="ml-2 text-sm font-medium hidden lg:block">Sign In</span>
                    </a>
                @endauth

                <!-- Wishlist -->
                @livewire('wishlist.wishlist-counter')

                <!-- Cart -->
                @livewire('cart.cart-counter')
            </div>
        </div>

        <!-- Navigation Container -->
        <div class="border-t border-gray-200 hidden lg:flex items-center justify-between">
            <!-- Primary Mega Menu (Left) -->
            <x-frontend.mega-menu 
                :megaMenuCategories="$megaMenuCategories ?? collect()" 
                :categoryTrendingBrands="$categoryTrendingBrands ?? []" 
                :globalTrendingBrands="$globalTrendingBrands ?? collect()" 
            />
            
            <!-- Secondary Menu (Right) -->
            <x-frontend.secondary-menu />
        </div>
    </div>
</header>

<!-- Mobile Menu Component -->
@livewire('mobile-menu')
