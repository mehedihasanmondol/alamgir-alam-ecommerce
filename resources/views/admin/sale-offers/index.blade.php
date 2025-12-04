@extends('layouts.admin')

@section('title', 'Sale Offers Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Section Settings -->
    <x-admin.section-settings
        :sectionEnabled="$sectionEnabled"
        :sectionTitle="$sectionTitle"
        toggleRoute="{{ route('admin.sale-offers.toggle-section') }}"
        updateTitleRoute="{{ route('admin.sale-offers.update-title') }}"
        sectionName="Sale Offers"
    />

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Sale Offers Management</h1>
            <p class="text-gray-600 mt-1">Manage products displayed in "Sale offers picked for you" section</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Product Search -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Add Product to Sale Offers</h2>
                
                <!-- Livewire Product Selector -->
                @livewire('admin.sale-offer-product-selector')

                <div class="mt-6 p-4 bg-blue-50 rounded-md">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Tips:</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Search by product name or SKU</li>
                        <li>• Drag and drop to reorder products</li>
                        <li>• Toggle status to show/hide offers</li>
                        <li>• Only active products are shown</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sale Offers List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    Current Sale Offers ({{ $saleOffers->count() }})
                </h2>

                @if($saleOffers->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-gray-600">No sale offers added yet. Add your first product above.</p>
                    </div>
                @else
                    <div id="sale-offers-list" class="space-y-3">
                        @foreach($saleOffers as $offer)
                            <div 
                                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow-md transition cursor-move"
                                data-id="{{ $offer->id }}"
                            >
                                <div class="flex items-center space-x-4 flex-1">
                                    <!-- Drag Handle -->
                                    <div class="text-gray-400 hover:text-gray-600 cursor-move">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                        </svg>
                                    </div>

                                    @if($offer->product)
                                        <!-- Product Image -->
                                        @if($offer->product->getPrimaryThumbnailUrl())
                                            <img 
                                                src="{{ $offer->product->getPrimaryThumbnailUrl() }}" 
                                                alt="{{ $offer->product->name }}"
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
                                            <h3 class="font-semibold text-gray-900">{{ $offer->product->name }}</h3>
                                            <div class="flex items-center space-x-3 mt-1">
                                                <span class="text-sm text-gray-600">Order: {{ $offer->display_order }}</span>
                                                @if($offer->product->sale_price)
                                                    <span class="text-sm font-semibold text-green-600">
                                                        {{ currency_format($offer->product->sale_price) }}
                                                    </span>
                                                    <span class="text-sm text-gray-400 line-through">
                                                        {{ currency_format($offer->product->price) }}
                                                    </span>
                                                @else
                                                    <span class="text-sm font-semibold text-gray-900">
                                                        {{ currency_format($offer->product->price) }}
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
                                    <form action="{{ route('admin.sale-offers.toggle', $offer) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit"
                                            class="px-3 py-1 rounded-md text-sm font-medium transition {{ $offer->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                                        >
                                            {{ $offer->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.sale-offers.destroy', $offer) }}" method="POST" class="inline" id="delete-form-{{ $offer->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="button"
                                            @click="$dispatch('confirm-modal', {
                                                title: 'Remove Sale Offer',
                                                message: 'Are you sure you want to remove {{ $offer->product ? addslashes($offer->product->name) : 'this item' }} from sale offers?',
                                                onConfirm: () => document.getElementById('delete-form-{{ $offer->id }}').submit()
                                            })"
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
    document.addEventListener('DOMContentLoaded', function() {
        const list = document.getElementById('sale-offers-list');
        
        if (list) {
            new Sortable(list, {
                animation: 150,
                handle: '.cursor-move',
                onEnd: function(evt) {
                    // Get all items in new order
                    const items = list.querySelectorAll('[data-id]');
                    const orders = Array.from(items).map((item, index) => ({
                        id: item.dataset.id,
                        display_order: index + 1
                    }));

                    // Send to server
                    fetch('{{ route("admin.sale-offers.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ orders: orders })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update order numbers in UI
                            items.forEach((item, index) => {
                                const orderSpan = item.querySelector('.text-sm.text-gray-600');
                                if (orderSpan) {
                                    orderSpan.textContent = 'Order: ' + (index + 1);
                                }
                            });
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        }
    });
</script>
@endpush
@endsection
