<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">My Feedback</h2>
        <p class="text-gray-600 mt-1">View and manage all your feedback submissions</p>
    </div>

    @if($feedback->count() > 0)
        <div class="space-y-4">
            @foreach($feedback as $item)
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                @if(\App\Models\SiteSetting::get('feedback_rating_enabled', '1') === '1')
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $item->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                @endif
                                <span class="text-sm text-gray-500">{{ $item->created_at->format('M d, Y h:i A') }}</span>
                            </div>

                            @if($item->title)
                                <h3 class="font-semibold text-gray-900 mb-2">{{ $item->title }}</h3>
                            @endif

                            <p class="text-gray-700 mb-3">{{ $item->feedback }}</p>

                            @if(\App\Models\SiteSetting::get('feedback_show_images', '1') === '1' && $item->images && count($item->images) > 0)
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @foreach($item->images as $image)
                                        @php
                                            $thumbPath = is_array($image) ? ($image['thumbnail'] ?? $image['original']) : $image;
                                        @endphp
                                        <img src="{{ asset('storage/' . $thumbPath) }}" 
                                             alt="Feedback image" 
                                             class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center space-x-4 text-sm">
                                <span class="flex items-center text-green-600">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $item->helpful_count }} Helpful
                                </span>
                                <span class="flex items-center text-red-600">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $item->not_helpful_count }} Not Helpful
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col items-end space-y-2">
                            @if($item->status === 'approved')
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                    Approved
                                </span>
                            @elseif($item->status === 'pending')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full">
                                    Pending Review
                                </span>
                            @elseif($item->status === 'rejected')
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">
                                    Rejected
                                </span>
                            @endif

                            @if($item->is_featured)
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Featured
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $feedback->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
            </svg>
            <p class="text-gray-500 text-lg font-medium mb-2">No feedback submissions yet</p>
            <p class="text-gray-400 mb-4">Share your experience and help others!</p>
            <a href="{{ route('feedback.index') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                Submit Your First Feedback
            </a>
        </div>
    @endif
</div>
