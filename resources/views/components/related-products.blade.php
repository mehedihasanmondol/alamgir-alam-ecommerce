@props(['products', 'title' => 'Related Products'])

@if($products->count() > 0)
<div class="bg-white rounded-lg shadow-sm p-6 lg:p-8">
    <!-- Section Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">{{ $title }}</h2>
        @if($products->count() > 4)
            <a href="{{ route('shop') }}" class="text-green-600 hover:text-green-700 font-medium text-sm flex items-center space-x-1 transition">
                <span>View All</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @endif
    </div>

    <!-- Products Carousel -->
    <div x-data="{
        scrollContainer: null,
        canScrollLeft: false,
        canScrollRight: true,
        init() {
            this.scrollContainer = this.$refs.container;
            this.updateScrollButtons();
        },
        scroll(direction) {
            const scrollAmount = 300;
            if (direction === 'left') {
                this.scrollContainer.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            } else {
                this.scrollContainer.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
            setTimeout(() => this.updateScrollButtons(), 300);
        },
        updateScrollButtons() {
            this.canScrollLeft = this.scrollContainer.scrollLeft > 0;
            this.canScrollRight = this.scrollContainer.scrollLeft < (this.scrollContainer.scrollWidth - this.scrollContainer.clientWidth - 10);
        }
    }" class="relative">
        
        <!-- Scroll Left Button -->
        <button 
            @click="scroll('left')"
            x-show="canScrollLeft"
            class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white hover:bg-gray-50 p-2 rounded-full shadow-lg transition-all"
            style="margin-left: -16px;">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <!-- Products Container -->
        <div 
            x-ref="container"
            @scroll="updateScrollButtons()"
            class="flex space-x-4 overflow-x-auto scrollbar-hide pb-4"
            style="scroll-behavior: smooth;">
            
            @foreach($products as $product)
                <!-- Product Card -->
                <div class="flex-shrink-0 w-64">
                    <x-product-card-unified :product="$product" size="default" />
                </div>
            @endforeach
        </div>

        <!-- Scroll Right Button -->
        <button 
            @click="scroll('right')"
            x-show="canScrollRight"
            class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white hover:bg-gray-50 p-2 rounded-full shadow-lg transition-all"
            style="margin-right: -16px;">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>
@endif

<style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    /* Line clamp utility */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
