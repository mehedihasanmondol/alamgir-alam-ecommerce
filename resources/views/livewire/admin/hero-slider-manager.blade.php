<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden hover:shadow-md transition-shadow duration-200">
    <!-- Enhanced Header -->
    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Hero Sliders</h2>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $sliders->count() }} slider(s)</p>
                </div>
            </div>
            <button 
                wire:click="openCreateModal"
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all shadow-sm hover:shadow-lg font-medium"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Slider
            </button>
        </div>
    </div>

    <!-- Sliders List -->
    <div class="p-6">
        @if($sliders->isEmpty())
            <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-purple-100 mb-4">
                    <svg class="w-10 h-10 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No sliders yet</h3>
                <p class="text-gray-600 mb-6">Get started by creating your first hero slider</p>
                <button 
                    wire:click="openCreateModal"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all shadow-sm hover:shadow-lg font-medium"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create First Slider
                </button>
            </div>
        @else
            <div class="space-y-4" id="sliders-list">
                @foreach($sliders as $slider)
                    <div 
                        data-slider-id="{{ $slider->id }}"
                        wire:key="slider-{{ $slider->id }}"
                        class="slider-item group relative bg-white border-2 border-gray-200 rounded-xl hover:border-purple-300 hover:shadow-md transition-all duration-200"
                    >
                        <!-- Drag Handle -->
                        <div class="drag-handle absolute left-2 top-1/2 -translate-y-1/2 cursor-move opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                            </svg>
                        </div>

                        <div class="flex items-center p-4 pl-12">
                            <!-- Slider Image -->
                            <div class="flex-shrink-0 w-32 h-20 rounded-lg overflow-hidden bg-gray-100">
                                <img 
                                    src="{{ $slider->image_url }}" 
                                    alt="{{ $slider->title }}"
                                    class="w-full h-full object-cover"
                                >
                            </div>

                            <!-- Slider Info -->
                            <div class="flex-1 ml-6">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $slider->title }}</h3>
                                        @if($slider->subtitle)
                                            <p class="text-sm text-gray-600 mt-1">{{ $slider->subtitle }}</p>
                                        @endif
                                        <div class="flex items-center space-x-4 mt-2">
                                            @if($slider->link)
                                                <a href="{{ $slider->link }}" target="_blank" class="text-xs text-purple-600 hover:text-purple-700 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                    View Link
                                                </a>
                                            @endif
                                            @if($slider->button_text)
                                                <span class="text-xs text-gray-500">
                                                    Button: <span class="font-medium">{{ $slider->button_text }}</span>
                                                </span>
                                            @endif
                                            <span class="text-xs text-gray-500">
                                                Order: <span class="font-medium">{{ $slider->order }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2 ml-4">
                                <!-- Toggle Active -->
                                <button 
                                    wire:click="toggleActive({{ $slider->id }})"
                                    class="p-2 rounded-lg transition-all {{ $slider->is_active ? 'bg-green-100 text-green-600 hover:bg-green-200' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}"
                                    title="{{ $slider->is_active ? 'Active' : 'Inactive' }}"
                                >
                                    @if($slider->is_active)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                        </svg>
                                    @endif
                                </button>

                                <!-- Edit -->
                                <button 
                                    wire:click="openEditModal({{ $slider->id }})"
                                    class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-all"
                                    title="Edit"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>

                                <!-- Delete -->
                                <button 
                                    wire:click="confirmDelete({{ $slider->id }})"
                                    class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all"
                                    title="Delete"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Drag & Drop Hint -->
            <div class="mt-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                <div class="flex items-center text-sm text-purple-800">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p><strong>Tip:</strong> Hover over a slider and drag the handle icon to reorder. Changes are saved automatically.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- SortableJS Script -->
    @push('styles')
    <style>
        /* Drag and Drop Styles */
        .sortable-ghost {
            opacity: 0.4;
            background: #f3e8ff !important;
            border: 2px dashed #a855f7 !important;
        }
        
        .sortable-drag {
            opacity: 0.9;
            box-shadow: 0 20px 40px rgba(168, 85, 247, 0.3);
            transform: rotate(3deg) scale(1.02);
            cursor: grabbing !important;
        }
        
        .sortable-chosen {
            background: #faf5ff;
            border-color: #a855f7 !important;
        }
        
        .sortable-chosen .drag-handle {
            opacity: 1 !important;
            color: #9333ea !important;
        }
        
        .drag-handle {
            cursor: grab;
            user-select: none;
        }
        
        .drag-handle:active {
            cursor: grabbing;
        }
        
        .slider-item {
            transition: all 0.2s ease;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            initializeSortable();
            
            Livewire.hook('morph.updated', () => {
                initializeSortable();
            });

            // Listen for media uploaded event from image uploader component
            window.addEventListener('media-uploaded-hero_slider_image', (event) => {
                @this.call('mediaUploaded', event.detail.mediaId);
            });
        });

        function initializeSortable() {
            const slidersList = document.getElementById('sliders-list');
            if (slidersList && !slidersList.sortableInstance) {
                slidersList.sortableInstance = new Sortable(slidersList, {
                    handle: '.drag-handle',
                    animation: 200,
                    easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    chosenClass: 'sortable-chosen',
                    forceFallback: false,
                    fallbackTolerance: 3,
                    scroll: true,
                    scrollSensitivity: 60,
                    scrollSpeed: 10,
                    bubbleScroll: true,
                    onStart: function(evt) {
                        evt.item.style.cursor = 'grabbing';
                    },
                    onEnd: function(evt) {
                        evt.item.style.cursor = '';
                        
                        const orderedIds = Array.from(slidersList.children)
                            .map(item => item.getAttribute('data-slider-id'))
                            .filter(id => id !== null);
                        
                        if (orderedIds.length > 0) {
                            @this.call('updateOrder', orderedIds);
                        }
                    }
                });
            }
        }
    </script>
    @endpush

    <!-- Modal for Create/Edit -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div 
                    class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                    @click.away="closeModal"
                >
                    <!-- Modal Header -->
                    <div class="sticky top-0 bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4 rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white">
                                {{ $editingId ? 'Edit Slider' : 'Create New Slider' }}
                            </h3>
                            <button 
                                wire:click="closeModal"
                                class="text-white hover:bg-white/20 rounded-lg p-2 transition"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <form wire:submit.prevent="save" class="p-6 space-y-6">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="title"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                placeholder="Enter slider title"
                            >
                            @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Subtitle -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                Subtitle
                            </label>
                            <input 
                                type="text" 
                                wire:model="subtitle"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                placeholder="Enter slider subtitle"
                            >
                            @error('subtitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Image Upload with Media Library -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                Slider Image @if(!$editingId)<span class="text-red-500">*</span>@endif
                            </label>
                            
                            @if($editingId && $existingMediaId)
                                @php
                                    $existingMedia = \App\Models\Media::find($existingMediaId);
                                @endphp
                                <div class="mb-3">
                                    @if($existingMedia)
                                        <img 
                                            src="{{ $existingMedia->large_url ?? $existingMedia->url }}" 
                                            alt="Current slider image"
                                            class="h-32 rounded-lg border-2 border-gray-200"
                                        >
                                        <p class="text-xs text-gray-500 mt-1">Current image</p>
                                    @endif
                                </div>
                            @endif

                            <div wire:ignore>
                                <x-image-uploader 
                                    target-field="hero_slider_image"
                                    :media-id="null"
                                    :aspect-ratio="[16, 6]"
                                    :width="1920"
                                    :height="720"
                                    label="Upload New Slider Image"
                                    help-text="Recommended size: 1920x720px for optimal display across all devices"
                                />
                            </div>
                            @error('media_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Link -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                Link URL
                            </label>
                            <input 
                                type="url" 
                                wire:model="link"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                placeholder="https://example.com"
                            >
                            @error('link') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Button Text -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                Button Text
                            </label>
                            <input 
                                type="text" 
                                wire:model="button_text"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                placeholder="Shop Now"
                            >
                            @error('button_text') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                Display Order
                            </label>
                            <input 
                                type="number" 
                                wire:model="order"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                placeholder="Auto"
                                min="1"
                            >
                            @error('order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Is Active -->
                        <div>
                            <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100 transition">
                                <span class="text-sm font-semibold text-gray-800">Active Slider</span>
                                <input 
                                    type="checkbox" 
                                    wire:model="is_active"
                                    class="sr-only peer"
                                >
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <button 
                                type="button"
                                wire:click="closeModal"
                                class="px-6 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="save"
                                class="px-6 py-2.5 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition font-medium flex items-center shadow-sm hover:shadow-lg disabled:opacity-75"
                            >
                                <svg wire:loading.remove wire:target="save" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <svg wire:loading wire:target="save" class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Slider' : 'Create Slider' }}</span>
                                <span wire:loading wire:target="save">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-[60] overflow-y-auto">
            <!-- Backdrop with blur -->
            <div class="fixed inset-0 transition-opacity" 
                 style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
                 wire:click="$set('showDeleteModal', false)"></div>
            
            <!-- Modal Content -->
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full" @click.stop>
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white">Delete Slider</h3>
                            <button 
                                wire:click="$set('showDeleteModal', false)"
                                class="text-white hover:bg-white/20 rounded-lg p-2 transition"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Modal Body -->
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">Are you sure?</h4>
                                <p class="text-sm text-gray-600 mt-1">This action cannot be undone. The slider will be permanently deleted.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end space-x-3 px-6 pb-6">
                        <button 
                            wire:click="$set('showDeleteModal', false)"
                            class="px-6 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium"
                        >
                            Cancel
                        </button>
                        <button 
                            wire:click="deleteSlider"
                            wire:loading.attr="disabled"
                            class="px-6 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition font-medium flex items-center shadow-sm hover:shadow-lg disabled:opacity-75"
                        >
                            <svg wire:loading.remove class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <svg wire:loading class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove>Delete Slider</span>
                            <span wire:loading>Deleting...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
