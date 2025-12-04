@props([
    'post',
    'class' => ''
])

<article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 group {{ $class }}">
    <!-- Media Section (Thumbnail with Video Badge) - Only show if media exists -->
    @if($post->youtube_url || $post->media || $post->featured_image)
    <a href="{{ route('products.show', $post->slug) }}" class="block relative">
        @if($post->youtube_url)
            <!-- YouTube Video Thumbnail (Priority) -->
            <div class="relative overflow-hidden" style="aspect-ratio: 16/9;">
                <img src="https://img.youtube.com/vi/{{ $post->youtube_video_id }}/maxresdefault.jpg" 
                     alt="{{ $post->title }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                     onerror="this.src='https://img.youtube.com/vi/{{ $post->youtube_video_id }}/hqdefault.jpg'">
                
                <!-- Video Play Badge -->
                <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/30 transition-colors">
                    <div class="bg-red-600 rounded-full p-4 group-hover:scale-110 transition-transform shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                        </svg>
                    </div>
                </div>
                
                <!-- YouTube Badge -->
                <div class="absolute top-3 right-3 bg-red-600 text-white px-2 py-1 rounded text-xs font-semibold flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                    </svg>
                    VIDEO
                </div>
            </div>
        @elseif($post->media)
            <!-- Featured Image from Media Library -->
            @php
                $aspectRatio = ($post->media->width && $post->media->height) 
                    ? $post->media->width / $post->media->height 
                    : 16/9;
            @endphp
            <div class="relative overflow-hidden" style="aspect-ratio: {{ $aspectRatio }};">
                <img src="{{ $post->media->large_url }}" 
                     alt="{{ $post->featured_image_alt ?? $post->title }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            </div>
        @elseif($post->featured_image)
            <!-- Featured Image (Legacy) -->
            <div class="relative overflow-hidden" style="aspect-ratio: 16/9;">
                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                     alt="{{ $post->featured_image_alt }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            </div>
        @endif
    </a>
    @endif

    <!-- Content Section -->
    <div class="p-5">
        <!-- Categories Badges -->
        @if($post->categories && $post->categories->count() > 0)
            <div class="flex flex-wrap gap-2 mb-2">
                @foreach($post->categories as $category)
                    <a href="{{ route('blog.category', $category->slug) }}" 
                       class="inline-block text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Title -->
        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
            <a href="{{ route('products.show', $post->slug) }}">
                {{ $post->title }}
            </a>
        </h3>

        <!-- Excerpt -->
        @if($post->excerpt)
            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $post->excerpt }}</p>
        @endif

        <!-- Tick Marks -->
        @if($post->tickMarks && $post->tickMarks->count() > 0)
            <div class="mb-3">
                <x-blog.tick-marks :post="$post" />
            </div>
        @endif

        <!-- Meta Info -->
        <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t border-gray-100 mb-3">
            @if(\App\Models\SiteSetting::get('blog_show_date', '1') === '1')
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ $post->published_at->format('M d, Y') }}
            </span>
            @endif
            @if(\App\Models\SiteSetting::get('blog_show_views', '1') === '1')
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                {{ number_format($post->views_count) }}
            </span>
            @endif
            <!-- Read More Button -->
            <a href="{{ route('products.show', $post->slug) }}" 
            class="flex items-center gap-1 text-blue-600 hover:text-blue-800 transition-colors">
                <span>Read More</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        
    </div>
</article>
