@props(['categories'])

<style>
.category-slider {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    cursor: grab;
}

.category-slider:active {
    cursor: grabbing;
}

.category-item {
    min-width: calc((100% - 2 * 16px) / 3); /* Mobile: 3 items */
}

@media (min-width: 640px) {
    .category-item {
        min-width: calc((100% - 3 * 16px) / 4); /* SM: 4 items */
    }
}

@media (min-width: 768px) {
    .category-item {
        min-width: calc((100% - 5 * 16px) / 6); /* MD: 6 items */
    }
}

@media (min-width: 1024px) {
    .category-item {
        min-width: calc((100% - 7 * 16px) / 8); /* LG: 8 items */
    }
}
</style>

<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Shop by category</h2>
        </div>

        <!-- Main Categories Slider -->
        <div class="relative mb-8" x-data="categorySlider()">
            <!-- Previous Button (Hidden on mobile) -->
            <button 
                @click="prev()" 
                class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-white rounded-full shadow-lg items-center justify-center hover:bg-gray-50 transition -ml-5 hidden md:flex"
                :class="{ 'opacity-50 cursor-not-allowed': atStart }"
                :disabled="atStart"
            >
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <!-- Categories Container -->
            <div class="overflow-hidden">
                <div 
                    x-ref="slider"
                    class="flex gap-4 transition-transform duration-300 ease-in-out category-slider"
                    :style="`transform: translateX(-${currentIndex * slideWidth}px)`"
                    @touchstart="handleTouchStart($event)"
                    @touchmove="handleTouchMove($event)"
                    @touchend="handleTouchEnd($event)"
                    @mousedown="handleMouseDown($event)"
                    @mousemove="handleMouseMove($event)"
                    @mouseup="handleMouseUp($event)"
                    @mouseleave="handleMouseUp($event)"
                >
                    @foreach($categories as $category)
                        <a href="{{ route('categories.show', $category->slug) }}" 
                           class="flex-shrink-0 flex flex-col items-center p-2 sm:p-4 rounded-lg hover:bg-gray-50 transition group category-item">
                            <!-- Icon Circle -->
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-green-50 rounded-full flex items-center justify-center mb-2 sm:mb-3 group-hover:bg-green-100 transition overflow-hidden">
                                @if($category->media)
                                    <img src="{{ $category->getThumbnailUrl() }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                                @else
                                    <!-- Default SVG Icon -->
                                    <svg class="w-8 h-8 sm:w-12 sm:h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                @endif
                            </div>
                            <!-- Category Name -->
                            <span class="text-xs sm:text-sm font-medium text-gray-900 text-center line-clamp-2">{{ $category->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Next Button (Hidden on mobile) -->
            <button 
                @click="next()" 
                class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-white rounded-full shadow-lg items-center justify-center hover:bg-gray-50 transition -mr-5 hidden md:flex"
                :class="{ 'opacity-50 cursor-not-allowed': atEnd }"
                :disabled="atEnd"
            >
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Mobile Scroll Indicator -->
            <div class="flex justify-center mt-4 md:hidden">
                <div class="flex space-x-2">
                    <template x-for="i in Math.ceil(totalItems / itemsPerView)" :key="i">
                        <div 
                            class="w-2 h-2 rounded-full transition-colors duration-200"
                            :class="Math.floor(currentIndex / itemsPerView) === (i - 1) ? 'bg-green-600' : 'bg-gray-300'"
                        ></div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Subcategories / Tags -->
        @php
            // Get all subcategories (child categories) from the main categories
            $subcategories = \App\Modules\Ecommerce\Category\Models\Category::whereNotNull('parent_id')
                ->whereIn('parent_id', $categories->pluck('id'))
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(12)
                ->get();
        @endphp
        
        @if($subcategories->count() > 0)
            <div class="flex flex-wrap gap-3 items-center">
                @foreach($subcategories as $subcategory)
                    <a href="{{ route('categories.show', $subcategory->slug) }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-full transition">
                        {{ $subcategory->name }}
                    </a>
                @endforeach
                
                @if($subcategories->count() >= 12)
                    <!-- View All Link -->
                    <a href="{{ route('categories.index') }}" 
                       class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-full transition"
                       title="View all categories">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>

<script>
function categorySlider() {
    return {
        currentIndex: 0,
        slideWidth: 0,
        itemsPerView: 8,
        totalItems: {{ $categories->count() }},
        atStart: true,
        atEnd: false,
        
        // Touch/Swipe variables
        startX: 0,
        startY: 0,
        currentX: 0,
        currentY: 0,
        isDragging: false,
        isMouseDown: false,
        startTime: 0,
        
        init() {
            this.calculateResponsiveSettings();
            this.updateButtonStates();
            
            window.addEventListener('resize', () => {
                this.calculateResponsiveSettings();
                this.updateButtonStates();
            });
        },
        
        calculateResponsiveSettings() {
            const container = this.$refs.slider;
            if (!container || !container.children.length) return;
            
            const screenWidth = window.innerWidth;
            
            // Responsive items per view
            if (screenWidth < 640) { // mobile
                this.itemsPerView = 3;
            } else if (screenWidth < 768) { // sm
                this.itemsPerView = 4;
            } else if (screenWidth < 1024) { // md
                this.itemsPerView = 6;
            } else { // lg and above
                this.itemsPerView = 8;
            }
            
            // Calculate slide width
            const containerWidth = container.offsetWidth;
            const gap = 16; // 1rem gap
            this.slideWidth = (containerWidth + gap) / this.itemsPerView;
            
            // Adjust current index if needed
            const maxIndex = Math.max(0, this.totalItems - this.itemsPerView);
            if (this.currentIndex > maxIndex) {
                this.currentIndex = maxIndex;
            }
        },
        
        prev() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.updateButtonStates();
            }
        },
        
        next() {
            const maxIndex = Math.max(0, this.totalItems - this.itemsPerView);
            if (this.currentIndex < maxIndex) {
                this.currentIndex++;
                this.updateButtonStates();
            }
        },
        
        updateButtonStates() {
            this.atStart = this.currentIndex === 0;
            this.atEnd = this.currentIndex >= Math.max(0, this.totalItems - this.itemsPerView);
        },
        
        // Touch event handlers
        handleTouchStart(e) {
            this.startX = e.touches[0].clientX;
            this.startY = e.touches[0].clientY;
            this.currentX = this.startX;
            this.currentY = this.startY;
            this.isDragging = true;
            this.startTime = Date.now();
        },
        
        handleTouchMove(e) {
            if (!this.isDragging) return;
            
            this.currentX = e.touches[0].clientX;
            this.currentY = e.touches[0].clientY;
            
            // Prevent default scrolling if horizontal swipe
            const deltaX = Math.abs(this.currentX - this.startX);
            const deltaY = Math.abs(this.currentY - this.startY);
            
            if (deltaX > deltaY) {
                e.preventDefault();
            }
        },
        
        handleTouchEnd(e) {
            if (!this.isDragging) return;
            
            const deltaX = this.currentX - this.startX;
            const deltaY = this.currentY - this.startY;
            const deltaTime = Date.now() - this.startTime;
            
            // Check if it's a horizontal swipe
            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
                if (deltaX > 0 && deltaTime < 300) {
                    // Swipe right - go to previous
                    this.prev();
                } else if (deltaX < 0 && deltaTime < 300) {
                    // Swipe left - go to next
                    this.next();
                }
            }
            
            this.isDragging = false;
        },
        
        // Mouse event handlers (for desktop drag)
        handleMouseDown(e) {
            this.startX = e.clientX;
            this.startY = e.clientY;
            this.currentX = this.startX;
            this.currentY = this.startY;
            this.isMouseDown = true;
            this.startTime = Date.now();
            e.preventDefault();
        },
        
        handleMouseMove(e) {
            if (!this.isMouseDown) return;
            
            this.currentX = e.clientX;
            this.currentY = e.clientY;
        },
        
        handleMouseUp(e) {
            if (!this.isMouseDown) return;
            
            const deltaX = this.currentX - this.startX;
            const deltaY = this.currentY - this.startY;
            const deltaTime = Date.now() - this.startTime;
            
            // Check if it's a horizontal drag
            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
                if (deltaX > 0 && deltaTime < 300) {
                    // Drag right - go to previous
                    this.prev();
                } else if (deltaX < 0 && deltaTime < 300) {
                    // Drag left - go to next
                    this.next();
                }
            }
            
            this.isMouseDown = false;
        }
    }
}
</script>
