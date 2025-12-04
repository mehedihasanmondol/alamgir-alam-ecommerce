@extends('layouts.admin')

@section('title', 'Add New Post')

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
                <h1 class="text-xl font-semibold text-gray-900">Add New Post</h1>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="previewPost()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Preview
                </button>
                <button type="button" onclick="saveDraft()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Save Draft
                </button>
                <button type="submit" form="post-form"
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Publish
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto">

        <form id="post-form" action="{{ route('admin.blog.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
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
                                   value="{{ old('title') }}" 
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
                                       value="{{ old('slug') }}"
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
                    <div class="bg-white rounded-lg shadow p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Content *</label>
                        <textarea name="content" 
                                  id="ckeditor" 
                                  class="ckeditor-content">{{ old('content') }}</textarea>
                        
                        <!-- Word Counter -->
                        <div class="char-counter" id="word-count"></div>
                        
                        @error('content')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Excerpt - Collapsible -->
                    <div class="bg-white rounded-lg shadow" x-data="{ open: false }">
                        <button type="button" 
                                @click="open = !open"
                                class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-gray-50">
                            <span class="font-medium text-gray-900">Excerpt</span>
                            <svg class="w-5 h-5 text-gray-400 transform transition-transform" 
                                 :class="{ 'rotate-180': open }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" 
                             x-transition
                             class="px-6 pb-6 border-t border-gray-100">
                            <textarea name="excerpt" 
                                      rows="3"
                                      class="w-full mt-4 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="Write a short excerpt or leave empty to auto-generate...">{{ old('excerpt') }}</textarea>
                            <p class="mt-2 text-xs text-gray-500">Excerpts are optional hand-crafted summaries of your content.</p>
                        </div>
                    </div>

                    <!-- SEO Section -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">SEO Settings</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                                <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                                <textarea name="meta_description" rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('meta_description') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                                <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="keyword1, keyword2, keyword3">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Publish -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Publish</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="unlisted" {{ old('status') == 'unlisted' ? 'selected' : '' }}>Unlisted</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    <strong>Unlisted:</strong> Post is viewable via direct link but won't appear in any frontend lists
                                </p>
                            </div>

                            <div id="scheduled-date" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Schedule Date</label>
                                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label class="ml-2 text-sm text-gray-700">Featured Post</label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="allow_comments" value="1" {{ old('allow_comments', true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label class="ml-2 text-sm text-gray-700">Allow Comments</label>
                            </div>
                        </div>
                    </div>

                    <!-- Tick Marks -->
                    <div class="bg-white rounded-lg shadow" x-data="{ expanded: false }">
                        <!-- Expandable Header -->
                        <button type="button" @click="expanded = !expanded" 
                                class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-semibold text-gray-900">Quality Indicators</h3>
                                <span class="text-xs text-gray-400">(Optional)</span>
                            </div>
                            
                            <!-- Expand/Collapse Icon -->
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <!-- Expandable Content -->
                        <div x-show="expanded" x-collapse class="px-6 pb-6 border-t border-gray-100">
                        <div class="space-y-3 pt-4">
                            <div class="flex items-start">
                                <input type="checkbox" name="is_verified" value="1" {{ old('is_verified') ? 'checked' : '' }}
                                       id="is_verified" class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="ml-3">
                                    <label for="is_verified" class="text-sm font-medium text-gray-700 flex items-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Verified
                                        </span>
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">Mark this post as fact-checked and verified</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <input type="checkbox" name="is_editor_choice" value="1" {{ old('is_editor_choice') ? 'checked' : '' }}
                                       id="is_editor_choice" class="mt-1 w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <div class="ml-3">
                                    <label for="is_editor_choice" class="text-sm font-medium text-gray-700 flex items-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-2">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            Editor's Choice
                                        </span>
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">Feature this post as editor's pick</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <input type="checkbox" name="is_trending" value="1" {{ old('is_trending') ? 'checked' : '' }}
                                       id="is_trending" class="mt-1 w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <div class="ml-3">
                                    <label for="is_trending" class="text-sm font-medium text-gray-700 flex items-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                                            </svg>
                                            Trending
                                        </span>
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">Mark as currently trending content</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <input type="checkbox" name="is_premium" value="1" {{ old('is_premium') ? 'checked' : '' }}
                                       id="is_premium" class="mt-1 w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                                <div class="ml-3">
                                    <label for="is_premium" class="text-sm font-medium text-gray-700 flex items-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-2">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Premium
                                        </span>
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">Mark as premium/exclusive content</p>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Featured Image</h3>
                        
                        @livewire('admin.blog.post-image-handler')
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alt Text</label>
                            <input type="text" name="featured_image_alt" value="{{ old('featured_image_alt') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   placeholder="Image description for SEO">
                        </div>
                    </div>

                    <!-- YouTube Video -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">YouTube Video (Optional)</h3>
                        
                        <!-- Video Preview -->
                        <div id="youtube-preview" class="mb-4 hidden">
                            <p class="text-sm font-medium text-gray-700 mb-2">Video Preview:</p>
                            <div class="relative" style="padding-bottom: 56.25%; height: 0;">
                                <iframe 
                                    id="youtube-iframe"
                                    src="" 
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
                               value="{{ old('youtube_url') }}"
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
                                           {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
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
                                           {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
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
                        showProducts: false,
                        filterProducts(query) {
                            const labels = document.querySelectorAll('#product-list-container label');
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
                        <div x-show="showProducts" x-collapse class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-3" id="product-list-container">
                            @forelse($products as $product)
                                <label class="flex items-center hover:bg-gray-50 p-2 rounded cursor-pointer">
                                    <input type="checkbox" 
                                           name="products[]" 
                                           value="{{ $product->id }}"
                                           {{ in_array($product->id, old('products', [])) ? 'checked' : '' }}
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
                    <div class="bg-white rounded-lg shadow p-6">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-150">
                            Create Post
                        </button>
                        <a href="{{ route('admin.blog.posts.index') }}" class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-3 px-4 rounded-lg mt-2 transition duration-150">
                            Cancel
                        </a>
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
        
        const preview = document.getElementById('image-preview');
        const previewContainer = document.getElementById('image-preview-container');
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
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

function removeImagePreview() {
    const preview = document.getElementById('image-preview');
    const container = document.getElementById('image-preview-container');
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
</script>
@endpush
@endsection
