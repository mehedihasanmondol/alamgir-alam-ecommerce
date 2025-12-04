{{-- View Comment Modal --}}
@if($showViewModal && $selectedComment)
<div class="fixed inset-0 z-50 overflow-y-auto" 
     x-data="{ show: @entangle('showViewModal') }"
     x-show="show"
     x-cloak
     style="display: none;">
    
    <!-- Backdrop with blur -->
    <div class="fixed inset-0 transition-all duration-300" 
         style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
         @click="$wire.closeViewModal()"></div>
    
    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative rounded-lg shadow-2xl max-w-2xl w-full border border-gray-200 max-h-[90vh] flex flex-col"
             style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"
             @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90">
            
            {{-- Header (Sticky) --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white rounded-t-lg flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-900">Comment Details</h3>
                <button wire:click="closeViewModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            {{-- Body (Scrollable) --}}
            <div class="px-6 py-4 overflow-y-auto flex-1">

            {{-- Post Info --}}
            @if($selectedComment->post)
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Blog Post</h4>
                <a href="{{ route('products.show', $selectedComment->post->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-900 font-medium">
                    {{ $selectedComment->post->title }}
                </a>
            </div>
            @endif

            {{-- Commenter Info --}}
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Commenter Information</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Name:</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $selectedComment->commenter_name }}
                        </span>
                        @if($selectedComment->user)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-user mr-1"></i> Registered
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-user-slash mr-1"></i> Guest
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Email:</span>
                        <span class="text-sm text-gray-900">{{ $selectedComment->commenter_email }}</span>
                    </div>
                </div>
            </div>

            {{-- Comment Content --}}
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Comment</h4>
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $selectedComment->content }}</p>
            </div>

            {{-- Reply Info --}}
            @if($selectedComment->parent_id)
            <div class="bg-purple-50 rounded-lg p-4 mb-6">
                <h4 class="text-sm font-medium text-purple-700 mb-2">
                    <i class="fas fa-reply mr-1"></i> This is a Reply
                </h4>
                <p class="text-sm text-purple-600">This comment is a reply to another comment.</p>
            </div>
            @endif

            {{-- Status & Dates --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Status & Timeline</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Status:</span>
                        @if($selectedComment->status == 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
                        @elseif($selectedComment->status == 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($selectedComment->status == 'spam')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Spam</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Trash</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Submitted:</span>
                        <span class="text-sm text-gray-900">{{ $selectedComment->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($selectedComment->approved_at)
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">Approved:</span>
                        <span class="text-sm text-gray-900">{{ $selectedComment->approved_at->format('M d, Y h:i A') }}</span>
                        @if($selectedComment->approvedBy)
                            <span class="text-sm text-gray-600">by {{ $selectedComment->approvedBy->name }}</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            </div>
            
            {{-- Footer Actions (Sticky) --}}
            @if($selectedComment->status == 'pending')
            <div class="px-6 py-4 border-t border-gray-200 bg-white rounded-b-lg flex-shrink-0">
                <div class="flex gap-3">
                    <button wire:click="approveComment({{ $selectedComment->id }})" 
                            wire:loading.attr="disabled"
                            wire:target="approveComment"
                            class="flex-1 px-4 py-2 text-sm font-medium bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="approveComment">
                            <i class="fas fa-check mr-2"></i>Approve Comment
                        </span>
                        <span wire:loading wire:target="approveComment" class="flex items-center justify-center">
                            <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Approving...
                        </span>
                    </button>
                    <button wire:click="markAsSpam({{ $selectedComment->id }})" 
                            wire:loading.attr="disabled"
                            wire:target="markAsSpam"
                            class="flex-1 px-4 py-2 text-sm font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="markAsSpam">
                            <i class="fas fa-ban mr-2"></i>Mark as Spam
                        </span>
                        <span wire:loading wire:target="markAsSpam" class="flex items-center justify-center">
                            <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
