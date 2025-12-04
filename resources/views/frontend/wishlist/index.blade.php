@extends('layouts.app')

@section('title', 'My Wishlist - ' . \App\Models\SiteSetting::get('site_name', config('app.name')))

@section('description', 'View and manage your saved products. Add items to cart or remove from wishlist.')

@section('keywords', 'wishlist, saved items, favorites, saved products')

@section('robots', 'noindex, follow')

@section('content')
<div class="bg-gray-50 min-h-screen py-8" x-data="wishlistPage()">
    <div class="container mx-auto px-4">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Wishlist</h1>
            <p class="text-gray-600">
                <span x-text="Object.keys(wishlistData).length">{{ count($wishlist) }}</span> item(s) saved for later
            </p>
        </div>

        @if(count($wishlist) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            @foreach($wishlist as $key => $item)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition group">
                <!-- Product Image -->
                <div class="relative aspect-square bg-gray-100">
                    <a href="{{ route('products.show', $item['slug']) }}">
                        <img src="{{ $item['image'] ?? asset('images/placeholder.png') }}" 
                             alt="{{ $item['product_name'] }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    </a>
                    
                    <!-- Remove Button (Top Right) -->
                    <button @click="removeItem('{{ $key }}')"
                            class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-red-50 transition opacity-0 group-hover:opacity-100">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <!-- Discount Badge -->
                    @if(isset($item['original_price']) && $item['original_price'] > $item['price'])
                    <div class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                        SALE
                    </div>
                    @endif
                </div>

                <!-- Product Details -->
                <div class="p-4">
                    @if($item['brand'])
                    <p class="text-xs text-gray-600 mb-1">{{ $item['brand'] }}</p>
                    @endif
                    
                    <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2 min-h-[40px]">
                        <a href="{{ route('products.show', $item['slug']) }}" class="hover:text-green-600">
                            {{ $item['product_name'] }}
                        </a>
                    </h3>

                    @if(isset($item['sku']))
                    <p class="text-xs text-gray-500 mb-2">SKU: {{ $item['sku'] }}</p>
                    @endif

                    <!-- Price -->
                    <div class="mb-3">
                        <div class="flex items-baseline space-x-2">
                            <span class="text-lg font-bold text-gray-900">{{ currency_format($item['price']) }}</span>
                            @if(isset($item['original_price']) && $item['original_price'] > $item['price'])
                            <span class="text-sm text-gray-500 line-through">{{ currency_format($item['original_price']) }}</span>
                            @endif
                        </div>
                        @if(isset($item['original_price']) && $item['original_price'] > $item['price'])
                        <p class="text-xs text-green-600 font-medium">
                            Save {{ currency_format($item['original_price'] - $item['price']) }}
                        </p>
                        @endif
                    </div>

                    <!-- Added Date -->
                    @if(isset($item['added_at']))
                    <p class="text-xs text-gray-500 mb-3">
                        Added {{ \Carbon\Carbon::parse($item['added_at'])->diffForHumans() }}
                    </p>
                    @endif

                    <!-- Actions -->
                    <div class="space-y-2">
                        <button @click="moveToCart('{{ $key }}')"
                                class="w-full px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                            <div class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Add to Cart
                            </div>
                        </button>
                        
                        <button @click="removeItem('{{ $key }}')"
                                class="w-full px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Bulk Actions -->
        <div class="mt-8 flex flex-wrap gap-4 justify-center">
            <button @click="moveAllToCart()"
                    class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Add All to Cart
                </div>
            </button>
            
            <button @click="clearWishlist()"
                    class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear Wishlist
                </div>
            </button>
        </div>

        @else
        <!-- Empty Wishlist State -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Your wishlist is empty</h3>
            <p class="text-gray-600 mb-6">Save items you love for later</p>
            <a href="{{ route('shop') }}" 
               class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Start Shopping
            </a>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function wishlistPage() {
    return {
        wishlistData: @json($wishlist),
        
        moveToCart(wishlistKey) {
            fetch('/wishlist/move-to-cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    wishlist_key: wishlistKey
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: {
                            type: 'success',
                            message: 'Item added to cart!'
                        }
                    }));
                    
                    // Dispatch events for counters
                    Livewire.dispatch('cart-updated');
                    Livewire.dispatch('wishlist-updated');
                    
                    // Reload page
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: {
                        type: 'error',
                        message: 'Failed to add item to cart'
                    }
                }));
            });
        },
        
        removeItem(wishlistKey) {
            // Show confirmation modal
            window.dispatchEvent(new CustomEvent('confirm-modal', {
                detail: {
                    title: 'Remove from Wishlist',
                    message: 'Are you sure you want to remove this item from your wishlist?',
                    onConfirm: () => {
                        fetch('/wishlist/remove', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                wishlist_key: wishlistKey
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.dispatchEvent(new CustomEvent('show-toast', {
                                    detail: {
                                        type: 'success',
                                        message: 'Item removed from wishlist'
                                    }
                                }));
                                
                                Livewire.dispatch('wishlist-updated');
                                
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);
                            }
                        });
                    }
                }
            }));
        },
        
        moveAllToCart() {
            if (Object.keys(this.wishlistData).length === 0) return;
            
            window.dispatchEvent(new CustomEvent('confirm-modal', {
                detail: {
                    title: 'Add All to Cart',
                    message: `Add all ${Object.keys(this.wishlistData).length} item(s) to your cart?`,
                    onConfirm: () => {
                        const promises = Object.keys(this.wishlistData).map(key => {
                            return fetch('/wishlist/move-to-cart', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    wishlist_key: key
                                })
                            });
                        });
                        
                        Promise.all(promises)
                            .then(() => {
                                window.dispatchEvent(new CustomEvent('show-toast', {
                                    detail: {
                                        type: 'success',
                                        message: 'All items added to cart!'
                                    }
                                }));
                                
                                Livewire.dispatch('cart-updated');
                                Livewire.dispatch('wishlist-updated');
                                
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                window.dispatchEvent(new CustomEvent('show-toast', {
                                    detail: {
                                        type: 'error',
                                        message: 'Failed to add items to cart'
                                    }
                                }));
                            });
                    }
                }
            }));
        },
        
        clearWishlist() {
            if (Object.keys(this.wishlistData).length === 0) return;
            
            window.dispatchEvent(new CustomEvent('confirm-modal', {
                detail: {
                    title: 'Clear Wishlist',
                    message: 'Are you sure you want to remove all items from your wishlist?',
                    onConfirm: () => {
                        const promises = Object.keys(this.wishlistData).map(key => {
                            return fetch('/wishlist/remove', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    wishlist_key: key
                                })
                            });
                        });
                        
                        Promise.all(promises)
                            .then(() => {
                                window.dispatchEvent(new CustomEvent('show-toast', {
                                    detail: {
                                        type: 'success',
                                        message: 'Wishlist cleared!'
                                    }
                                }));
                                
                                Livewire.dispatch('wishlist-updated');
                                
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    }
                }
            }));
        }
    }
}
</script>
@endpush
@endsection
