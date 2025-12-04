<div>
    {{-- Vote Message --}}
    @if($voteMessage)
        <div class="mb-6 p-4 rounded-lg border {{ $voteMessageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' }} flex items-center justify-between"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5000)">
            <div class="flex items-center">
                @if($voteMessageType === 'success')
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                @endif
                <span>{{ $voteMessage }}</span>
            </div>
            <button @click="show = false" class="text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- Rating Summary --}}
    @if($ratingEnabled)
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Average Rating --}}
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
                    <div class="text-sm text-gray-600 mt-1">{{ $totalCount }} {{ Str::plural('feedback', $totalCount) }}</div>
                </div>
            </div>

            {{-- Rating Distribution --}}
            <div class="space-y-2">
                @foreach($ratingDistribution as $star => $data)
                    <button wire:click="filterByRating({{ $star }})" 
                            class="flex items-center w-full space-x-2 hover:bg-gray-50 p-1 rounded transition-colors {{ $filterRating == $star ? 'bg-blue-50' : '' }}">
                        <span class="text-sm font-medium text-gray-700 w-12">{{ $star }} star</span>
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-yellow-400" style="width: {{ $data['percentage'] }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600 w-12 text-right">{{ $data['count'] }}</span>
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
    @endif

    {{-- Sort and Filter --}}
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">
            Customer Feedback 
            @if($filterRating)
                ({{ $filterRating }} star)
            @endif
        </h3>
        <select wire:model.live="sortBy" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="recent">Most Recent</option>
            <option value="helpful">Most Helpful</option>
            @if($ratingEnabled)
            <option value="highest">Highest Rating</option>
            <option value="lowest">Lowest Rating</option>
            @endif
        </select>
    </div>

    {{-- Feedback List --}}
    <div class="space-y-6">
        @forelse($feedbackItems as $item)
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                {{-- Feedback Header --}}
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-sm font-semibold text-white">
                            {{ strtoupper(substr($item->customer_display_name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-gray-900">{{ $item->customer_display_name }}</span>
                                @if($item->is_featured)
                                    <span class="flex items-center text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        Featured
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2 mt-1">
                                {{-- Star Rating --}}
                                @if($ratingEnabled)
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $item->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                @endif
                                @if($timeEnabled)
                                <span class="text-sm text-gray-500">{{ $item->created_at->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Feedback Title --}}
                @if($item->title)
                    <h4 class="font-semibold text-gray-900 mb-2">{{ $item->title }}</h4>
                @endif

                {{-- Feedback Content --}}
                <p class="text-gray-700 mb-4">{{ $item->feedback }}</p>

                {{-- Feedback Images --}}
                @if($showImages && $item->images && count($item->images) > 0)
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($item->images as $index => $image)
                            @php
                                $thumbnailPath = is_array($image) ? ($image['thumbnail'] ?? $image['original']) : $image;
                            @endphp
                            <img src="{{ asset('storage/' . $thumbnailPath) }}" 
                                 alt="Feedback image" 
                                 wire:click="openGallery({{ $item->id }}, {{ $index }})"
                                 class="w-20 h-20 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-75 hover:border-blue-500 transition-all">
                        @endforeach
                    </div>
                @endif

                {{-- Helpful Actions --}}
                @if($helpfulEnabled)
                <div class="flex items-center space-x-4 pt-4 border-t border-gray-100">
                    @auth
                        <button wire:click="voteHelpful({{ $item->id }})" 
                                class="flex items-center space-x-1 text-sm text-gray-600 hover:text-green-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                            </svg>
                            <span>Helpful ({{ $item->helpful_count }})</span>
                        </button>
                        <button wire:click="voteNotHelpful({{ $item->id }})" 
                                class="flex items-center space-x-1 text-sm text-gray-600 hover:text-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"/>
                            </svg>
                            <span>Not Helpful ({{ $item->not_helpful_count }})</span>
                        </button>
                    @else
                        <button wire:click="voteHelpful({{ $item->id }})" 
                                class="flex items-center space-x-1 text-sm text-gray-400 cursor-not-allowed" 
                                title="Please log in to vote">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                            </svg>
                            <span>Helpful ({{ $item->helpful_count }})</span>
                        </button>
                        <button wire:click="voteNotHelpful({{ $item->id }})" 
                                class="flex items-center space-x-1 text-sm text-gray-400 cursor-not-allowed" 
                                title="Please log in to vote">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"/>
                            </svg>
                            <span>Not Helpful ({{ $item->not_helpful_count }})</span>
                        </button>
                    @endauth
                </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                <p class="text-gray-500 text-lg font-medium">No feedback yet</p>
                <p class="text-gray-400 mt-1">Be the first to share your experience!</p>
            </div>
        @endforelse
    </div>

    {{-- Load More Button --}}
    @if($hasMore)
        <div class="text-center mt-8">
            <button wire:click="loadMore" 
                    class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                Load More ({{ $loadedCount }} of {{ $totalCount }})
            </button>
        </div>
    @endif

    {{-- Image Gallery Modal --}}
    @if($showGalleryModal && count($galleryImages) > 0)
    <div class="fixed inset-0 z-[60] bg-black bg-opacity-95"
         x-data="{ show: @entangle('showGalleryModal') }"
         x-show="show"
         @keydown.escape.window="$wire.closeGallery()"
         @keydown.arrow-left.window="$wire.previousImage()"
         @keydown.arrow-right.window="$wire.nextImage()"
         @click="$wire.closeGallery()"
         x-cloak
         style="display: none;">
        
        <!-- Close Button -->
        <button @click.stop="$wire.closeGallery()" 
                class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-20 bg-black bg-opacity-50 rounded-full p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Main Content Container -->
        <div class="h-full flex flex-col items-center justify-center p-4" @click.stop>
            
            <!-- Image Container with Navigation -->
            <div class="relative w-full max-w-5xl flex items-center justify-center mb-20">
                
                <!-- Previous Button -->
                @if(count($galleryImages) > 1)
                <button wire:click="previousImage" 
                        @click.stop
                        class="absolute left-0 text-white hover:text-gray-300 transition-colors z-10 bg-black bg-opacity-50 rounded-full p-3 hover:bg-opacity-70">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                @endif

                <!-- Image -->
                <div class="relative">
                    <img src="{{ $galleryImages[$currentImageIndex] ?? '' }}" 
                         alt="Feedback Image"
                         class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-2xl"
                         @click.stop>
                    
                    <!-- Image Counter -->
                    <div class="absolute -bottom-10 left-1/2 transform -translate-x-1/2 bg-white text-gray-900 px-4 py-2 rounded-full text-sm font-medium shadow-lg">
                        {{ $currentImageIndex + 1 }} / {{ count($galleryImages) }}
                    </div>
                </div>

                <!-- Next Button -->
                @if(count($galleryImages) > 1)
                <button wire:click="nextImage" 
                        @click.stop
                        class="absolute right-0 text-white hover:text-gray-300 transition-colors z-10 bg-black bg-opacity-50 rounded-full p-3 hover:bg-opacity-70">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
                @endif
            </div>

            <!-- Thumbnail Strip -->
            @if(count($galleryImages) > 1)
            <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 max-w-4xl" @click.stop>
                <div class="flex gap-2 px-4 py-3 bg-white rounded-lg shadow-2xl overflow-x-auto">
                    @foreach($galleryImages as $index => $image)
                        <div wire:click="currentImageIndex = {{ $index }}" 
                             @click.stop
                             class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden cursor-pointer transition-all hover:ring-4 hover:ring-blue-400 {{ $currentImageIndex === $index ? 'ring-4 ring-blue-500' : 'ring-2 ring-gray-300' }}">
                            <img src="{{ $image }}" 
                                 alt="Thumbnail"
                                 class="w-full h-full object-cover {{ $currentImageIndex === $index ? 'opacity-100' : 'opacity-60' }}">
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
