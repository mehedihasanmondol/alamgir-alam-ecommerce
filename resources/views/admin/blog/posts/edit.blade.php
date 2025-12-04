@extends('layouts.admin')

@section('title', 'Edit Post')

@push('styles')
<style>
/* CKEditor Custom Styling */

/* Force list markers to display (override Tailwind reset) */
.ck-content ul,
.ck-content ol {
    margin-left: 20px;
}

</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- WordPress-style Top Bar -->
    <div class="bg-white border-b border-gray-200 -mx-4 -mt-6 px-4 py-3 mb-6 sticky top-16 z-10 shadow-sm">
        <div class="flex items-center justify-between max-w-7xl mx-auto">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.blog.posts.index') }}" 
                   class="text-gray-600 hover:text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Posts
                </a>
                <span class="text-gray-300">|</span>
                <h1 class="text-xl font-semibold text-gray-900">Edit Post</h1>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('products.show', $post->slug) }}" target="_blank"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View Post
                </a>
                <button type="button" onclick="saveDraft()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Save Draft
                </button>
                <button type="submit" form="post-form"
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Update
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto">

        <form id="post-form" action="{{ route('admin.blog.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Auto-save indicator -->
            <div id="autosave-indicator" class="hidden fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Draft saved
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title - WordPress Style -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 pb-0">
                            <input type="text" 
                                   name="title" 
                                   id="post-title"
                                   value="{{ old('title', $post->title) }}" 
                                   required
                                   class="w-full text-3xl font-bold border-none focus:outline-none focus:ring-0 placeholder-gray-400"
                                   placeholder="Add title"
                                   autocomplete="off">
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Permalink -->
                        <div class="px-6 py-3 border-t border-gray-100">
                            <div class="flex items-center text-sm">
                                <span class="text-gray-500 mr-2">Permalink:</span>
                                <span class="text-blue-600">{{ url('/') }}/</span>
                                <input type="text" 
                                       name="slug" 
                                       id="post-slug"
                                       value="{{ old('slug', $post->slug) }}"
                                       class="border-none focus:outline-none focus:ring-0 text-blue-600 px-1 py-0 min-w-[200px]"
                                       placeholder="auto-generated">
                                <button type="button" 
                                        onclick="editSlug()" 
                                        class="ml-2 text-blue-600 hover:text-blue-800 text-xs">
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>



                    <!-- Content Editor - CKEditor 5 -->
                    <div class="bg-white rounded-lg shadow p-6 prose prose-lg max-w-none">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Content *</label>
                        <textarea name="content" 
                                  id="ckeditor" 
                                  class="ckeditor-content">{{ old('content', $post->content) }}</textarea>
                        
                        <!-- Word Counter -->
                        <div class="char-counter" id="word-count"></div>
                        
                        @error('content')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Excerpt -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                        <textarea name="excerpt" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                  placeholder="Short description for listings (optional)">{{ old('excerpt', $post->excerpt) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from content</p>
                        @error('excerpt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SEO Section -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">SEO Settings</h3>
                            <button type="button" onclick="toggleSection('seo')" 
                                    class="text-sm text-blue-600 hover:text-blue-800">
                                Toggle
                            </button>
                        </div>
                        
                        <div id="seo-section" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                                <input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="SEO title (60 characters recommended)">
                                <p class="mt-1 text-xs text-gray-500">Leave empty to use post title</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                                <textarea name="meta_description" rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                          placeholder="SEO description (160 characters recommended)">{{ old('meta_description', $post->meta_description) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                                <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $post->meta_keywords) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="keyword1, keyword2, keyword3">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Publish Box -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Publish</h3>
                        
                        <div class="space-y-4">
                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                <select name="status" id="status-select" required 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="scheduled" {{ old('status', $post->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="unlisted" {{ old('status', $post->status) == 'unlisted' ? 'selected' : '' }}>Unlisted</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    <strong>Unlisted:</strong> Post is viewable via direct link but won't appear in any frontend lists
                                </p>
                            </div>

                            <!-- Schedule Date -->
                            <div id="scheduled-date" style="display: {{ old('status', $post->status) == 'scheduled' ? 'block' : 'none' }};">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Schedule Date</label>
                                <input type="datetime-local" name="scheduled_at" 
                                       value="{{ old('scheduled_at', $post->scheduled_at ? $post->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Published Date (if published) -->
                            @if($post->published_at)
                            <div class="text-sm text-gray-600">
                                <strong>Published:</strong><br>
                                {{ $post->published_at->format('M d, Y h:i A') }}
                            </div>
                            @endif

                            <!-- Post Stats -->
                            <div class="pt-4 border-t border-gray-200 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Views:</span>
                                    <span class="font-semibold">{{ number_format($post->views_count) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Reading Time:</span>
                                    <span class="font-semibold">{{ $post->reading_time_text }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Comments:</span>
                                    <span class="font-semibold">{{ $post->comments->count() }}</span>
                                </div>
                            </div>

                            <!-- Checkboxes -->
                            <div class="pt-4 border-t border-gray-200 space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_featured" value="1" 
                                           {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Featured Post</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" name="allow_comments" value="1" 
                                           {{ old('allow_comments', $post->allow_comments) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Allow Comments</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Quality Indicators (Dynamic Tick Marks - Expandable) -->
                    <div class="bg-white rounded-lg shadow" x-data="{ expanded: {{ $post->tickMarks->count() > 0 ? 'true' : 'false' }} }">
                        <!-- Expandable Header -->
                        <button type="button" @click="expanded = !expanded" 
                                class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-semibold text-gray-900">Quality Indicators</h3>
                                
                                <!-- Active Indicator Icons (shown when collapsed) -->
                                <div class="flex items-center gap-1" x-show="!expanded">
                                    @foreach($post->tickMarks as $tickMark)
                                        <span class="inline-flex items-center p-1 rounded" style="background-color: {{ $tickMark->bg_color }}; color: {{ $tickMark->text_color }};" title="{{ $tickMark->label }}">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                {!! $tickMark->getIconHtml() !!}
                                            </svg>
                                        </span>
                                    @endforeach
                                    @if($post->tickMarks->count() === 0)
                                        <span class="text-xs text-gray-400">None</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Expand/Collapse Icon -->
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <!-- Expandable Content -->
                        <div x-show="expanded" x-collapse class="px-6 pb-6 border-t border-gray-100">
                            <div class="pt-4">
                                <!-- Livewire Component for Dynamic Tick Mark Management -->
                                <livewire:admin.blog.post-tick-mark-manager :postId="$post->id" />
                            </div>
                        </div>
                        
                        <!-- Legacy checkboxes for backward compatibility (hidden, will be synced by Livewire) -->
                        <input type="hidden" name="is_verified" value="{{ old('is_verified', $post->is_verified) ? '1' : '0' }}">
                        <input type="hidden" name="is_editor_choice" value="{{ old('is_editor_choice', $post->is_editor_choice) ? '1' : '0' }}">
                        <input type="hidden" name="is_trending" value="{{ old('is_trending', $post->is_trending) ? '1' : '0' }}">
                        <input type="hidden" name="is_premium" value="{{ old('is_premium', $post->is_premium) ? '1' : '0' }}">
                    </div>

                    <!-- Featured Image -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Featured Image</h3>
                        
                        @livewire('admin.blog.post-image-handler', ['post' => $post])
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alt Text</label>
                            <input type="text" name="featured_image_alt" 
                                   value="{{ old('featured_image_alt', $post->featured_image_alt) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   placeholder="Image description for SEO">
                        </div>
                    </div>

                    <!-- YouTube Video -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">YouTube Video (Optional)</h3>
                        
                        <!-- Video Preview -->
                        <div id="youtube-preview" class="mb-4 {{ $post->youtube_url && $post->youtube_video_id ? '' : 'hidden' }}">
                            <p class="text-sm font-medium text-gray-700 mb-2">Video Preview:</p>
                            <div class="relative" style="padding-bottom: 56.25%; height: 0;">
                                <iframe 
                                    id="youtube-iframe"
                                    src="{{ $post->youtube_embed_url ?? '' }}" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    class="absolute top-0 left-0 w-full h-full rounded-lg">
                                </iframe>
                            </div>
                        </div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">YouTube URL</label>
                        <input type="url" 
                               name="youtube_url" 
                               id="youtube_url"
                               value="{{ old('youtube_url', $post->youtube_url) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="https://www.youtube.com/watch?v=..."
                               onchange="previewYouTubeVideo(this.value)"
                               onpaste="setTimeout(() => previewYouTubeVideo(this.value), 100)">
                        <p class="mt-2 text-xs text-gray-500">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Paste the full YouTube video URL. The video will be embedded in the post.
                        </p>
                    </div>

                    <!-- Blog Categories -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Blog Categories</h3>
                            <button type="button" 
                                    onclick="openCategoryModal()"
                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add New
                            </button>
                        </div>

                        <p class="text-sm text-gray-600 mb-3">Select one or more categories (hierarchical structure shown)</p>

                        <!-- Category Checkboxes -->
                        <div class="space-y-1 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3" id="category-list">
                            @forelse($categories as $category)
                                <label class="flex items-start hover:bg-gray-50 p-2 rounded cursor-pointer group">
                                    <input type="checkbox" 
                                           name="categories[]" 
                                           value="{{ $category->id }}"
                                           {{ in_array($category->id, old('categories', $post->categories->pluck('id')->toArray())) ? 'checked' : '' }}
                                           class="mt-0.5 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-2 flex-1">
                                        <span class="text-sm text-gray-700">{{ $category->dropdown_label ?? $category->name }}</span>
                                        @if($category->dropdown_path && $category->getDepthLevel() > 0)
                                            <div class="text-xs text-gray-400 mt-0.5">
                                                {{ $category->dropdown_path }}
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 text-center py-4">No categories available. Click "Add New" to create one.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Tags</h3>
                            <button type="button" 
                                    onclick="openTagModal()"
                                    class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add New
                            </button>
                        </div>

                        <p class="text-sm text-gray-600 mb-3">Select one or more tags</p>

                        <!-- Tag Checkboxes -->
                        <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3" id="tag-list">
                            @forelse($tags as $tag)
                                <label class="flex items-center hover:bg-gray-50 p-2 rounded cursor-pointer">
                                    <input type="checkbox" 
                                           name="tags[]" 
                                           value="{{ $tag->id }}"
                                           {{ in_array($tag->id, old('tags', $post->tags->pluck('id')->toArray())) ? 'checked' : '' }}
                                           class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 text-center py-4">No tags available. Click "Add New" to create one.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Shop This Article Products -->
                    <div class="bg-white rounded-lg shadow p-6" x-data="{ 
                        showProducts: {{ $post->products->count() > 0 ? 'true' : 'false' }},
                        filterProducts(query) {
                            const labels = document.querySelectorAll('#product-list-container-edit label');
                            labels.forEach(label => {
                                const productName = label.querySelector('span').textContent.toLowerCase();
                                if (productName.includes(query.toLowerCase())) {
                                    label.style.display = 'flex';
                                } else {
                                    label.style.display = 'none';
                                }
                            });
                        }
                    }">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                Shop This Article
                                @if($post->products->count() > 0)
                                    <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                        {{ $post->products->count() }} selected
                                    </span>
                                @endif
                            </h3>
                            <button type="button" 
                                    @click="showProducts = !showProducts"
                                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition flex items-center gap-1">
                                <span x-text="showProducts ? 'Hide' : 'Show'"></span>
                                <svg class="w-4 h-4 transform transition-transform" :class="showProducts ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>

                        <p class="text-sm text-gray-600 mb-3">Select products to feature in this article (optional). Products will appear in "Shop This Article" section.</p>

                        <!-- Search Box -->
                        <div x-show="showProducts" x-collapse class="mb-3">
                            <input type="text" 
                                   x-ref="productSearch"
                                   @input="filterProducts($event.target.value)"
                                   placeholder="Search products by name..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>

                        <!-- Product Selection -->
                        <div x-show="showProducts" x-collapse class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-3" id="product-list-container-edit">
                            @php
                                $attachedProductIds = old('products', $post->products->pluck('id')->toArray());
                            @endphp
                            @forelse($products as $product)
                                <label class="flex items-center hover:bg-gray-50 p-2 rounded cursor-pointer">
                                    <input type="checkbox" 
                                           name="products[]" 
                                           value="{{ $product->id }}"
                                           {{ in_array($product->id, $attachedProductIds) ? 'checked' : '' }}
                                           class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                    @if($product->getPrimaryThumbnailUrl())
                                        <img src="{{ $product->getPrimaryThumbnailUrl() }}" 
                                             alt="{{ $product->name }}"
                                             class="w-10 h-10 object-cover rounded ml-2">
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 rounded ml-2 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="ml-3 text-sm text-gray-700 flex-1">{{ $product->name }}</span>
                                    <span class="text-xs text-gray-500">ID: {{ $product->id }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 text-center py-4">No published products available.</p>
                            @endforelse
                        </div>

                        <p class="text-xs text-gray-500 mt-2">
                            <svg class="w-4 h-4 inline text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Tip: Products will be displayed in the order you select them. You can reorder by unchecking and rechecking.
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white rounded-lg shadow p-6 space-y-3">
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-150">
                            Update Post
                        </button>
                        
                        @if($post->status === 'draft')
                        <button type="button" onclick="publishNow()" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-150">
                            Publish Now
                        </button>
                        @endif

                        <a href="{{ route('admin.blog.posts.index') }}" 
                           class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-3 px-4 rounded-lg transition duration-150">
                            Cancel
                        </a>

                        <button type="button" onclick="deletePost()" 
                                class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-medium py-3 px-4 rounded-lg transition duration-150">
                            Delete Post
                        </button>
                    </div>

                    <!-- Last Updated -->
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <strong>Last Updated:</strong>
                        </div>
                        <p>{{ $post->updated_at->format('M d, Y h:i A') }}</p>
                        <p class="text-xs mt-1">{{ $post->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Universal Image Uploader (used for both CKEditor and Featured Image) -->
<livewire:universal-image-uploader />

<!-- Add New Category Modal - Modern Style -->
<div id="categoryModal" class="hidden fixed inset-0 overflow-y-auto" style="z-index: 9999;" x-data="{ show: false }">
    {{-- Background overlay with blur --}}
    <div class="fixed inset-0 transition-opacity" 
         style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
         onclick="closeCategoryModal()"></div>
    
    {{-- Modal container --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        {{-- Modal panel --}}
        <div class="relative rounded-lg shadow-xl max-w-lg w-full border border-gray-200"
             style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
            <div class="bg-white px-6 pt-5 pb-4 rounded-t-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Add New Category</h3>
                    <button type="button" 
                            onclick="closeCategoryModal()"
                            class="text-gray-400 hover:text-gray-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                        <input type="text" 
                               id="modal-category-name"
                               onkeydown="if(event.key === 'Enter') { event.preventDefault(); createCategoryFromModal(); } else if(event.key === 'Escape') { closeCategoryModal(); }"
                               autofocus
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter category name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="modal-category-description"
                                  rows="3"
                                  onkeydown="if(event.key === 'Escape') { closeCategoryModal(); }"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Brief description (optional)"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-3 flex items-center justify-end gap-3 rounded-b-lg">
                <button type="button" 
                        onclick="closeCategoryModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="button" 
                        onclick="createCategoryFromModal()"
                        id="create-category-btn"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors">
                    <span id="create-btn-text">Create Category</span>
                    <span id="create-btn-loading" class="hidden">Creating...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add New Tag Modal - Modern Style -->
<div id="tagModal" class="hidden fixed inset-0 overflow-y-auto" style="z-index: 9999;" x-data="{ show: false }">
    {{-- Background overlay with blur --}}
    <div class="fixed inset-0 transition-opacity" 
         style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
         onclick="closeTagModal()"></div>
    
    {{-- Modal container --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        {{-- Modal panel --}}
        <div class="relative rounded-lg shadow-xl max-w-lg w-full border border-gray-200"
             style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
            <div class="bg-white px-6 pt-5 pb-4 rounded-t-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Add New Tag</h3>
                    <button type="button" 
                            onclick="closeTagModal()"
                            class="text-gray-400 hover:text-gray-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tag Name *</label>
                        <input type="text" 
                               id="modal-tag-name"
                               onkeydown="if(event.key === 'Enter') { event.preventDefault(); createTagFromModal(); } else if(event.key === 'Escape') { closeTagModal(); }"
                               autofocus
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="Enter tag name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea id="modal-tag-description"
                                  rows="3"
                                  onkeydown="if(event.key === 'Escape') { closeTagModal(); }"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                  placeholder="Brief description (optional)"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-3 flex items-center justify-end gap-3 rounded-b-lg">
                <button type="button" 
                        onclick="closeTagModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="button" 
                        onclick="createTagFromModal()"
                        id="create-tag-btn"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-50 transition-colors">
                    <span id="create-tag-btn-text">Create Tag</span>
                    <span id="create-tag-btn-loading" class="hidden">Creating...</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@vite('resources/js/blog-post-editor.js')
<script>

// Status change handler
document.getElementById('status-select').addEventListener('change', function() {
    const scheduledDiv = document.getElementById('scheduled-date');
    scheduledDiv.style.display = this.value === 'scheduled' ? 'block' : 'none';
});

// Slug generator
function generateSlug() {
    const title = document.querySelector('input[name="title"]').value;
    const slug = title.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/--+/g, '-')
        .trim();
    document.querySelector('input[name="slug"]').value = slug;
    document.getElementById('slug-preview').textContent = slug;
}

// Update slug preview on input
document.querySelector('input[name="slug"]').addEventListener('input', function() {
    document.getElementById('slug-preview').textContent = this.value;
});

// Toggle sections
function toggleSection(section) {
    const element = document.getElementById(section + '-section');
    element.style.display = element.style.display === 'none' ? 'block' : 'none';
}

// Publish now
function publishNow() {
    if (confirm('Publish this post now?')) {
        document.getElementById('status-select').value = 'published';
        document.querySelector('form').submit();
    }
}

// Form validation is handled by blog-post-editor.js

// Delete post
function deletePost() {
    if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
        fetch('{{ route('admin.blog.posts.destroy', $post->id) }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route('admin.blog.posts.index') }}';
            }
        });
    }
}

// Modal Functions
function openCategoryModal() {
    const modal = document.getElementById('categoryModal');
    modal.classList.remove('hidden');
    // Focus on input after modal opens
    setTimeout(() => {
        document.getElementById('modal-category-name').focus();
    }, 100);
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
    document.getElementById('modal-category-name').value = '';
    document.getElementById('modal-category-description').value = '';
    // Reset button state
    const createBtn = document.getElementById('create-category-btn');
    createBtn.disabled = false;
    document.getElementById('create-btn-text').classList.remove('hidden');
    document.getElementById('create-btn-loading').classList.add('hidden');
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('categoryModal');
        if (!modal.classList.contains('hidden')) {
            closeCategoryModal();
        }
    }
});

// Create Category from Modal
function createCategoryFromModal() {
    const name = document.getElementById('modal-category-name').value.trim();
    const description = document.getElementById('modal-category-description').value.trim();
    
    if (!name) {
        alert('Please enter a category name');
        return;
    }
    
    // Show loading state
    const createBtn = document.getElementById('create-category-btn');
    createBtn.disabled = true;
    document.getElementById('create-btn-text').classList.add('hidden');
    document.getElementById('create-btn-loading').classList.remove('hidden');
    
    // Send AJAX request to create category
    fetch('{{ route('admin.blog.categories.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            name: name,
            description: description,
            is_active: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new category to checkbox list
            const categoryList = document.getElementById('category-list');
            
            // Remove "no categories" message if exists
            const emptyMessage = categoryList.querySelector('p');
            if (emptyMessage) {
                emptyMessage.remove();
            }
            
            // Create new checkbox
            const label = document.createElement('label');
            label.className = 'flex items-center hover:bg-gray-50 p-2 rounded cursor-pointer';
            label.innerHTML = `
                <input type="checkbox" 
                       name="categories[]" 
                       value="${data.category.id}"
                       checked
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">${data.category.name}</span>
            `;
            categoryList.appendChild(label);
            
            // Close modal
            closeCategoryModal();
            
            // Show success message
            showNotification('Category created successfully!', 'success');
        } else {
            alert(data.message || 'Failed to create category');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the category');
    })
    .finally(() => {
        createBtn.disabled = false;
        document.getElementById('create-btn-text').classList.remove('hidden');
        document.getElementById('create-btn-loading').classList.add('hidden');
    });
}

// Show notification function
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white font-medium transform transition-all duration-300`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// ============================================
// Featured Image Preview Functions
// ============================================

function previewFeaturedImage(event) {
    const file = event.target.files[0];
    
    if (file) {
        // Validate file size (2MB)
        const maxSize = 2 * 1024 * 1024; // 2MB in bytes
        if (file.size > maxSize) {
            alert('File size exceeds 2MB. Please choose a smaller image.');
            event.target.value = ''; // Clear the input
            return;
        }
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert('Please select a valid image file (JPG, PNG, WebP, or GIF).');
            event.target.value = ''; // Clear the input
            return;
        }
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('new-image-preview');
            const container = document.getElementById('new-image-preview-container');
            
            preview.src = e.target.result;
            container.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function removeNewImagePreview() {
    const preview = document.getElementById('new-image-preview');
    const container = document.getElementById('new-image-preview-container');
    const input = document.getElementById('featured-image-input');
    
    // Clear preview
    preview.src = '';
    container.classList.add('hidden');
    
    // Clear file input
    input.value = '';
}

// ============================================
// Tag Modal Functions
// ============================================

function openTagModal() {
    const modal = document.getElementById('tagModal');
    modal.classList.remove('hidden');
    // Focus on input after modal opens
    setTimeout(() => {
        document.getElementById('modal-tag-name').focus();
    }, 100);
}

function closeTagModal() {
    document.getElementById('tagModal').classList.add('hidden');
    document.getElementById('modal-tag-name').value = '';
    document.getElementById('modal-tag-description').value = '';
    // Reset button state
    const createBtn = document.getElementById('create-tag-btn');
    createBtn.disabled = false;
    document.getElementById('create-tag-btn-text').classList.remove('hidden');
    document.getElementById('create-tag-btn-loading').classList.add('hidden');
}

// Create Tag from Modal
function createTagFromModal() {
    const name = document.getElementById('modal-tag-name').value.trim();
    const description = document.getElementById('modal-tag-description').value.trim();
    
    if (!name) {
        alert('Please enter a tag name');
        return;
    }
    
    // Show loading state
    const createBtn = document.getElementById('create-tag-btn');
    createBtn.disabled = true;
    document.getElementById('create-tag-btn-text').classList.add('hidden');
    document.getElementById('create-tag-btn-loading').classList.remove('hidden');
    
    // Send AJAX request to create tag
    fetch('{{ route('admin.blog.tags.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            name: name,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new tag to checkbox list
            const tagList = document.getElementById('tag-list');
            
            // Remove "no tags" message if exists
            const emptyMessage = tagList.querySelector('p');
            if (emptyMessage) {
                emptyMessage.remove();
            }
            
            // Create new checkbox
            const label = document.createElement('label');
            label.className = 'flex items-center hover:bg-gray-50 p-2 rounded cursor-pointer';
            label.innerHTML = `
                <input type="checkbox" 
                       name="tags[]" 
                       value="${data.tag.id}"
                       checked
                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                <span class="ml-2 text-sm text-gray-700">${data.tag.name}</span>
            `;
            tagList.appendChild(label);
            
            // Close modal
            closeTagModal();
            
            // Show success message
            showNotification('Tag created successfully!', 'success');
        } else {
            alert(data.message || 'Failed to create tag');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the tag');
    })
    .finally(() => {
        createBtn.disabled = false;
        document.getElementById('create-tag-btn-text').classList.remove('hidden');
        document.getElementById('create-tag-btn-loading').classList.add('hidden');
    });
}

function previewYouTubeVideo(url) {
    const previewContainer = document.getElementById('youtube-preview');
    const iframe = document.getElementById('youtube-iframe');
    
    if (!url) {
        previewContainer.classList.add('hidden');
        iframe.src = '';
        return;
    }

    // Extract video ID from various YouTube URL formats
    const patterns = [
        /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/,
        /youtube\.com\/watch\?.*v=([^&\n?#]+)/
    ];

    let videoId = null;
    for (const pattern of patterns) {
        const match = url.match(pattern);
        if (match && match[1]) {
            videoId = match[1];
            break;
        }
    }

    if (videoId) {
        iframe.src = `https://www.youtube.com/embed/${videoId}`;
        previewContainer.classList.remove('hidden');
    } else {
        previewContainer.classList.add('hidden');
        iframe.src = '';
    }
}
</script>
@endpush
@endsection
