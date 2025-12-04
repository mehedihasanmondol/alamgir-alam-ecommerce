<div>
    <!-- Success Messages -->
    <div x-data="{ show: false, message: '' }" 
         @link-added.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
         @link-updated.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
         @link-deleted.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
         @link-toggled.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)">
        <div x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg"
             style="display: none;">
            <span x-text="message"></span>
        </div>
    </div>

    <!-- Footer Links Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($sections as $section => $title)
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                <button wire:click="openAddModal('{{ $section }}')" 
                        class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-plus mr-1"></i>Add Link
                </button>
            </div>
            
            <div class="space-y-2">
                @if(isset($this->links[$section]) && $this->links[$section]->count() > 0)
                    @foreach($this->links[$section] as $link)
                    <div class="flex items-center justify-between bg-white p-3 rounded border {{ !$link->is_active ? 'opacity-50' : '' }}">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <p class="font-medium text-gray-900">{{ $link->title }}</p>
                                @if(!$link->is_active)
                                    <span class="px-2 py-1 bg-gray-200 text-gray-600 text-xs rounded">Inactive</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $link->url }}</p>
                        </div>
                        
                        <div class="flex items-center space-x-1">
                            <!-- Move Up/Down -->
                            <button wire:click="moveUp({{ $link->id }})" 
                                    class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
                                    title="Move Up">
                                <i class="fas fa-chevron-up text-xs"></i>
                            </button>
                            <button wire:click="moveDown({{ $link->id }})" 
                                    class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
                                    title="Move Down">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <!-- Toggle Active -->
                            <button wire:click="toggleActive({{ $link->id }})" 
                                    class="p-1 {{ $link->is_active ? 'text-green-600 hover:text-green-800' : 'text-gray-400 hover:text-gray-600' }} transition-colors"
                                    title="{{ $link->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="fas {{ $link->is_active ? 'fa-eye' : 'fa-eye-slash' }} text-xs"></i>
                            </button>
                            
                            <!-- Edit -->
                            <button wire:click="openEditModal({{ $link->id }})" 
                                    class="p-1 text-blue-600 hover:text-blue-800 transition-colors"
                                    title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            
                            <!-- Delete -->
                            <button wire:click="openDeleteModal({{ $link->id }})" 
                                    class="p-1 text-red-600 hover:text-red-800 transition-colors"
                                    title="Delete">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-link text-2xl mb-2"></i>
                        <p class="text-sm">No links added yet</p>
                        <button wire:click="openAddModal('{{ $section }}')" 
                                class="mt-2 text-green-600 hover:text-green-800 text-sm font-medium">
                            Add your first link
                        </button>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Add Link Modal -->
    @if($showAddModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 transition-all duration-300" 
             style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
             wire:click="closeAddModal"></div>
        
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative rounded-lg shadow-2xl max-w-md w-full p-6 border border-gray-200"
                 style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100">
                
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Add New Link</h3>
                    <button wire:click="closeAddModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Form -->
                <form wire:submit.prevent="addLink" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                        <select wire:model="selectedSection" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach($sections as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('selectedSection') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link Title</label>
                        <input type="text" wire:model="linkTitle" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter link title">
                        @error('linkTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label>
                        <input type="text" wire:model="linkUrl" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter URL (e.g., /about or https://example.com)">
                        @error('linkUrl') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3 pt-4">
                        <button type="button" wire:click="closeAddModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-1"></i>Add Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Link Modal -->
    @if($showEditModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 transition-all duration-300" 
             style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
             wire:click="closeEditModal"></div>
        
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative rounded-lg shadow-2xl max-w-md w-full p-6 border border-gray-200"
                 style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100">
                
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Edit Link</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Form -->
                <form wire:submit.prevent="updateLink" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                        <select wire:model="selectedSection" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach($sections as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('selectedSection') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link Title</label>
                        <input type="text" wire:model="linkTitle" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter link title">
                        @error('linkTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label>
                        <input type="text" wire:model="linkUrl" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter URL (e.g., /about or https://example.com)">
                        @error('linkUrl') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3 pt-4">
                        <button type="button" wire:click="closeEditModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                            <i class="fas fa-save mr-1"></i>Update Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 transition-all duration-300" 
             style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
             wire:click="closeDeleteModal"></div>
        
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative rounded-lg shadow-2xl max-w-md w-full p-6 border border-gray-200"
                 style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100">
                
                <!-- Icon -->
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                
                <!-- Title -->
                <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Delete Link</h3>
                
                <!-- Message -->
                <p class="text-sm text-gray-600 text-center mb-6">Are you sure you want to delete this footer link? This action cannot be undone.</p>
                
                <!-- Actions -->
                <div class="flex items-center justify-end space-x-3">
                    <button wire:click="closeDeleteModal" 
                            type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button wire:click="deleteLink" 
                            type="button"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
