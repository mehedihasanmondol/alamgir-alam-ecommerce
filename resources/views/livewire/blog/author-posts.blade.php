<div>
    <!-- Filter & Sort Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-4 border-t border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
            </svg>
            <span>Articles <span class="text-gray-500 font-normal">({{ number_format($totalPosts) }})</span></span>
        </h2>
        
        <!-- Sorting Dropdown -->
        <div class="flex items-center gap-2">
            <label for="sort" class="text-sm font-medium text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                </svg>
                Sort:
            </label>
            <select wire:model.live="sort"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white cursor-pointer">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="most_viewed">Most Viewed</option>
                <option value="most_popular">Most Popular</option>
            </select>
        </div>
    </div>

    @if($posts->count() > 0)
        <!-- Masonry Posts Grid -->
        <div class="masonry-grid bg-gray-50 pt-6 pb-4 ">
            @foreach($posts as $post)
                <x-blog.post-card :post="$post" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Articles Yet</h3>
            <p class="text-gray-600">No articles have been published yet. Check back soon!</p>
        </div>
    @endif
</div>

@push('styles')
<style>
    /* Masonry Layout using CSS Columns */
    .masonry-grid {
        column-count: 1;
        column-gap: 1.5rem;
    }
    
    @media (min-width: 768px) {
        .masonry-grid {
            column-count: 2;
        }
    }
    
    @media (min-width: 1024px) {
        .masonry-grid {
            column-count: 3;
        }
    }
    
    /* Prevent items from breaking across columns */
    .masonry-grid > * {
        break-inside: avoid;
        margin-bottom: 1.5rem;
        display: inline-block;
        width: 100%;
    }
</style>
@endpush

