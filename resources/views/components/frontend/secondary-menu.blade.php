{{-- 
/**
 * Secondary Menu Component
 * Purpose: Display secondary navigation menu (Sale Offers, Best Sellers, etc.)
 * Manageable from admin settings
 * 
 * @category Frontend
 * @package  Components
 * @created  2025-11-06
 */
--}}

@php
    $secondaryMenuItems = \App\Models\SecondaryMenuItem::active()->ordered()->get();
@endphp

@if($secondaryMenuItems->isNotEmpty())
<div class="flex items-center space-x-6 py-3">
      
        @foreach($secondaryMenuItems as $item)
            @if($item->type === 'link')
                <a href="{{ $item->url }}" 
                   class="{{ $item->color }} hover:text-green-600 text-sm font-medium transition"
                   @if($item->open_new_tab) target="_blank" rel="noopener noreferrer" @endif>
                    {{ $item->label }}
                </a>
            @elseif($item->type === 'dropdown')
                <!-- More Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            @click.away="open = false"
                            class="{{ $item->color }} hover:text-green-600 text-sm font-medium transition flex items-center">
                        {{ $item->label }}
                        <svg class="w-4 h-4 ml-1" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50"
                         style="display: none;">
                        <div class="py-1">
                            <a href="/about" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition">
                                About Us
                            </a>
                            <a href="/contact" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition">
                                Contact
                            </a>
                            <a href="/blog" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition">
                                Blog
                            </a>
                            <a href="/faq" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition">
                                FAQ
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
</div>
@endif
