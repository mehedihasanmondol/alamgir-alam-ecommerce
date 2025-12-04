<style>
    [x-cloak] { display: none !important; }
</style>

<div>
    <!-- Rating Summary -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Average Rating -->
            <div class="flex items-center space-x-6">
                <div class="text-center">
                    <div class="text-5xl font-bold text-gray-900">{{ number_format($averageRating, 1) }}</div>
                    <div class="flex items-center justify-center mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <div class="text-sm text-gray-600 mt-1">{{ $totalCount }} {{ Str::plural('review', $totalCount) }}</div>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="space-y-2">
                @foreach($ratingDistribution as $star => $count)
                    <button wire:click="filterByRating({{ $star }})" 
                            class="flex items-center w-full space-x-2 hover:bg-gray-50 p-1 rounded transition-colors {{ $filterRating == $star ? 'bg-blue-50' : '' }}">
                        <span class="text-sm font-medium text-gray-700 w-12">{{ $star }} star</span>
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-yellow-400" style="width: {{ $totalCount > 0 ? ($count / $totalCount * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600 w-12 text-right">{{ $count }}</span>
                    </button>
                @endforeach
                @if($filterRating)
                    <button wire:click="filterByRating(null)" class="text-sm text-blue-600 hover:text-blue-700 mt-2">
                        Clear filter
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Sort and Filter -->
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">
            Customer Reviews 
            @if($filterRating)
                ({{ $filterRating }} star)
            @endif
        </h3>
        <select wire:model.live="sortBy" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="recent">Most Recent</option>
            <option value="helpful">Most Helpful</option>
            <option value="highest">Highest Rating</option>
            <option value="lowest">Lowest Rating</option>
        </select>
    </div>

    <!-- Reviews List -->
    <div class="space-y-6">
        @forelse($reviews as $review)
            <div class="border-b border-gray-200 pb-6">
                <!-- Review Header -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-sm font-semibold text-gray-600">
                            {{ substr($review->reviewer_name, 0, 2) }}
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-gray-900">{{ $review->reviewer_name }}</span>
                                @if($review->is_verified_purchase)
                                    <span class="flex items-center text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Verified Purchase
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2 mt-1">
                                <!-- Star Rating -->
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review Title -->
                @if($review->title)
                    <h4 class="font-semibold text-gray-900 mb-2">{{ $review->title }}</h4>
                @endif

                <!-- Review Text -->
                <p class="text-gray-700 mb-3">{{ $review->review }}</p>

                <!-- Review Images -->
                @if($review->images && count($review->images) > 0)
                    <div class="flex items-center space-x-2 mb-3" x-data="{ openGallery: false, currentImage: 0, images: {{ json_encode(array_map(fn($img) => asset('storage/' . $img), $review->images)) }} }">
                        @foreach($review->images as $index => $image)
                            <img src="{{ asset('storage/' . $image) }}" 
                                 alt="Review image" 
                                 class="w-20 h-20 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-75 transition-opacity"
                                 @click="openGallery = true; currentImage = {{ $index }}">
                        @endforeach
                        
                        <!-- Gallery Modal -->
                        <div x-show="openGallery" 
                             x-cloak
                             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90"
                             @click.self="openGallery = false"
                             @keydown.escape.window="openGallery = false">
                            
                            <!-- Close Button -->
                            <button @click="openGallery = false" 
                                    class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            
                            <!-- Previous Button -->
                            <button @click="currentImage = currentImage > 0 ? currentImage - 1 : images.length - 1" 
                                    class="absolute left-4 text-white hover:text-gray-300 z-10"
                                    x-show="images.length > 1">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Image Display -->
                            <div class="max-w-6xl max-h-screen p-4">
                                <template x-for="(image, index) in images" :key="index">
                                    <img x-show="currentImage === index"
                                         :src="image"
                                         alt="Review image"
                                         class="max-w-full max-h-[90vh] object-contain mx-auto">
                                </template>
                                
                                <!-- Image Counter -->
                                <div class="text-center text-white mt-4" x-show="images.length > 1">
                                    <span x-text="(currentImage + 1) + ' / ' + images.length"></span>
                                </div>
                            </div>
                            
                            <!-- Next Button -->
                            <button @click="currentImage = currentImage < images.length - 1 ? currentImage + 1 : 0" 
                                    class="absolute right-4 text-white hover:text-gray-300 z-10"
                                    x-show="images.length > 1">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Helpful Votes -->
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Helpful?</span>
                    <button wire:click="voteHelpful({{ $review->id }}, true)" 
                            class="flex items-center space-x-1 text-sm text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                        <span>{{ $review->helpful_count }}</span>
                    </button>
                    <button wire:click="voteHelpful({{ $review->id }}, false)" 
                            class="flex items-center space-x-1 text-sm text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"/>
                        </svg>
                        <span>{{ $review->not_helpful_count }}</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No reviews yet</h3>
                <p class="mt-1 text-sm text-gray-500">Be the first to review this product.</p>
            </div>
        @endforelse
    </div>

    <!-- Load More Section -->
    @if($reviews->count() > 0)
        <div class="mt-6">
            <!-- Showing count -->
            <div class="text-center text-sm text-gray-600 mb-4">
                Showing <span class="font-semibold text-gray-900">{{ $loadedCount }}</span> of 
                <span class="font-semibold text-gray-900">{{ $totalCount }}</span> 
                {{ Str::plural('review', $totalCount) }}
            </div>

            <!-- Load More Button -->
            @if($hasMore)
                <div class="text-center">
                    <button wire:click="loadMore" 
                            wire:loading.attr="disabled"
                            class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <span wire:loading.remove wire:target="loadMore">Load More Reviews</span>
                        <span wire:loading wire:target="loadMore" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading...
                        </span>
                    </button>
                </div>
            @endif
        </div>
    @endif
</div>
