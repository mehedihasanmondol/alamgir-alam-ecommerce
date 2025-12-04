@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">Category Details</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.categories.edit', $category) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('admin.categories.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 gap-4">
                    <!-- Image -->
                    @if($category->media)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-2">Image</dt>
                        <dd>
                            <img src="{{ $category->getMediumImageUrl() }}" 
                                 alt="{{ $category->name }}"
                                 class="h-48 w-48 object-cover rounded-lg border border-gray-300">
                        </dd>
                    </div>
                    @endif

                    <!-- Name -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $category->name }}</dd>
                    </div>

                    <!-- Slug -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Slug</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $category->slug }}</dd>
                    </div>

                    <!-- Parent Category -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Parent Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($category->parent)
                                <a href="{{ route('admin.categories.show', $category->parent) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    {{ $category->parent->name }}
                                </a>
                            @else
                                <span class="text-gray-400">None (Root Category)</span>
                            @endif
                        </dd>
                    </div>

                    <!-- Full Path -->
                    @if($category->ancestors()->count() > 0)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full Path</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $category->getFullPath() }}</dd>
                    </div>
                    @endif

                    <!-- Description -->
                    @if($category->description)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $category->description }}</dd>
                    </div>
                    @endif

                    <!-- Sort Order -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $category->sort_order }}</dd>
                    </div>

                    <!-- Status -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($category->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </dd>
                    </div>

                    <!-- Created At -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $category->created_at->format('M d, Y h:i A') }}</dd>
                    </div>

                    <!-- Updated At -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $category->updated_at->format('M d, Y h:i A') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Child Categories -->
        @if($category->children->isNotEmpty())
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    Child Categories ({{ $category->children->count() }})
                </h2>
            </div>
            <div class="px-6 py-4">
                <ul class="divide-y divide-gray-200">
                    @foreach($category->children as $child)
                    <li class="py-3 flex items-center justify-between">
                        <div class="flex items-center">
                            @if($child->image)
                                <img src="{{ asset('storage/' . $child->image) }}" 
                                     alt="{{ $child->name }}"
                                     class="h-10 w-10 rounded object-cover mr-3">
                            @else
                                <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center mr-3">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $child->name }}</p>
                                <p class="text-xs text-gray-500">{{ $child->slug }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.categories.show', $child) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            View <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- SEO Information -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-search mr-2 text-blue-600"></i>
                    SEO Configuration
                </h2>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 gap-4">
                    <!-- Meta Title -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Meta Title</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $category->meta_title ?: $category->name }}
                        </dd>
                    </div>

                    <!-- Meta Description -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Meta Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $category->meta_description ?: 'Not set' }}
                        </dd>
                    </div>

                    <!-- Meta Keywords -->
                    @if($category->meta_keywords)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Meta Keywords</dt>
                        <dd class="mt-1">
                            @foreach(explode(',', $category->meta_keywords) as $keyword)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-2 mb-2">
                                    {{ trim($keyword) }}
                                </span>
                            @endforeach
                        </dd>
                    </div>
                    @endif

                    <!-- Canonical URL -->
                    @if($category->canonical_url)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Canonical URL</dt>
                        <dd class="mt-1 text-sm text-blue-600 break-all">
                            <a href="{{ $category->canonical_url }}" target="_blank" class="hover:underline">
                                {{ $category->canonical_url }}
                                <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                            </a>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Open Graph Information -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fab fa-facebook mr-2 text-blue-600"></i>
                    Open Graph (Social Media)
                </h2>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 gap-4">
                    <!-- OG Title -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">OG Title</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $category->og_title ?: $category->meta_title ?: $category->name }}
                        </dd>
                    </div>

                    <!-- OG Description -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">OG Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $category->og_description ?: $category->meta_description ?: 'Not set' }}
                        </dd>
                    </div>

                    <!-- OG Image -->
                    @if($category->og_image)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-2">OG Image</dt>
                        <dd>
                            <img src="{{ $category->og_image }}" 
                                 alt="OG Image"
                                 class="h-32 w-auto rounded-lg border border-gray-300">
                            <p class="mt-2 text-xs text-gray-500 break-all">{{ $category->og_image }}</p>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
