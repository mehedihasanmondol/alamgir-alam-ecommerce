@props(['product'])

@php
    $images = $product->images->sortBy('sort_order');
    $mainImage = $images->first();
@endphp

<div x-data="{
    currentIndex: 0,
    images: {{ json_encode($images->map(function($img) {
        return [
            'full' => $img->getImageUrl() ?? asset('images/placeholder.png'),
            'thumb' => $img->getThumbnailUrl() ?? asset('images/placeholder.png')
        ];
    })->values()) }},
    showLightbox: false,
    get currentImage() {
        return this.images[this.currentIndex] || { full: '{{ asset('images/placeholder.png') }}', thumb: '{{ asset('images/placeholder.png') }}' };
    },
    next() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
    },
    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
    },
    selectImage(index) {
        this.currentIndex = index;
    }
}" class="space-y-4">
    
    <!-- Main Image Display -->
    <div class="relative bg-white rounded-lg overflow-hidden border border-gray-200 group">
        <!-- Main Image -->
        <div class="aspect-square relative overflow-hidden cursor-zoom-in" @click="showLightbox = true">
            <img :src="currentImage.full" 
                 :alt="'{{ $product->name }}'"
                 class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-110">
            
            <!-- Image Counter -->
            <div class="absolute top-4 right-4 bg-black bg-opacity-60 text-white text-sm px-3 py-1 rounded-full">
                <span x-text="currentIndex + 1"></span>/<span x-text="images.length"></span>
            </div>

            <!-- Zoom Icon -->
            <div class="absolute bottom-4 right-4 bg-white bg-opacity-90 p-2 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                </svg>
            </div>
        </div>

        <!-- Navigation Arrows -->
        <template x-if="images.length > 1">
            <div>
                <!-- Previous Button -->
                <button @click="prev()" 
                        class="absolute left-2 top-1/2 -translate-y-1/2 bg-white bg-opacity-90 hover:bg-opacity-100 p-2 rounded-full shadow-lg transition-all opacity-0 group-hover:opacity-100">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <!-- Next Button -->
                <button @click="next()" 
                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-white bg-opacity-90 hover:bg-opacity-100 p-2 rounded-full shadow-lg transition-all opacity-0 group-hover:opacity-100">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Thumbnail Gallery -->
    <template x-if="images.length > 1">
        <div class="flex space-x-2 overflow-x-auto pb-2">
            <template x-for="(image, index) in images" :key="index">
                <button @click="selectImage(index)" 
                        :class="{ 'ring-2 ring-green-600': currentIndex === index, 'ring-1 ring-gray-200': currentIndex !== index }"
                        class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden hover:ring-2 hover:ring-green-400 transition-all">
                    <img :src="image.thumb" 
                         :alt="'{{ $product->name }} thumbnail ' + (index + 1)"
                         class="w-full h-full object-cover">
                </button>
            </template>
        </div>
    </template>

    <!-- Lightbox Modal -->
    <div x-show="showLightbox" 
         x-cloak
         @click.self="showLightbox = false"
         @keydown.escape.window="showLightbox = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90 p-4"
         style="display: none;">
        
        <!-- Close Button -->
        <button @click="showLightbox = false" 
                class="absolute top-4 right-4 text-white hover:text-gray-300 transition z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Image Counter -->
        <div class="absolute top-4 left-1/2 -translate-x-1/2 bg-black bg-opacity-60 text-white px-4 py-2 rounded-full z-10">
            <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
        </div>

        <!-- Main Lightbox Image -->
        <div class="relative max-w-6xl max-h-full">
            <img :src="currentImage.full" 
                 :alt="'{{ $product->name }}'"
                 class="max-w-full max-h-[90vh] object-contain">
        </div>

        <!-- Navigation Arrows -->
        <template x-if="images.length > 1">
            <div>
                <!-- Previous Button -->
                <button @click="prev()" 
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white bg-opacity-20 hover:bg-opacity-30 p-3 rounded-full transition">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <!-- Next Button -->
                <button @click="next()" 
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white bg-opacity-20 hover:bg-opacity-30 p-3 rounded-full transition">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </template>

        <!-- Thumbnail Strip -->
        <template x-if="images.length > 1">
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2 bg-black bg-opacity-60 p-2 rounded-lg max-w-full overflow-x-auto">
                <template x-for="(image, index) in images" :key="index">
                    <button @click="selectImage(index)" 
                            :class="{ 'ring-2 ring-white': currentIndex === index }"
                            class="flex-shrink-0 w-16 h-16 rounded overflow-hidden hover:ring-2 hover:ring-gray-300 transition">
                        <img :src="image.thumb" 
                             :alt="'Thumbnail ' + (index + 1)"
                             class="w-full h-full object-cover">
                    </button>
                </template>
            </div>
        </template>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
