<div class="p-6" >
    
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Feedback</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Approved</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Rejected</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Featured</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['featured'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, email, or feedback..." 
                   class="col-span-2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            
            <select wire:model.live="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>

            <select wire:model.live="ratingFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Ratings</option>
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
            </select>
        </div>

        @if(!empty($selectedItems))
            <div class="mt-4 flex items-center space-x-2">
                <span class="text-sm font-medium text-gray-700">{{ count($selectedItems) }} selected:</span>
                <button wire:click="bulkApprove" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                    <i class="fas fa-check mr-1"></i> Approve
                </button>
                <button wire:click="bulkReject" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                    <i class="fas fa-times mr-1"></i> Reject
                </button>
                <button wire:click="bulkDelete" class="px-3 py-1.5 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                    <i class="fas fa-trash mr-1"></i> Delete
                </button>
            </div>
        @endif
    </div>

    {{-- Feedback Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" wire:model="selectAll" class="rounded border-gray-300">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feedback</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($feedback as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" wire:model="selectedItems" value="{{ $item->id }}" class="rounded border-gray-300">
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->customer_name }}</div>
                                <div class="text-sm text-gray-500">{{ $item->customer_email }}</div>
                                <div class="text-sm text-gray-500">{{ $item->formatted_mobile }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $item->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($item->feedback, 50) }}</div>
                                @if($item->title)
                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($item->title, 30) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($item->status === 'approved')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                @endif
                                @if($item->is_featured)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 ml-1">Featured</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if($item->status === 'pending')
                                        <button wire:click="approve({{ $item->id }})" class="text-green-600 hover:text-green-900" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button wire:click="reject({{ $item->id }})" class="text-red-600 hover:text-red-900" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    <button wire:click="toggleFeatured({{ $item->id }})" 
                                            class="{{ $item->is_featured ? 'text-blue-600' : 'text-gray-400' }} hover:text-blue-900" 
                                            title="Toggle Featured">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <button wire:click="viewFeedback({{ $item->id }})" class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $item->id }})" class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p>No feedback found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $feedback->links() }}
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showDeleteModal') }">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity" 
                 style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
                 wire:click="$set('showDeleteModal', false)"></div>
            
            <div class="relative rounded-lg shadow-xl max-w-md w-full p-6 border border-gray-200"
                 style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Delete Feedback</h3>
                <p class="text-sm text-gray-500 text-center mb-6">Are you sure you want to delete this feedback? This action cannot be undone.</p>
                
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="deleteFeedback" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- View Feedback Modal --}}
    @if($showViewModal && $selectedFeedback)
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
            <div class="relative rounded-lg shadow-2xl max-w-3xl w-full border border-gray-200 max-h-[90vh] flex flex-col"
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
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Feedback Details</h3>
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $selectedFeedback->rating)
                                    <i class="fas fa-star text-amber-400"></i>
                                @else
                                    <i class="far fa-star text-gray-300"></i>
                                @endif
                            @endfor
                            <span class="ml-2 text-sm font-semibold text-gray-700">{{ $selectedFeedback->rating }}/5</span>
                        </div>
                    </div>
                    <button wire:click="closeViewModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                {{-- Body (Scrollable) --}}
                <div class="px-6 py-4 overflow-y-auto flex-1">

                {{-- Customer Info --}}
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Customer Information</h4>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Name:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $selectedFeedback->customer_name }}</span>
                            @if($selectedFeedback->user)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-user mr-1"></i> Registered
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Email:</span>
                            <span class="text-sm text-gray-900">{{ $selectedFeedback->customer_email }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Mobile:</span>
                            <span class="text-sm text-gray-900">{{ $selectedFeedback->formatted_mobile }}</span>
                        </div>
                        @if($selectedFeedback->customer_address)
                        <div class="flex items-start gap-2">
                            <span class="text-sm text-gray-600">Address:</span>
                            <span class="text-sm text-gray-900">{{ $selectedFeedback->customer_address }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Feedback Content --}}
                <div class="mb-6">
                    @if($selectedFeedback->title)
                    <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $selectedFeedback->title }}</h4>
                    @endif
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $selectedFeedback->feedback }}</p>
                </div>

                {{-- Feedback Images Gallery --}}
                @if($selectedFeedback->images && count($selectedFeedback->images) > 0)
                <div class="mb-6" x-data="{ 
                    showLightbox: false, 
                    currentImage: 0,
                    images: {{ json_encode(array_map(fn($img) => is_array($img) ? asset('storage/' . $img['original']) : asset('storage/' . $img), $selectedFeedback->images)) }}
                }">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">
                        Feedback Images ({{ count($selectedFeedback->images) }})
                    </h4>
                    
                    <!-- Image Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($selectedFeedback->images as $index => $image)
                        @php
                            $imagePath = is_array($image) ? $image['original'] : $image;
                        @endphp
                        <div class="group relative aspect-square rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-500 transition-all bg-gray-100"
                             @click="showLightbox = true; currentImage = {{ $index }}">
                            <img src="{{ asset('storage/' . $imagePath) }}" 
                                 alt="Feedback Image {{ $index + 1 }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            
                            <!-- Hover overlay -->
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity pointer-events-none"></div>
                            
                            <!-- Zoom icon -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                <div class="bg-white bg-opacity-90 rounded-full p-2">
                                    <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Image number badge -->
                            <div class="absolute top-2 right-2 bg-white text-gray-900 text-xs font-semibold px-2 py-1 rounded shadow-md">
                                {{ $index + 1 }}/{{ count($selectedFeedback->images) }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Lightbox Modal -->
                    <div x-show="showLightbox" 
                         x-cloak
                         @keydown.escape.window="showLightbox = false"
                         @keydown.arrow-left.window="currentImage = currentImage > 0 ? currentImage - 1 : images.length - 1"
                         @keydown.arrow-right.window="currentImage = currentImage < images.length - 1 ? currentImage + 1 : 0"
                         @click="showLightbox = false"
                         class="fixed inset-0 z-[60] bg-black bg-opacity-95"
                         style="display: none;">
                        
                        <!-- Close Button -->
                        <button @click.stop="showLightbox = false" 
                                class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-20 bg-black bg-opacity-50 rounded-full p-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        <!-- Main Content Container -->
                        <div class="h-full flex flex-col items-center justify-center p-4" @click.stop>
                            
                            <!-- Image Container with Navigation -->
                            <div class="relative w-full max-w-5xl flex items-center justify-center mb-20">
                                
                                <!-- Previous Button -->
                                <button @click.stop="currentImage = currentImage > 0 ? currentImage - 1 : images.length - 1"
                                        class="absolute left-0 text-white hover:text-gray-300 transition-colors z-10 bg-black bg-opacity-50 rounded-full p-3 hover:bg-opacity-70">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>

                                <!-- Image -->
                                <div class="relative">
                                    <img :src="images[currentImage]" 
                                         alt="Feedback Image"
                                         class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-2xl"
                                         @click.stop>
                                    
                                    <!-- Image Counter -->
                                    <div class="absolute -bottom-10 left-1/2 transform -translate-x-1/2 bg-white text-gray-900 px-4 py-2 rounded-full text-sm font-medium shadow-lg">
                                        <span x-text="currentImage + 1"></span> / <span x-text="images.length"></span>
                                    </div>
                                </div>

                                <!-- Next Button -->
                                <button @click.stop="currentImage = currentImage < images.length - 1 ? currentImage + 1 : 0"
                                        class="absolute right-0 text-white hover:text-gray-300 transition-colors z-10 bg-black bg-opacity-50 rounded-full p-3 hover:bg-opacity-70">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Thumbnail Strip -->
                            <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 max-w-4xl" @click.stop>
                                <div class="flex gap-2 px-4 py-3 bg-white rounded-lg shadow-2xl overflow-x-auto">
                                    <template x-for="(img, idx) in images" :key="idx">
                                        <div @click.stop="currentImage = idx"
                                             :class="{ 'ring-4 ring-blue-500': currentImage === idx, 'ring-2 ring-gray-300': currentImage !== idx }"
                                             class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden cursor-pointer transition-all hover:ring-4 hover:ring-blue-400">
                                            <img :src="img" 
                                                 alt="Thumbnail"
                                                 class="w-full h-full object-cover"
                                                 :class="{ 'opacity-100': currentImage === idx, 'opacity-60': currentImage !== idx }">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Helpful Votes --}}
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Helpfulness</h4>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-thumbs-up text-green-600"></i>
                            <span class="text-sm text-gray-900">{{ $selectedFeedback->helpful_count }} helpful</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-thumbs-down text-red-600"></i>
                            <span class="text-sm text-gray-900">{{ $selectedFeedback->not_helpful_count }} not helpful</span>
                        </div>
                    </div>
                </div>

                {{-- Status & Dates --}}
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Status & Timeline</h4>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Status:</span>
                            @if($selectedFeedback->status == 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
                            @elseif($selectedFeedback->status == 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                            @endif
                            @if($selectedFeedback->is_featured)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Featured</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Submitted:</span>
                            <span class="text-sm text-gray-900">{{ $selectedFeedback->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        @if($selectedFeedback->approved_at)
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Approved:</span>
                            <span class="text-sm text-gray-900">{{ $selectedFeedback->approved_at->format('M d, Y h:i A') }}</span>
                            @if($selectedFeedback->approver)
                                <span class="text-sm text-gray-600">by {{ $selectedFeedback->approver->name }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                </div>
                
                {{-- Footer Actions (Sticky) --}}
                @if($selectedFeedback->status == 'pending')
                <div class="px-6 py-4 border-t border-gray-200 bg-white rounded-b-lg flex-shrink-0">
                    <div class="flex gap-3">
                        <button wire:click="approve({{ $selectedFeedback->id }})" 
                                wire:loading.attr="disabled"
                                wire:target="approve"
                                class="flex-1 px-4 py-2 text-sm font-medium bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="approve">
                                <i class="fas fa-check mr-2"></i>Approve Feedback
                            </span>
                            <span wire:loading wire:target="approve" class="flex items-center justify-center">
                                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Approving...
                            </span>
                        </button>
                        <button wire:click="reject({{ $selectedFeedback->id }})" 
                                wire:loading.attr="disabled"
                                wire:target="reject"
                                class="flex-1 px-4 py-2 text-sm font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="reject">
                                <i class="fas fa-times mr-2"></i>Reject Feedback
                            </span>
                            <span wire:loading wire:target="reject" class="flex items-center justify-center">
                                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Rejecting...
                            </span>
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

