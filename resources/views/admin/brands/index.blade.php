@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Brands</h1>
                <p class="mt-1 text-sm text-gray-600">Manage product brands with SEO configuration</p>
            </div>
            <a href="{{ route('admin.brands.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Add Brand
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-copyright text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['active'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle text-2xl text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Inactive</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['inactive'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-star text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Featured</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['featured'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-4">
            <div class="flex items-center gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <form method="GET" action="{{ route('admin.brands.index') }}" id="searchForm">
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $filters['search'] ?? '' }}"
                                   placeholder="Search brands..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <!-- Hidden inputs to preserve filters -->
                            <input type="hidden" name="is_active" value="{{ $filters['is_active'] ?? '' }}">
                            <input type="hidden" name="is_featured" value="{{ $filters['is_featured'] ?? '' }}">
                            <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
                        </div>
                    </form>
                </div>

                <!-- Filter Toggle -->
                <button onclick="toggleFilters()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filters
                </button>
            </div>

            <!-- Advanced Filters -->
            <div id="advancedFilters" class="hidden">
                <form method="GET" action="{{ route('admin.brands.index') }}" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-200">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Active Status</label>
                            <select name="is_active" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                <option value="">All</option>
                                <option value="1" {{ ($filters['is_active'] ?? '') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ ($filters['is_active'] ?? '') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Featured Status</label>
                            <select name="is_featured" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                <option value="">All</option>
                                <option value="1" {{ ($filters['is_featured'] ?? '') === '1' ? 'selected' : '' }}>Featured</option>
                                <option value="0" {{ ($filters['is_featured'] ?? '') === '0' ? 'selected' : '' }}>Not Featured</option>
                            </select>
                        </div>

                        <!-- Hidden inputs to preserve search and per_page -->
                        <input type="hidden" name="search" value="{{ $filters['search'] ?? '' }}">
                        <input type="hidden" name="per_page" id="perPageInput" value="{{ request('per_page', 15) }}">

                        <div class="md:col-span-4">
                            <button type="submit" class="text-sm text-blue-600 hover:text-blue-700 font-medium mr-4">
                                Apply Filters
                            </button>
                            <a href="{{ route('admin.brands.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                Clear all filters
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Brands Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Brand
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Contact
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Slug
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Featured
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($brands as $brand)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($brand->media || $brand->logo)
                                <img src="{{ $brand->getThumbnailUrl() }}" 
                                     alt="{{ $brand->name }}"
                                     class="h-10 w-10 rounded object-contain mr-3 bg-gray-50">
                            @else
                                <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center mr-3">
                                    <i class="fas fa-copyright text-gray-400"></i>
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $brand->name }}</div>
                                @if($brand->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($brand->description, 50) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            @if($brand->website)
                                <a href="{{ $brand->getWebsiteUrl() }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                    <i class="fas fa-globe mr-1"></i>
                                    {{ Str::limit($brand->website, 20) }}
                                </a>
                            @endif
                            @if($brand->email)
                                <div class="text-gray-600">
                                    <i class="fas fa-envelope mr-1"></i>
                                    {{ $brand->email }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600 font-mono">{{ $brand->slug }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button onclick="toggleStatus({{ $brand->id }})" 
                                class="status-toggle-{{ $brand->id }}">
                            @if($brand->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </button>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button onclick="toggleFeatured({{ $brand->id }})" 
                                class="featured-toggle-{{ $brand->id }}">
                            @if($brand->is_featured)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Not Featured
                                </span>
                            @endif
                        </button>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.brands.show', $brand) }}" 
                               class="text-blue-600 hover:text-blue-900"
                               title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.brands.edit', $brand) }}" 
                               class="text-indigo-600 hover:text-indigo-900"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.brands.duplicate', $brand) }}" 
                                  method="POST" 
                                  class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-green-600 hover:text-green-900"
                                        title="Duplicate">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </form>
                            <form id="delete-brand-{{ $brand->id }}" action="{{ route('admin.brands.destroy', $brand) }}" 
                                  method="POST" 
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        onclick="window.dispatchEvent(new CustomEvent('confirm-modal', { 
                                            detail: { 
                                                title: 'Delete Brand', 
                                                message: 'Are you sure you want to delete this brand?',
                                                onConfirm: () => document.getElementById('delete-brand-{{ $brand->id }}').submit()
                                            } 
                                        }))" 
                                        class="text-red-600 hover:text-red-900"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <i class="fas fa-copyright text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No brands found.</p>
                        <a href="{{ route('admin.brands.create') }}" 
                           class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Create First Brand
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($brands->hasPages() || $brands->total() > 0)
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <label for="perPage" class="text-sm text-gray-700">Show</label>
                        <select id="perPage" 
                                onchange="updatePerPage(this.value)"
                                class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-700">entries</span>
                    </div>
                    <span class="text-sm text-gray-500">
                        Showing {{ $brands->firstItem() ?? 0 }} to {{ $brands->lastItem() ?? 0 }} of {{ $brands->total() }} results
                    </span>
                </div>
                <div>
                    {{ $brands->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Toggle advanced filters visibility
function toggleFilters() {
    const filtersDiv = document.getElementById('advancedFilters');
    filtersDiv.classList.toggle('hidden');
}

// Auto-submit search form on Enter key
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('searchForm').submit();
            }
        });
    }
    
    // Show filters if any filter is active
    const hasActiveFilters = {{ (($filters['is_active'] ?? '') !== '' || ($filters['is_featured'] ?? '') !== '') ? 'true' : 'false' }};
    if (hasActiveFilters) {
        document.getElementById('advancedFilters').classList.remove('hidden');
    }
});

function updatePerPage(value) {
    // Get current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('per_page', value);
    urlParams.delete('page'); // Reset to first page when changing per page
    
    // Redirect with new parameters
    window.location.href = window.location.pathname + '?' + urlParams.toString();
}

function toggleStatus(brandId) {
    fetch(`/admin/brands/${brandId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            window.dispatchEvent(new CustomEvent('alert-toast', { 
                detail: { type: 'error', message: 'Failed to update status: ' + data.message } 
            }));
        }
    })
    .catch(error => {
        window.dispatchEvent(new CustomEvent('alert-toast', { 
            detail: { type: 'error', message: 'Error: ' + error } 
        }));
    });
}

function toggleFeatured(brandId) {
    fetch(`/admin/brands/${brandId}/toggle-featured`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            window.dispatchEvent(new CustomEvent('alert-toast', { 
                detail: { type: 'error', message: 'Failed to update featured status: ' + data.message } 
            }));
        }
    })
    .catch(error => {
        window.dispatchEvent(new CustomEvent('alert-toast', { 
            detail: { type: 'error', message: 'Error: ' + error } 
        }));
    });
}
</script>
@endpush
@endsection
