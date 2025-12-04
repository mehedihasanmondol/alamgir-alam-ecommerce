@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $brand->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">Brand Details</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.brands.edit', $brand) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('admin.brands.index') }}" 
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
                    <!-- Logo -->
                    @if($brand->media || $brand->logo)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-2">Logo</dt>
                        <dd>
                            <img src="{{ $brand->getMediumLogoUrl() }}" 
                                 alt="{{ $brand->name }}"
                                 class="h-32 w-32 object-contain rounded-lg border border-gray-300 bg-gray-50 p-2">
                        </dd>
                    </div>
                    @endif

                    <!-- Name -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $brand->name }}</dd>
                    </div>

                    <!-- Slug -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Slug</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $brand->slug }}</dd>
                    </div>

                    <!-- Description -->
                    @if($brand->description)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $brand->description }}</dd>
                    </div>
                    @endif

                    <!-- Sort Order -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $brand->sort_order }}</dd>
                    </div>

                    <!-- Status -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($brand->is_active)
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

                    <!-- Featured -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Featured</dt>
                        <dd class="mt-1">
                            @if($brand->is_featured)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Not Featured
                                </span>
                            @endif
                        </dd>
                    </div>

                    <!-- Created At -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $brand->created_at->format('M d, Y h:i A') }}</dd>
                    </div>

                    <!-- Updated At -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $brand->updated_at->format('M d, Y h:i A') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Contact Information -->
        @if($brand->website || $brand->email || $brand->phone)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-address-book mr-2 text-blue-600"></i>
                    Contact Information
                </h2>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 gap-4">
                    <!-- Website -->
                    @if($brand->website)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Website</dt>
                        <dd class="mt-1 text-sm">
                            <a href="{{ $brand->getWebsiteUrl() }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                <i class="fas fa-globe mr-2"></i>
                                {{ $brand->website }}
                                <i class="fas fa-external-link-alt ml-2 text-xs"></i>
                            </a>
                        </dd>
                    </div>
                    @endif

                    <!-- Email -->
                    @if($brand->email)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm">
                            <a href="mailto:{{ $brand->email }}" class="text-blue-600 hover:underline flex items-center">
                                <i class="fas fa-envelope mr-2"></i>
                                {{ $brand->email }}
                            </a>
                        </dd>
                    </div>
                    @endif

                    <!-- Phone -->
                    @if($brand->phone)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900 flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            {{ $brand->phone }}
                        </dd>
                    </div>
                    @endif
                </dl>
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
                            {{ $brand->meta_title ?: $brand->name }}
                        </dd>
                    </div>

                    <!-- Meta Description -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Meta Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $brand->meta_description ?: 'Not set' }}
                        </dd>
                    </div>

                    <!-- Meta Keywords -->
                    @if($brand->meta_keywords)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Meta Keywords</dt>
                        <dd class="mt-1">
                            @foreach(explode(',', $brand->meta_keywords) as $keyword)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-2 mb-2">
                                    {{ trim($keyword) }}
                                </span>
                            @endforeach
                        </dd>
                    </div>
                    @endif

                    <!-- Canonical URL -->
                    @if($brand->canonical_url)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Canonical URL</dt>
                        <dd class="mt-1 text-sm text-blue-600 break-all">
                            <a href="{{ $brand->canonical_url }}" target="_blank" class="hover:underline">
                                {{ $brand->canonical_url }}
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
                            {{ $brand->og_title ?: $brand->meta_title ?: $brand->name }}
                        </dd>
                    </div>

                    <!-- OG Description -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">OG Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $brand->og_description ?: $brand->meta_description ?: 'Not set' }}
                        </dd>
                    </div>

                    <!-- OG Image -->
                    @if($brand->og_image)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-2">OG Image</dt>
                        <dd>
                            <img src="{{ $brand->og_image }}" 
                                 alt="OG Image"
                                 class="h-32 w-auto rounded-lg border border-gray-300">
                            <p class="mt-2 text-xs text-gray-500 break-all">{{ $brand->og_image }}</p>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
