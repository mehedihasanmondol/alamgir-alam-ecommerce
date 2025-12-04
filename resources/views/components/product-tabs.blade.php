@props(['product', 'averageRating', 'totalReviews'])

<div x-data="{ activeTab: 'description' }" class="bg-white rounded-lg shadow-sm overflow-hidden">
    <!-- Tab Navigation -->
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px overflow-x-auto" aria-label="Tabs">
            <!-- Description Tab -->
            <button 
                @click="activeTab = 'description'"
                :class="{ 'border-green-600 text-green-600': activeTab === 'description', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'description' }"
                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                Description
            </button>

            <!-- Specifications Tab -->
            @if($product->product_type === 'variable' && $product->variants->count() > 0)
            <button 
                @click="activeTab = 'specifications'"
                :class="{ 'border-green-600 text-green-600': activeTab === 'specifications', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'specifications' }"
                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                Specifications
            </button>
            @endif

            <!-- Reviews Tab -->
            <button 
                @click="activeTab = 'reviews'"
                :class="{ 'border-green-600 text-green-600': activeTab === 'reviews', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'reviews' }"
                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                Reviews ({{ $totalReviews }})
            </button>

            <!-- Shipping & Returns Tab -->
            <button 
                @click="activeTab = 'shipping'"
                :class="{ 'border-green-600 text-green-600': activeTab === 'shipping', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'shipping' }"
                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                Shipping & Returns
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="p-6 lg:p-8">
        <!-- Description Content -->
        <div x-show="activeTab === 'description'" x-cloak class="prose max-w-none">
            @if($product->description)
                <div class="text-gray-700 leading-relaxed">
                    {!! $product->description !!}
                </div>
            @else
                <p class="text-gray-500 italic">No description available for this product.</p>
            @endif

            <!-- Additional Product Details -->
            @if($product->short_description)
                <div class="mt-6 p-4 bg-green-50 border-l-4 border-green-600 rounded">
                    <h4 class="font-semibold text-gray-900 mb-2">Key Features:</h4>
                    <p class="text-gray-700">{{ $product->short_description }}</p>
                </div>
            @endif
        </div>

        <!-- Specifications Content -->
        <div x-show="activeTab === 'specifications'" x-cloak>
            @if($product->product_type === 'variable' && $product->variants->count() > 0)
                <div class="space-y-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Product Specifications</h3>
                    
                    <!-- Specifications Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- SKU -->
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50 w-1/3">
                                        SKU
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $product->variants->first()->sku }}
                                    </td>
                                </tr>

                                <!-- Brand -->
                                @if($product->brand)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">
                                        Brand
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $product->brand->name }}
                                    </td>
                                </tr>
                                @endif

                                <!-- Category -->
                                @if($product->category)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">
                                        Category
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $product->category->name }}
                                    </td>
                                </tr>
                                @endif

                                <!-- Weight -->
                                @if($product->variants->first()->weight)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">
                                        Weight
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $product->variants->first()->weight }} kg
                                    </td>
                                </tr>
                                @endif

                                <!-- Dimensions -->
                                @if($product->variants->first()->length || $product->variants->first()->width || $product->variants->first()->height)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">
                                        Dimensions
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $product->variants->first()->length ?? 0 }} × 
                                        {{ $product->variants->first()->width ?? 0 }} × 
                                        {{ $product->variants->first()->height ?? 0 }} cm
                                    </td>
                                </tr>
                                @endif

                                <!-- Available Variants -->
                                @if($product->variants->count() > 1)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">
                                        Available Options
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $product->variants->count() }} variants
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <p class="text-gray-500 italic">No specifications available for this product.</p>
            @endif
        </div>

        <!-- Reviews Content -->
        <div x-show="activeTab === 'reviews'" x-cloak id="reviews">
            <div class="space-y-6">
                <!-- Reviews Summary -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-gray-200">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Customer Reviews</h3>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($averageRating))
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-lg font-medium text-gray-900">
                                {{ number_format($averageRating, 1) }} out of 5
                            </span>
                            <span class="text-gray-500">
                                ({{ $totalReviews }} {{ Str::plural('review', $totalReviews) }})
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <button class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                            Write a Review
                        </button>
                    </div>
                </div>

                <!-- Reviews List -->
                @if($totalReviews > 0)
                    <div class="space-y-6">
                        <!-- Sample Review (Replace with actual reviews from database) -->
                        <div class="border-b border-gray-200 pb-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 font-semibold">
                                        JD
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">John Doe</h4>
                                            <div class="flex items-center mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-500">2 days ago</span>
                                    </div>
                                    <p class="text-gray-700 leading-relaxed">
                                        Great product! Exactly as described. Fast shipping and excellent quality.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No reviews yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Be the first to review this product.</p>
                        <div class="mt-6">
                            <button class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                                Write the First Review
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Shipping & Returns Content -->
        <div x-show="activeTab === 'shipping'" x-cloak>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Shipping Information</h3>
                    <div class="space-y-4 text-gray-700">
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <div>
                                <p class="font-medium">Free Shipping</p>
                                <p class="text-sm text-gray-600">On orders over ৳1000</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="font-medium">Delivery Time</p>
                                <p class="text-sm text-gray-600">3-5 business days within Dhaka, 5-7 days outside Dhaka</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="font-medium">Order Tracking</p>
                                <p class="text-sm text-gray-600">Track your order status online</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Return Policy</h3>
                    <div class="space-y-4 text-gray-700">
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <div>
                                <p class="font-medium">7-Day Return</p>
                                <p class="text-sm text-gray-600">Easy returns within 7 days of delivery</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <div>
                                <p class="font-medium">Quality Guarantee</p>
                                <p class="text-sm text-gray-600">100% authentic products guaranteed</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <div>
                                <p class="font-medium">Refund Process</p>
                                <p class="text-sm text-gray-600">Refunds processed within 5-7 business days</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
