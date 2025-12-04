{{-- 
/**
 * ModuleName: Frontend Hero Slider Component
 * Purpose: Auto-rotating hero slider for homepage with slide name indicators
 * 
 * Features:
 * - Auto-play with 5-second interval
 * - Manual navigation (prev/next arrows)
 * - Slide name indicators (instead of dots)
 * - Pause on hover
 * - Full-width banner images
 * - Database-driven content
 * 
 * @category Frontend
 * @package  Components
 * @created  2025-11-06
 */
--}}

@php
    $sliders = \App\Models\HeroSlider::getActive();
@endphp

@if($sliders->isEmpty())
    <!-- No sliders available -->
    <div class="bg-gray-100 py-16 text-center">
        <p class="text-gray-500">No sliders available</p>
    </div>
@else
<section class="relative overflow-visible bg-gray-100 mb-8" 
         x-data="{ 
            currentSlide: 0,
            autoplayInterval: null,
            isPlaying: true,
            showModal: false,
            slides: @js($sliders->map(function($slider) {
                return [
                    'id' => $slider->id,
                    'title' => $slider->title,
                    'subtitle' => $slider->subtitle,
                    'image' => $slider->image_url,
                    'link' => $slider->link,
                    'button_text' => $slider->button_text,
                ];
            })->values()),
            init() {
                this.startAutoplay();
            },
            startAutoplay() {
                if (!this.autoplayInterval) {
                    this.autoplayInterval = setInterval(() => {
                        this.nextSlide();
                    }, 5000);
                    this.isPlaying = true;
                }
            },
            stopAutoplay() {
                if (this.autoplayInterval) {
                    clearInterval(this.autoplayInterval);
                    this.autoplayInterval = null;
                }
                this.isPlaying = false;
            },
            nextSlide() {
                this.currentSlide = (this.currentSlide + 1) % this.slides.length;
            },
            prevSlide() {
                this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
            },
            goToSlide(index) {
                this.currentSlide = index;
            },
            openModal() {
                this.showModal = true;
                document.body.style.overflow = 'hidden';
            },
            closeModal() {
                this.showModal = false;
                document.body.style.overflow = 'auto';
            },
            selectSlide(index) {
                this.currentSlide = index;
                this.closeModal();
            },
            // Touch/Swipe functionality
            handleTouchStart(e) {
                this.touchStartX = e.touches[0].clientX;
                this.isDragging = true;
                this.stopAutoplay();
            },
            handleTouchMove(e) {
                if (!this.isDragging) return;
                
                this.touchEndX = e.touches[0].clientX;
                const diff = this.touchStartX - this.touchEndX;
                
                // Check if we should prevent browser swipe
                const canScrollLeft = this.currentSlide > 0;
                const canScrollRight = this.currentSlide < this.slides.length - 1;
                const isSwipingLeft = diff > 0;
                const isSwipingRight = diff < 0;
                
                // Prevent browser swipe only if slider can handle the gesture
                if ((isSwipingLeft && canScrollRight) || (isSwipingRight && canScrollLeft)) {
                    e.preventDefault();
                }
            },
            handleTouchEnd(e) {
                if (!this.isDragging) return;
                
                this.isDragging = false;
                const diff = this.touchStartX - this.touchEndX;
                const threshold = 50; // Minimum swipe distance
                
                if (Math.abs(diff) > threshold) {
                    if (diff > 0 && this.currentSlide < this.slides.length - 1) {
                        // Swipe left - go to next slide
                        this.nextSlide();
                    } else if (diff < 0 && this.currentSlide > 0) {
                        // Swipe right - go to previous slide
                        this.prevSlide();
                    }
                }
                
                this.startAutoplay();
            },
            // Initialize touch properties
            touchStartX: 0,
            touchEndX: 0,
            isDragging: false
         }"
         @mouseenter="stopAutoplay()"
         @mouseleave="startAutoplay()"
         @touchstart="handleTouchStart($event)"
         @touchmove="handleTouchMove($event)"
         @touchend="handleTouchEnd($event)">
    
    <!-- Slider Container with Overlay Navigation -->
    <div class="relative h-[300px] md:h-[400px]">
        <!-- Slider Images -->
        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="currentSlide === index"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-500"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0">
                
                <!-- Full Width Banner Image (Clickable if has link) -->
                <template x-if="slide.link && slide.link !== '#'">
                    <a :href="slide.link" class="block w-full h-full">
                        <img :src="slide.image" 
                             :alt="slide.title" 
                             class="w-full h-full object-cover cursor-pointer hover:opacity-95 transition-opacity">
                    </a>
                </template>
                <template x-if="!slide.link || slide.link === '#'">
                    <img :src="slide.image" 
                         :alt="slide.title" 
                         class="w-full h-full object-cover">
                </template>
            </div>
        </template>
        
        <!-- Previous Button -->
        <button @click="prevSlide()" 
                class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white p-3 rounded-full backdrop-blur-sm transition z-20">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        
        <!-- Next Button -->
        <button @click="nextSlide()" 
                class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white p-3 rounded-full backdrop-blur-sm transition z-20">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
        
        <!-- Slide Name Indicators (Responsive) -->
        <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 z-20 w-full max-w-[calc(100vw-2rem)] px-4">
            <div class="bg-gray-50/98 shadow-lg rounded-lg px-2 md:px-4 py-2.5 mx-auto w-fit max-w-full">
                <!-- Desktop Layout -->
                <div class="hidden md:flex items-center gap-2">
                    <!-- Navigation Buttons -->
                    <div class="flex items-center gap-2">
                        <template x-for="(slide, index) in slides" :key="index">
                            <button @click="goToSlide(index)" 
                                    class="px-3 py-2 text-center rounded-md transition-all duration-300 min-w-[100px]"
                                    :class="currentSlide === index ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200'">
                                <div class="text-sm font-semibold leading-tight" x-text="slide.title"></div>
                                <div class="text-xs mt-0.5" x-text="slide.subtitle"></div>
                            </button>
                        </template>
                    </div>
                    
                    <!-- View All Link -->
                    <button @click="openModal()" class="text-blue-600 hover:text-blue-700 font-medium text-sm whitespace-nowrap flex items-center gap-1 px-3 border-l border-gray-300">
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Mobile Layout -->
                <div class="md:hidden">
                    <div class="flex items-center justify-between">
                        <!-- Current Slide Info -->
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-900" x-text="slides[currentSlide]?.title"></div>
                            <div class="text-xs text-gray-600 mt-0.5" x-text="slides[currentSlide]?.subtitle"></div>
                        </div>
                        
                        <!-- View All Link (Mobile - Right Side) -->
                        <div class="ml-4">
                            <button @click="openModal()" class="text-blue-600 hover:text-blue-700 font-medium text-xs inline-flex items-center gap-1">
                                View All
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pause/Play Button -->
    <button @click="isPlaying ? stopAutoplay() : startAutoplay()" 
            class="absolute bottom-6 right-6 bg-white/30 hover:bg-white/50 text-white p-2 rounded-full backdrop-blur-sm transition z-20">
        <svg x-show="isPlaying" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <svg x-show="!isPlaying" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </button>

    <!-- View All Slides Modal -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0" 
             style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
             @click="closeModal()"></div>
        
        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden"
                 @click.stop>
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">All Slides</h2>
                    <button @click="closeModal()" 
                            class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div class="space-y-6">
                        <template x-for="(slide, index) in slides" :key="index">
                            <div class="border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-sm transition-all duration-200 overflow-hidden">
                                
                                <!-- Full Width Slide Image -->
                                <a :href="slide.link || '#'" class="block w-full">
                                    <img :src="slide.image" 
                                         :alt="slide.title" 
                                         class="w-full h-48 md:h-64 object-cover">
                                </a>
                                
                                <!-- Slide Content -->
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <!-- Slide Info -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900 mb-1" x-text="slide.title"></h3>
                                            <p class="text-sm text-gray-600" x-text="slide.subtitle"></p>
                                            
                                            <!-- Current Slide Indicator -->
                                            <div x-show="currentSlide === index" class="mt-3">
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Currently Showing
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Action Link -->
                                        <div class="flex-shrink-0">
                                            <template x-if="slide.link && slide.link !== '#'">
                                                <a :href="slide.link" 
                                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <span x-text="slide.button_text || 'Visit'"></span>
                                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                </a>
                                            </template>
                                            <template x-if="!slide.link || slide.link === '#'">
                                                <button @click="selectSlide(index)"
                                                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    View Slide
                                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50">
                    <button @click="closeModal()" 
                            class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
