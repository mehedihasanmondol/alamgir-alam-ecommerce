<div>
    <button 
        wire:click="toggleWishlist"
        type="button"
        class="group relative inline-flex items-center justify-center bg-white rounded-full shadow-md hover:scale-110 transition-all duration-200
               @if($size === 'sm') p-1.5
               @elseif($size === 'lg') p-3
               @else p-2
               @endif
               @if($isInWishlist) 
                   text-red-500 hover:text-red-600
               @else 
                   text-gray-400 hover:text-red-500
               @endif"
        title="{{ $isInWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}"
    >
        @if($isInWishlist)
            <!-- Filled Heart (In Wishlist) -->
            <svg class="
                @if($size === 'sm') w-4 h-4
                @elseif($size === 'lg') w-7 h-7
                @else w-5 h-5
                @endif
                fill-current" 
                viewBox="0 0 20 20">
                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
            </svg>
        @else
            <!-- Outline Heart (Not in Wishlist) -->
            <svg class="
                @if($size === 'sm') w-4 h-4
                @elseif($size === 'lg') w-7 h-7
                @else w-5 h-5
                @endif" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
        @endif
        
        @if(!$showIcon)
            <span class="ml-2 text-sm font-medium">
                {{ $isInWishlist ? 'Saved' : 'Save' }}
            </span>
        @endif
        
        <!-- Tooltip on hover -->
        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
            {{ $isInWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}
        </span>
    </button>
</div>
