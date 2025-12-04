@props(['products'])

@if($products->count() > 0)
<div class="py-8">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Most Popular Products</h2>
        
        <!-- Products Carousel -->
        <div class="relative">
            <!-- Navigation Buttons -->
            <button 
                onclick="scrollCarousel('popular-products', 'left')" 
                class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 transition-all border border-gray-200"
                aria-label="Previous products"
            >
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <button 
                onclick="scrollCarousel('popular-products', 'right')" 
                class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 transition-all border border-gray-200"
                aria-label="Next products"
            >
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Products Grid -->
            <div 
                id="popular-products" 
                class="flex gap-4 overflow-x-auto scroll-smooth pb-4 scrollbar-hide"
                style="scrollbar-width: none; -ms-overflow-style: none;"
            >
                @foreach($products as $product)
                    <div class="flex-none w-[calc(75%-0.75rem)] sm:w-[calc(50%-0.5rem)] md:w-[calc(33.333%-0.667rem)] lg:w-[calc(25%-0.75rem)] xl:w-[calc(20%-0.8rem)]">
                        <x-product-card-unified :product="$product" size="default" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Carousel Scroll Script -->
<script>
// Prevent duplicate function definition
if (typeof scrollCarousel === 'undefined') {
    function scrollCarousel(carouselId, direction) {
        const carousel = document.getElementById(carouselId);
        const scrollAmount = 220; // Card width (200px) + gap (20px)
        
        if (direction === 'left') {
            carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else {
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    }
}

// Hide scrollbar for webkit browsers
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    `;
    document.head.appendChild(style);
});
</script>

<!-- Custom Styles -->
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endif
