@extends('layouts.customer')

@section('title', 'My Feedback')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900">My Feedback</h1>
        <p class="mt-1 text-sm text-gray-600">View and manage your feedback submissions</p>
    </div>

    <!-- Feedback List -->
    <div class="p-6">
        @if($feedbacks->count() > 0)
            <div class="space-y-4">
                @foreach($feedbacks as $feedback)
                    <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Title -->
                                @if($feedback->title)
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $feedback->title }}
                                    </h3>
                                @endif

                                <!-- Rating -->
                                @if($feedback->rating)
                                    <div class="flex items-center mt-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="ml-2 text-sm text-gray-600">{{ $feedback->rating }}/5</span>
                                    </div>
                                @endif

                                <!-- Feedback Text -->
                                <p class="mt-3 text-gray-700">
                                    {{ Str::limit($feedback->feedback, 200) }}
                                </p>

                                <!-- Meta Info -->
                                <div class="flex items-center mt-3 space-x-4 text-sm text-gray-500">
                                    <span>{{ $feedback->created_at->diffForHumans() }}</span>
                                    @if($feedback->is_approved)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending Review
                                        </span>
                                    @endif
                                </div>

                                <!-- Helpful Stats -->
                                @if($feedback->helpful_count > 0 || $feedback->not_helpful_count > 0)
                                    <div class="flex items-center mt-3 space-x-4 text-sm text-gray-600">
                                        <span>👍 {{ $feedback->helpful_count }} helpful</span>
                                        <span>👎 {{ $feedback->not_helpful_count }} not helpful</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="ml-4 flex flex-col space-y-2">
                                <a href="{{ route('customer.feedback.show', $feedback) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    View/Edit
                                </a>

                                <button onclick="deleteFeedback({{ $feedback->id }})" 
                                        class="inline-flex items-center px-3 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $feedbacks->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No feedback yet</h3>
                <p class="mt-1 text-sm text-gray-500">You haven't submitted any feedback yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal (Product-style with backdrop blur) -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 transition-opacity" 
             style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
             onclick="closeDeleteModal()"></div>
        
        <div class="relative rounded-lg shadow-xl max-w-md w-full p-6 border border-gray-200"
             style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Delete Feedback</h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                Are you sure you want to delete this feedback? This action cannot be undone.
            </p>
            
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteFeedback(id) {
    document.getElementById('deleteForm').action = `/my/feedback/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>
@endpush
@endsection
