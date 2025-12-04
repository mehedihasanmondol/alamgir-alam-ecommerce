@extends('layouts.admin')

@section('title', 'Pending Reviews')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pending Reviews</h1>
        <a href="{{ route('admin.reviews.index') }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            View All Reviews
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

    <!-- Pending Reviews List -->
    <div class="space-y-4">
        @forelse($reviews as $review)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-start justify-between">
                    <!-- Review Content -->
                    <div class="flex-1">
                        <!-- Product Info -->
                        <div class="flex items-center mb-4">
                            @if($review->product->image)
                                <img src="{{ Storage::url($review->product->image) }}" 
                                     alt="{{ $review->product->name }}"
                                     class="w-16 h-16 rounded object-cover mr-4">
                            @endif
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $review->product->name }}</h3>
                                <p class="text-sm text-gray-500">SKU: {{ $review->product->sku }}</p>
                            </div>
                        </div>

                        <!-- Reviewer Info -->
                        <div class="flex items-center space-x-4 mb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-sm font-semibold text-gray-600 mr-2">
                                    {{ substr($review->reviewer_name, 0, 2) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $review->reviewer_name }}</div>
                                    @if($review->is_verified_purchase)
                                        <span class="text-xs text-green-600">✓ Verified Purchase</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                        </div>

                        <!-- Review Title -->
                        @if($review->title)
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $review->title }}</h4>
                        @endif

                        <!-- Review Text -->
                        <p class="text-gray-700 mb-3">{{ $review->review }}</p>

                        <!-- Review Images -->
                        @if($review->images && count($review->images) > 0)
                            <div class="flex items-center space-x-2 mb-3">
                                @foreach($review->images as $image)
                                    <img src="{{ Storage::url($image) }}" 
                                         alt="Review image" 
                                         class="w-20 h-20 object-cover rounded-lg border border-gray-200 cursor-pointer"
                                         onclick="window.open('{{ Storage::url($image) }}', '_blank')">
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="ml-6 flex flex-col space-y-2">
                        <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                Approve
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.reviews.reject', $review->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                Reject
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this review?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pending reviews</h3>
                <p class="mt-1 text-sm text-gray-500">All reviews have been processed.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($reviews->hasPages())
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
@endsection
