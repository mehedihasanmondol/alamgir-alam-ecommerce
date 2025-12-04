@extends('layouts.admin')

@section('title', 'Best Seller Products Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Section Settings -->
    <x-admin.section-settings
        :sectionEnabled="$sectionEnabled"
        :sectionTitle="$sectionTitle"
        toggleRoute="{{ route('admin.best-seller-products.toggle-section') }}"
        updateTitleRoute="{{ route('admin.best-seller-products.update-title') }}"
        sectionName="Best Seller Products"
    />

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Best Seller Products Management</h1>
            <p class="text-gray-600 mt-1">Manage products displayed in "Best Sellers" section on homepage</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Product Search -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Add Product to Best Sellers</h2>
                
                <!-- Livewire Product Selector -->
                @livewire('admin.best-seller-product-selector')

                <div class="mt-6 p-4 bg-blue-50 rounded-md">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Tips:</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Search by product name or SKU</li>
                        <li>• Drag and drop to reorder products</li>
                        <li>• Toggle status to show/hide products</li>
                        <li>• Only active products are shown on homepage</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Best Seller Products List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    Current Best Sellers ({{ $bestSellerProducts->count() }})
                </h2>

                @if($bestSellerProducts->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <p class="text-gray-600">No best seller products added yet. Add your first product above.</p>
                    </div>
                @else
                    <div id="bestseller-list" class="space-y-3">
                        @foreach($bestSellerProducts as $bestSeller)
                            <div 
                                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow-md transition cursor-move"
                                data-id="{{ $bestSeller->id }}"
                            >
                                <div class="flex items-center space-x-4 flex-1">
                                    <!-- Drag Handle -->
                                    <div class="text-gray-400 hover:text-gray-600 cursor-move">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                        </svg>
                                    </div>

                                    @if($bestSeller->product)
                                        <!-- Product Image -->
                                        @if($bestSeller->product->getPrimaryThumbnailUrl())
                                            <img 
                                                src="{{ $bestSeller->product->getPrimaryThumbnailUrl() }}" 
                                                alt="{{ $bestSeller->product->name }}"
                                                class="w-16 h-16 object-cover rounded-md"
                                            >
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif

                                        <!-- Product Info -->
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900">{{ $bestSeller->product->name }}</h3>
                                            <div class="flex items-center space-x-3 mt-1">
                                                <span class="text-sm text-gray-600">Order: {{ $bestSeller->sort_order }}</span>
                                                @if($bestSeller->product->sale_price)
                                                    <span class="text-sm font-semibold text-green-600">
                                                        {{ currency_format($bestSeller->product->sale_price) }}
                                                    </span>
                                                    <span class="text-sm text-gray-400 line-through">
                                                        {{ currency_format($bestSeller->product->price) }}
                                                    </span>
                                                @else
                                                    <span class="text-sm font-semibold text-gray-900">
                                                        {{ currency_format($bestSeller->product->price) }}
                                                    </span>
                                                @endif
                                                @if($bestSeller->product->variants->first())
                                                    <span class="text-xs text-gray-500">
                                                        Stock: {{ $bestSeller->product->variants->first()->stock_quantity }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <!-- Product Deleted/Missing -->
                                        <div class="w-16 h-16 bg-red-100 rounded-md flex items-center justify-center">
                                            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-red-600">Product Deleted</h3>
                                            <p class="text-sm text-gray-600">This product no longer exists</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    <!-- Toggle Status -->
                                    <button 
                                        onclick="toggleStatus({{ $bestSeller->id }})"
                                        class="px-3 py-1 rounded-md text-sm font-medium transition {{ $bestSeller->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                                    >
                                        {{ $bestSeller->is_active ? 'Active' : 'Inactive' }}
                                    </button>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.best-seller-products.destroy', $bestSeller) }}" method="POST" class="inline" id="delete-form-{{ $bestSeller->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="button"
                                            onclick="confirmDelete({{ $bestSeller->id }}, '{{ $bestSeller->product ? addslashes($bestSeller->product->name) : 'Deleted Product' }}')"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-md transition"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    // Sortable List
    const list = document.getElementById('bestseller-list');
    if (list) {
        new Sortable(list, {
            animation: 150,
            handle: '.cursor-move',
            onEnd: function(evt) {
                const items = Array.from(list.children).map((item, index) => ({
                    id: item.dataset.id,
                    sort_order: index
                }));

                fetch('{{ route("admin.best-seller-products.update-order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        });
    }

    // Toggle Status
    function toggleStatus(id) {
        fetch(`/admin/best-seller-products/${id}/toggle-status`, {
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
            }
        });
    }

    // Confirm Delete
    function confirmDelete(id, name) {
        if (confirm(`Are you sure you want to remove "${name}" from trending products?`)) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }
</script>
@endpush
@endsection
