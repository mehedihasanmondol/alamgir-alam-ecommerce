<div>
    <!-- Header with Stats -->
    <div class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Messages</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $messages->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-envelope text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Unread</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $unreadCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-envelope-open text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Replied</p>
                        <p class="text-2xl font-bold text-green-600">{{ \App\Models\ContactMessage::where('status', 'replied')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Archived</p>
                        <p class="text-2xl font-bold text-gray-600">{{ \App\Models\ContactMessage::where('status', 'archived')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-archive text-gray-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Search by name, email, subject, message..."
                >
            </div>

            <div>
                <select wire:model.live="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="unread">Unread</option>
                    <option value="read">Read</option>
                    <option value="replied">Replied</option>
                    <option value="archived">Archived</option>
                </select>
            </div>

            <div>
                <select wire:model.live="perPage" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="10">10 per page</option>
                    <option value="15">15 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Messages Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject & Message</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($messages as $message)
                    <tr class="hover:bg-gray-50 {{ $message->status == 'unread' ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($message->name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 {{ $message->status == 'unread' ? 'font-bold' : '' }}">
                                        {{ $message->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $message->email }}</div>
                                    @if($message->phone)
                                    <div class="text-xs text-gray-400">{{ $message->phone }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 {{ $message->status == 'unread' ? 'font-bold' : '' }}">
                                {{ Str::limit($message->subject, 60) }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ Str::limit($message->message, 100) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($message->status == 'unread')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-envelope mr-1"></i> Unread
                                </span>
                            @elseif($message->status == 'read')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas fa-envelope-open mr-1"></i> Read
                                </span>
                            @elseif($message->status == 'replied')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Replied
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-600">
                                    <i class="fas fa-archive mr-1"></i> Archived
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            <div>{{ $message->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $message->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="viewMessage({{ $message->id }})" 
                                   class="text-blue-600 hover:text-blue-900" 
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>

                                @if($message->status == 'unread')
                                <button wire:click="markAsRead({{ $message->id }})" 
                                        class="text-gray-600 hover:text-gray-900" 
                                        title="Mark as Read">
                                    <i class="fas fa-envelope-open"></i>
                                </button>
                                @endif

                                @if($message->status != 'replied')
                                <button wire:click="markAsReplied({{ $message->id }})" 
                                        class="text-green-600 hover:text-green-900" 
                                        title="Mark as Replied">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif

                                @if($message->status != 'archived')
                                <button wire:click="archive({{ $message->id }})" 
                                        class="text-yellow-600 hover:text-yellow-900" 
                                        title="Archive">
                                    <i class="fas fa-archive"></i>
                                </button>
                                @endif

                                <button wire:click="confirmDelete({{ $message->id }})" 
                                        class="text-red-600 hover:text-red-900" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                                <p class="text-gray-500 text-lg font-medium">No messages found</p>
                                <p class="text-gray-400 text-sm mt-2">Contact messages will appear here</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($messages->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $messages->links() }}
        </div>
        @endif
    </div>

    {{-- View Message Modal (Product Modal Style) --}}
    @if($viewingMessage)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('viewingMessage').live }">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity" 
                 style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
                 wire:click="closeViewModal"></div>
            
            <div class="relative rounded-lg shadow-xl max-w-4xl w-full p-6 border border-gray-200"
                 style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mr-3">
                            <i class="fas fa-envelope text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Contact Message</h3>
                    </div>
                    <button wire:click="closeViewModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Email-Style Message Display -->
                <div class="space-y-4">
                    <!-- Message Info -->
                    <div class="bg-gray-50 border-l-4 border-blue-500 p-4 rounded">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-sm font-semibold text-gray-600">From:</span>
                                <p class="text-gray-900 font-medium">{{ $viewingMessage->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600">Email:</span>
                                <p class="text-blue-600"><a href="mailto:{{ $viewingMessage->email }}">{{ $viewingMessage->email }}</a></p>
                            </div>
                            @if($viewingMessage->phone)
                            <div>
                                <span class="text-sm font-semibold text-gray-600">Phone:</span>
                                <p class="text-blue-600"><a href="tel:{{ $viewingMessage->phone }}">{{ $viewingMessage->phone }}</a></p>
                            </div>
                            @endif
                            <div>
                                <span class="text-sm font-semibold text-gray-600">Received:</span>
                                <p class="text-gray-900">{{ $viewingMessage->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600">Status:</span>
                                <p>
                                    @if($viewingMessage->status == 'unread')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Unread</span>
                                    @elseif($viewingMessage->status == 'read')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Read</span>
                                    @elseif($viewingMessage->status == 'replied')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Replied</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-600">Archived</span>
                                    @endif
                                </p>
                            </div>
                            @if($viewingMessage->ip_address)
                            <div>
                                <span class="text-sm font-semibold text-gray-600">IP Address:</span>
                                <p class="text-gray-700 text-xs">{{ $viewingMessage->ip_address }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Subject -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-600 mb-2">Subject:</h4>
                        <p class="text-lg font-semibold text-gray-900">{{ $viewingMessage->subject }}</p>
                    </div>

                    <!-- Message Content -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-600 mb-3">Message:</h4>
                        <div class="text-gray-800 whitespace-pre-wrap">{{ $viewingMessage->message }}</div>
                    </div>

                    <!-- Admin Note (if exists) -->
                    @if($viewingMessage->admin_note)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                        <h4 class="text-sm font-semibold text-yellow-800 mb-2">Admin Note:</h4>
                        <p class="text-yellow-900">{{ $viewingMessage->admin_note }}</p>
                    </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="flex gap-3 mt-6 pt-4 border-t">
                    <button wire:click="closeViewModal" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Close
                    </button>
                    <a href="mailto:{{ $viewingMessage->email }}" 
                       class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center">
                        <i class="fas fa-reply mr-2"></i>Reply via Email
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation Modal (Exact Product Modal Clone) --}}
    @if($deletingMessageId)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('deletingMessageId').live }">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity" 
                 style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
                 wire:click="cancelDelete"></div>
            
            <div class="relative rounded-lg shadow-xl max-w-md w-full p-6 border border-gray-200"
                 style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Delete Message</h3>
                <p class="text-sm text-gray-500 text-center mb-6">Are you sure you want to delete this message? This action cannot be undone.</p>
                
                <div class="flex gap-3">
                    <button wire:click="cancelDelete" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="deleteMessage" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
