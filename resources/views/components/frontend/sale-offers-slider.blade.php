@props(['products', 'title' => 'Sale Offers'])

@if($products->count() > 0)
<section class="py-8 bg-white border-t border-b border-gray-200">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ $title }}</h2>
            <a href="#" class="text-blue-600 hover:text-blue-700 font-medium text-sm hover:underline">
                Shop all
            </a>
        </div>

        <!-- Products Slider -->
        <div class="relative">
            <!-- Navigation Buttons -->
            <button 
                onclick="scrollCarousel('sale-slider', 'left')" 
                class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 transition-all border border-gray-200"
                aria-label="Previous products"
            >
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <button 
                onclick="scrollCarousel('sale-slider', 'right')" 
                class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 transition-all border border-gray-200"
                aria-label="Next products"
            >
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Products Slider -->
            <div 
                id="sale-slider" 
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
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.getElementById('sale-slider');
        const prevBtn = document.getElementById('sale-prev');
        const nextBtn = document.getElementById('sale-next');
        
        if (!slider || !prevBtn || !nextBtn) return;

        let currentPosition = 0;
        
        function getScrollAmount() {
            const itemWidth = slider.children[0]?.offsetWidth || 0;
            const gap = 16; // 1rem = 16px
            const isMobile = window.innerWidth < 640; // sm breakpoint
            
            if (isMobile) {
                // On mobile, scroll by 75% of item width to show next item partially
                return itemWidth * 0.75 + gap;
            } else {
                // On desktop, scroll by full item width
                return itemWidth + gap;
            }
        }
        
        function getMaxScroll() {
            return Math.max(0, slider.scrollWidth - slider.parentElement.offsetWidth);
        }

        function updateButtons() {
            const maxScroll = getMaxScroll();
            
            prevBtn.style.opacity = currentPosition <= 0 ? '0.5' : '1';
            prevBtn.style.pointerEvents = currentPosition <= 0 ? 'none' : 'auto';
            
            nextBtn.style.opacity = currentPosition >= maxScroll ? '0.5' : '1';
            nextBtn.style.pointerEvents = currentPosition >= maxScroll ? 'none' : 'auto';
        }

        prevBtn.addEventListener('click', () => {
            const scrollAmount = getScrollAmount();
            currentPosition = Math.max(0, currentPosition - scrollAmount);
            slider.style.transform = `translateX(-${currentPosition}px)`;
            updateButtons();
        });

        nextBtn.addEventListener('click', () => {
            const scrollAmount = getScrollAmount();
            const maxScroll = getMaxScroll();
            currentPosition = Math.min(maxScroll, currentPosition + scrollAmount);
            slider.style.transform = `translateX(-${currentPosition}px)`;
            updateButtons();
        });

        // Initialize button states
        updateButtons();

        // Touch/Swipe functionality
        let touchStartX = 0;
        let touchEndX = 0;
        let isDragging = false;
        let startPosition = 0;

        slider.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            startPosition = currentPosition;
            isDragging = true;
            slider.style.transition = 'none';
        });

        slider.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            
            touchEndX = e.touches[0].clientX;
            const diff = touchStartX - touchEndX;
            const newPosition = startPosition + diff;
            const maxScroll = getMaxScroll();
            
            // Check if we should prevent browser swipe
            const canScrollLeft = currentPosition > 0;
            const canScrollRight = currentPosition < maxScroll;
            const isSwipingLeft = diff > 0;
            const isSwipingRight = diff < 0;
            
            // Prevent browser swipe only if slider can handle the gesture
            if ((isSwipingLeft && canScrollRight) || (isSwipingRight && canScrollLeft)) {
                e.preventDefault();
            }
            
            // Apply some resistance at the boundaries
            let constrainedPosition = newPosition;
            
            if (newPosition < 0) {
                constrainedPosition = newPosition * 0.3; // Resistance when going left
            } else if (newPosition > maxScroll) {
                constrainedPosition = maxScroll + (newPosition - maxScroll) * 0.3; // Resistance when going right
            }
            
            slider.style.transform = `translateX(-${constrainedPosition}px)`;
        });

        slider.addEventListener('touchend', (e) => {
            if (!isDragging) return;
            
            isDragging = false;
            slider.style.transition = 'transform 0.3s ease-in-out';
            
            const diff = touchStartX - touchEndX;
            const threshold = 50; // Minimum swipe distance
            
            if (Math.abs(diff) > threshold) {
                if (diff > 0) {
                    // Swipe left - go to next
                    const scrollAmount = getScrollAmount();
                    const maxScroll = getMaxScroll();
                    currentPosition = Math.min(maxScroll, currentPosition + scrollAmount);
                } else {
                    // Swipe right - go to previous
                    const scrollAmount = getScrollAmount();
                    currentPosition = Math.max(0, currentPosition - scrollAmount);
                }
            } else {
                // Snap back to current position if swipe was too small
                currentPosition = startPosition;
            }
            
            slider.style.transform = `translateX(-${currentPosition}px)`;
            updateButtons();
        });

        // Prevent default drag behavior on images
        slider.addEventListener('dragstart', (e) => {
            e.preventDefault();
        });

        // Update on window resize
        window.addEventListener('resize', () => {
            currentPosition = 0;
            slider.style.transform = 'translateX(0)';
            updateButtons();
        });
    });
</script>
@endpush
@endif
