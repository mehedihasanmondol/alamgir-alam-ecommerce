@extends('layouts.admin')

@section('title', 'Review Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Review Details</h1>
        <a href="{{ route('admin.reviews.index') }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            Back to Reviews
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <!-- Product Info -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Product</h3>
                    <div class="flex items-center">
                        @if($review->product->image)
                            <img src="{{ Storage::url($review->product->image) }}" 
                                 alt="{{ $review->product->name }}"
                                 class="w-20 h-20 rounded object-cover mr-4">
                        @endif
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">{{ $review->product->name }}</h4>
                            <p class="text-sm text-gray-500">SKU: {{ $review->product->sku }}</p>
                            <a href="{{ route('products.show', $review->product->slug) }}" 
                               target="_blank"
                               class="text-sm text-blue-600 hover:text-blue-700">
                                View Product →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Reviewer Info -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Reviewer</h3>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center text-lg font-semibold text-gray-600">
                            {{ substr($review->reviewer_name, 0, 2) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $review->reviewer_name }}</div>
                            @if($review->user)
                                <div class="text-sm text-gray-500">{{ $review->user->email }}</div>
                            @elseif($review->reviewer_email)
                                <div class="text-sm text-gray-500">{{ $review->reviewer_email }}</div>
                            @endif
                            @if($review->is_verified_purchase)
                                <span class="inline-flex items-center text-xs text-green-600 mt-1">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Verified Purchase
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Rating -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Rating</h3>
                    <div class="flex items-center space-x-2">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-6 h-6 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                        <span class="text-lg font-semibold text-gray-900">{{ $review->rating }}/5</span>
                    </div>
                </div>

                <!-- Review Title -->
                @if($review->title)
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Title</h3>
                        <h4 class="text-lg font-semibold text-gray-900">{{ $review->title }}</h4>
                    </div>
                @endif

                <!-- Review Text -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Review</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $review->review }}</p>
                </div>

                <!-- Review Images -->
                @if($review->images && count($review->images) > 0)
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Images</h3>
                        <div class="grid grid-cols-4 gap-4">
                            @foreach($review->images as $image)
                                <img src="{{ Storage::url($image) }}" 
                                     alt="Review image" 
                                     class="w-full h-32 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-75 transition-opacity"
                                     onclick="window.open('{{ Storage::url($image) }}', '_blank')">
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Helpful Votes -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Helpful Votes</h3>
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                            </svg>
                            <span class="font-semibold text-gray-900">{{ $review->helpful_count }}</span>
                            <span class="text-sm text-gray-500">Helpful</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"/>
                            </svg>
                            <span class="font-semibold text-gray-900">{{ $review->not_helpful_count }}</span>
                            <span class="text-sm text-gray-500">Not Helpful</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                <!-- Status -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Status</h3>
                    @if($review->status === 'approved')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                            Approved
                        </span>
                    @elseif($review->status === 'pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                            Rejected
                        </span>
                    @endif
                </div>

                <!-- Dates -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Dates</h3>
                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="text-gray-500">Submitted:</span>
                            <span class="text-gray-900">{{ $review->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        @if($review->approved_at)
                            <div>
                                <span class="text-gray-500">Approved:</span>
                                <span class="text-gray-900">{{ $review->approved_at->format('M d, Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-2">
                    @if($review->status === 'pending')
                        <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                Approve Review
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.reviews.reject', $review->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                Reject Review
                            </button>
                        </form>
                    @endif
                    
                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this review?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Delete Review
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
