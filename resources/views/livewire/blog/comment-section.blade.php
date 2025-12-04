<div class="bg-white rounded-lg shadow-sm mt-8 p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">
            <svg class="w-6 h-6 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Comments (<span wire:loading.remove>{{ $commentsCount }}</span><span wire:loading>...</span>)
        </h2>
    </div>

    <!-- Comments List -->
    @if($comments->count() > 0)
    <div class="space-y-6 mb-8" wire:loading.class="opacity-50">
        @foreach($comments as $comment)
        <div class="bg-gray-50 rounded-lg p-6 transition-all duration-300">
            <div class="flex items-start gap-4">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                        {{ substr($comment->commenter_name, 0, 1) }}
                    </div>
                </div>

                <!-- Comment Content -->
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $comment->commenter_name }}</h4>
                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $comment->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <button wire:click="replyTo({{ $comment->id }})"
                                class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Reply
                        </button>
                    </div>
                    <p class="text-gray-700 leading-relaxed">{{ $comment->content }}</p>

                    <!-- Replies -->
                    @if($comment->approvedReplies->count() > 0)
                    <div class="mt-4 space-y-3">
                        @foreach($comment->approvedReplies as $reply)
                        <div class="ml-8 bg-blue-50 rounded-lg p-4 border-l-4 border-blue-400">
                            <div class="flex items-start gap-3">
                                <!-- Reply Avatar -->
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($reply->commenter_name, 0, 1) }}
                                    </div>
                                </div>

                                <!-- Reply Content -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h5 class="font-semibold text-gray-900 text-sm">{{ $reply->commenter_name }}</h5>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                            Reply to {{ $comment->commenter_name }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-700 text-sm leading-relaxed">{{ $reply->content }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Load More Button -->
    @if($remainingComments > 0)
    <div class="text-center mb-8">
        <button wire:click="loadMore" 
                wire:loading.attr="disabled"
                class="inline-flex items-center px-6 py-3 bg-white border-2 border-blue-600 text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition-colors disabled:opacity-50">
            <svg wire:loading.remove wire:target="loadMore" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <svg wire:loading wire:target="loadMore" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="loadMore">Load More ({{ $remainingComments }} remaining)</span>
            <span wire:loading wire:target="loadMore">Loading...</span>
        </button>
    </div>
    @endif
    @else
    <div class="text-center py-12 mb-8">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <p class="text-gray-500 text-lg font-medium">No comments yet</p>
        <p class="text-gray-400 text-sm mt-1">Be the first to share your thoughts!</p>
    </div>
    @endif

    <!-- Success Message -->
    @if($showSuccess)
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center" 
         x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Thank you! Your comment has been submitted and is awaiting moderation.
    </div>
    @endif

    <!-- Comment Form -->
    <div class="bg-gray-50 rounded-lg p-6" id="comment-form">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            @if($replyingTo)
                <span class="text-blue-600">Replying to comment</span>
            @else
                Leave a Comment
            @endif
        </h3>

        <!-- Reply Context Box -->
        @if($replyingTo)
        @php
            $parentComment = $comments->firstWhere('id', $replyingTo);
        @endphp
        @if($parentComment)
        <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        <span class="text-sm font-semibold text-blue-900">Replying to {{ $parentComment->commenter_name }}</span>
                    </div>
                    <p class="text-sm text-gray-700 line-clamp-2">{{ $parentComment->content }}</p>
                </div>
                <button type="button" 
                        wire:click="cancelReply"
                        class="ml-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif
        @endif
        
        <form wire:submit="submitComment">
            <div class="space-y-4">
                @guest
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" 
                               wire:model="guest_name"
                               placeholder="Your Name" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('guest_name') border-red-500 @enderror">
                        @error('guest_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" 
                               wire:model="guest_email"
                               placeholder="your@email.com" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('guest_email') border-red-500 @enderror">
                        @error('guest_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @else
                <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                @endguest

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comment *</label>
                    <textarea wire:model="content"
                              rows="4" 
                              placeholder="Share your thoughts..." 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('content') border-red-500 @enderror"></textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Your comment will be reviewed before being published.</p>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-lg transition-colors">
                            <svg wire:loading.remove class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <svg wire:loading class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove>{{ $replyingTo ? 'Post Reply' : 'Post Comment' }}</span>
                            <span wire:loading>Posting...</span>
                        </button>
                    </div>
                    
                    @guest
                    <p class="text-sm text-gray-600">
                        Have an account? 
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">Sign in</a>
                    </p>
                    @endguest
                </div>
            </div>
        </form>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2">
        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Loading...</span>
    </div>
</div>

@script
<script>
    $wire.on('scroll-to-form', () => {
        document.getElementById('comment-form').scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
    
    $wire.on('comment-posted', () => {
        setTimeout(() => {
            $wire.showSuccess = false;
        }, 5000);
    });
</script>
@endscript
