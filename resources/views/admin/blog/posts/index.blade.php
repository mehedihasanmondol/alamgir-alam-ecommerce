@extends('layouts.admin')

@section('title', 'Blog Posts')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Blog Posts</h1>
            <p class="text-gray-600 mt-1">Manage your blog posts</p>
        </div>
        <div class="flex gap-3">
            <button id="bulk-delete-btn" 
                    onclick="confirmBulkDelete()" 
                    class="hidden bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Selected (<span id="selected-count">0</span>)
            </button>
            <a href="{{ route('admin.blog.posts.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Post
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Posts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $counts['all'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Published</p>
                    <p class="text-2xl font-bold text-green-600">{{ $counts['published'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Drafts</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $counts['draft'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Scheduled</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $counts['scheduled'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Unlisted</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $counts['unlisted'] }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-4">
            <form method="GET" action="{{ route('admin.blog.posts.index') }}" id="filter-form">
                <div class="flex items-center gap-4">
                    {{-- Search --}}
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   id="search-input"
                                   value="{{ request('search') }}"
                                   placeholder="Search posts by title, content..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            {{-- Loading indicator --}}
                            <div id="search-loading" class="hidden absolute right-3 top-2.5">
                                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Toggle --}}
                    <button type="button" 
                            onclick="toggleFilters()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filters
                        @if(request()->hasAny(['status', 'category_id', 'author_id', 'date_from', 'date_to', 'featured']))
                        <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-600 rounded-full">Active</span>
                        @endif
                    </button>
                </div>

                {{-- Advanced Filters --}}
                <div id="advanced-filters" class="{{ request()->hasAny(['status', 'category_id', 'author_id', 'date_from', 'date_to', 'featured']) ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-200">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" 
                                onchange="submitFilterForm()"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="unlisted" {{ request('status') == 'unlisted' ? 'selected' : '' }}>Unlisted</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id" 
                                onchange="submitFilterForm()"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            @foreach(\App\Modules\Blog\Models\BlogCategory::active()->ordered()->get() as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                        <select name="author_id" 
                                onchange="submitFilterForm()"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">All Authors</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $user)
                            <option value="{{ $user->id }}" {{ request('author_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Featured</label>
                        <select name="featured" 
                                onchange="submitFilterForm()"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">All Posts</option>
                            <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Featured Only</option>
                            <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Non-Featured</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" 
                               name="date_from" 
                               value="{{ request('date_from') }}"
                               onchange="submitFilterForm()"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" 
                               name="date_to" 
                               value="{{ request('date_to') }}"
                               onchange="submitFilterForm()"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-2 flex items-end">
                        <a href="{{ route('admin.blog.posts.index') }}" 
                           class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            Clear all filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Posts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden relative">
        <!-- Loading Overlay -->
        <div id="table-loading" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
            <div class="text-center">
                <svg class="animate-spin h-10 w-10 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-600">Loading posts...</p>
            </div>
        </div>

        <div id="posts-table-container">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" 
                                   id="select-all" 
                                   onchange="toggleSelectAll(this)" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Post</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($posts as $post)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <input type="checkbox" 
                               class="post-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                               value="{{ $post->id }}" 
                               onchange="updateBulkDeleteButton()">
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-12 h-12 rounded object-cover mr-3">
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($post->title, 50) }}</div>
                                <div class="text-sm text-gray-500">{{ $post->reading_time_text }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex justify-center">
                            @if($post->author->avatar)
                                <img src="{{ asset('storage/' . $post->author->avatar) }}" 
                                     alt="{{ $post->author->name }}"
                                     title="{{ $post->author->name }}"
                                     class="h-8 w-8 rounded-full object-cover">
                            @else
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center"
                                     title="{{ $post->author->name }}">
                                    <span class="text-xs font-medium text-gray-600">
                                        {{ strtoupper(substr($post->author->name, 0, 2)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            @if($post->categories && $post->categories->count() > 0)
                                @foreach($post->categories as $category)
                                    <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded mr-1 mb-1">{{ $category->name }}</span>
                                @endforeach
                            @else
                                <span class="text-gray-400">Uncategorized</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($post->status === 'published')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Published
                            </span>
                        @elseif($post->status === 'draft')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Draft
                            </span>
                        @elseif($post->status === 'scheduled')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                Scheduled
                            </span>
                        @elseif($post->status === 'unlisted')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                Unlisted
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($post->status) }}
                            </span>
                        @endif
                        <div class="mt-2">
                            <button onclick="toggleFeatured({{ $post->id }}, this)" 
                                    class="inline-flex items-center transition-colors {{ $post->is_featured ? 'text-yellow-500' : 'text-gray-400' }} hover:text-yellow-600"
                                    title="{{ $post->is_featured ? 'Remove from Featured' : 'Mark as Featured' }}"
                                    data-post-id="{{ $post->id }}"
                                    data-is-featured="{{ $post->is_featured ? '1' : '0' }}">
                                <svg class="w-5 h-5" fill="{{ $post->is_featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                @if($post->is_featured)
                                <span class="ml-1 text-xs font-medium">Featured</span>
                                @endif
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($post->views_count) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $post->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.blog.posts.edit', $post->id) }}" 
                           class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        <button onclick="deletePost({{ $post->id }})" 
                                class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="mt-2 text-sm">No posts found</p>
                        <a href="{{ route('admin.blog.posts.create') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                            Create your first post
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($posts->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $posts->links() }}
        </div>
        @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle filters visibility
function toggleFilters() {
    const filtersDiv = document.getElementById('advanced-filters');
    filtersDiv.classList.toggle('hidden');
}

// Submit filter form with AJAX (background update)
function submitFilterForm() {
    const form = document.getElementById('filter-form');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    // Show loading overlay
    document.getElementById('table-loading').classList.remove('hidden');
    
    // Fetch filtered results
    fetch(form.action + '?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        }
    })
    .then(response => response.text())
    .then(html => {
        // Parse the HTML response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Extract the table container content
        const newTableContent = doc.querySelector('#posts-table-container');
        
        if (newTableContent) {
            // Update the table
            document.getElementById('posts-table-container').innerHTML = newTableContent.innerHTML;
            
            // Update URL without page reload
            const newUrl = form.action + '?' + params.toString();
            window.history.pushState({}, '', newUrl);
        }
        
        // Hide loading overlay
        document.getElementById('table-loading').classList.add('hidden');
        
        // Hide search loading indicator
        document.getElementById('search-loading').classList.add('hidden');
    })
    .catch(error => {
        console.error('Error:', error);
        // Hide loading overlay on error
        document.getElementById('table-loading').classList.add('hidden');
        document.getElementById('search-loading').classList.add('hidden');
    });
}

// Search with debounce (300ms delay)
let searchTimeout;
const searchInput = document.getElementById('search-input');
const searchLoading = document.getElementById('search-loading');

searchInput.addEventListener('input', function() {
    // Show loading indicator
    searchLoading.classList.remove('hidden');
    
    // Clear previous timeout
    clearTimeout(searchTimeout);
    
    // Set new timeout
    searchTimeout = setTimeout(() => {
        submitFilterForm();
    }, 300); // 300ms debounce
});

// Handle pagination clicks
document.addEventListener('click', function(e) {
    // Check if clicked element is a pagination link
    if (e.target.closest('.pagination a')) {
        e.preventDefault();
        const link = e.target.closest('.pagination a');
        const url = link.href;
        
        // Show loading
        document.getElementById('table-loading').classList.remove('hidden');
        
        // Fetch page
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTableContent = doc.querySelector('#posts-table-container');
            
            if (newTableContent) {
                document.getElementById('posts-table-container').innerHTML = newTableContent.innerHTML;
                window.history.pushState({}, '', url);
                
                // Scroll to top of table
                document.getElementById('posts-table-container').scrollIntoView({ behavior: 'smooth' });
            }
            
            document.getElementById('table-loading').classList.add('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('table-loading').classList.add('hidden');
        });
    }
});

// Toggle featured status
function toggleFeatured(postId, button) {
    // Show loading state
    button.style.opacity = '0.5';
    button.disabled = true;
    
    fetch(`/admin/blog/posts/${postId}/toggle-featured`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Toggle the visual state
            const isFeatured = data.is_featured;
            const svg = button.querySelector('svg');
            
            if (isFeatured) {
                button.classList.remove('text-gray-300');
                button.classList.add('text-yellow-500');
                svg.setAttribute('fill', 'currentColor');
                button.title = 'Remove from Featured';
            } else {
                button.classList.remove('text-yellow-500');
                button.classList.add('text-gray-300');
                svg.setAttribute('fill', 'none');
                button.title = 'Mark as Featured';
            }
            
            button.dataset.isFeatured = isFeatured ? '1' : '0';
        }
        
        // Remove loading state
        button.style.opacity = '1';
        button.disabled = false;
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update featured status');
        button.style.opacity = '1';
        button.disabled = false;
    });
}

// Delete post function
function deletePost(postId) {
    if (confirm('Are you sure you want to delete this post?')) {
        fetch(`/admin/blog/posts/${postId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload table content instead of full page
                submitFilterForm();
            }
        });
    }
}

// Toggle select all checkboxes
function toggleSelectAll(checkbox) {
    const postCheckboxes = document.querySelectorAll('.post-checkbox');
    postCheckboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateBulkDeleteButton();
}

// Update bulk delete button visibility and count
function updateBulkDeleteButton() {
    const selectedCheckboxes = document.querySelectorAll('.post-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const selectedCount = document.getElementById('selected-count');
    const selectAllCheckbox = document.getElementById('select-all');
    
    if (selectedCheckboxes.length > 0) {
        bulkDeleteBtn.classList.remove('hidden');
        selectedCount.textContent = selectedCheckboxes.length;
    } else {
        bulkDeleteBtn.classList.add('hidden');
    }
    
    // Update select all checkbox state
    const allCheckboxes = document.querySelectorAll('.post-checkbox');
    selectAllCheckbox.checked = allCheckboxes.length > 0 && selectedCheckboxes.length === allCheckboxes.length;
}

// Confirm bulk delete
function confirmBulkDelete() {
    const selectedCheckboxes = document.querySelectorAll('.post-checkbox:checked');
    const postIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    if (postIds.length === 0) {
        alert('Please select at least one post to delete');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${postIds.length} post(s)? This action cannot be undone.`)) {
        // Show loading
        document.getElementById('table-loading').classList.remove('hidden');
        
        fetch('{{ route('admin.blog.posts.bulk-delete') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ post_ids: postIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Uncheck select all
                document.getElementById('select-all').checked = false;
                // Reload table
                submitFilterForm();
                // Show success message
                alert(data.message);
            } else {
                alert('Error: ' + data.message);
                document.getElementById('table-loading').classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting posts');
            document.getElementById('table-loading').classList.add('hidden');
        });
    }
}
</script>
@endpush
@endsection
