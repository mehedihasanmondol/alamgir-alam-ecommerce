@extends('layouts.customer')

@section('title', 'Edit Feedback')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Feedback</h1>
                <p class="mt-1 text-sm text-gray-600">Update your feedback submission</p>
            </div>
            <a href="{{ route('customer.feedback.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="p-6">
        <form action="{{ route('customer.feedback.update', $feedback) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Rating -->
            @if(\App\Models\SiteSetting::get('feedback_rating_enabled', '1') === '1')
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Rating (Optional)
                </label>
                <div class="flex items-center space-x-2">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" 
                                   name="rating" 
                                   value="{{ $i }}" 
                                   {{ old('rating', $feedback->rating) == $i ? 'checked' : '' }}
                                   class="sr-only peer">
                            <svg class="w-8 h-8 text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 transition-colors" 
                                 fill="currentColor" 
                                 viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </label>
                    @endfor
                    <button type="button" 
                            onclick="clearRating()" 
                            class="ml-3 text-sm text-gray-600 hover:text-gray-900">
                        Clear
                    </button>
                </div>
                @error('rating')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <!-- Feedback Text -->
            <div class="mb-6">
                <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">
                    Feedback <span class="text-red-500">*</span>
                </label>
                <textarea name="feedback" 
                          id="feedback" 
                          rows="6"
                          required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('feedback') border-red-500 @enderror"
                          placeholder="Share your thoughts and feedback...">{{ old('feedback', $feedback->feedback) }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Maximum 2000 characters</p>
                @error('feedback')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Info -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Current Status</p>
                        <p class="mt-1 text-sm text-blue-700">
                            @if($feedback->is_approved)
                                Your feedback has been approved and is publicly visible.
                            @else
                                Your feedback is pending review by our team.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Meta Information -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Submitted:</span>
                        <span class="text-gray-600">{{ $feedback->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Last Updated:</span>
                        <span class="text-gray-600">{{ $feedback->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($feedback->helpful_count > 0 || $feedback->not_helpful_count > 0)
                    <div>
                        <span class="font-medium text-gray-700">Helpful votes:</span>
                        <span class="text-gray-600">{{ $feedback->helpful_count }} 👍</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Not helpful votes:</span>
                        <span class="text-gray-600">{{ $feedback->not_helpful_count }} 👎</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <button type="button" 
                        onclick="deleteFeedback()" 
                        class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Feedback
                </button>

                <div class="flex space-x-3">
                    <a href="{{ route('customer.feedback.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Feedback
                    </button>
                </div>
            </div>
        </form>
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
            
            <form action="{{ route('customer.feedback.destroy', $feedback) }}" method="POST">
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
function clearRating() {
    document.querySelectorAll('input[name="rating"]').forEach(input => {
        input.checked = false;
    });
}

function deleteFeedback() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>
@endpush
@endsection
