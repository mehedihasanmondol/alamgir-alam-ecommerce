<div>
    @if(!$post)
        <span class="text-xs text-gray-400">Loading...</span>
    @else
        <!-- Checkboxes List with Absolute Manage Button -->
        <div class="relative">
            <!-- Manage Button (Icon Only - Absolute Position) -->
            <button 
                wire:click="openModal" 
                type="button"
                class="absolute top-0 right-0 p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded transition-colors z-10"
                title="Manage all indicators"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>

            <!-- Checkboxes List (Single Column) -->
            <div class="space-y-3">
                @forelse($availableTickMarks as $tickMark)
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input 
                                wire:model="selectedTickMarks"
                                wire:change="saveTickMarks"
                                type="checkbox" 
                                value="{{ $tickMark['id'] }}"
                                id="tick-{{ $tickMark['id'] }}"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                        </div>
                        <div class="ml-3 flex-1">
                            <label for="tick-{{ $tickMark['id'] }}" class="flex items-center cursor-pointer">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: {{ $tickMark['bg_color'] }}; color: {{ $tickMark['text_color'] }};">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        {!! (new \App\Modules\Blog\Models\TickMark(['icon' => $tickMark['icon']]))->getIconHtml() !!}
                                    </svg>
                                    {{ $tickMark['label'] }}
                                </span>
                            </label>
                            @if($tickMark['description'])
                                <p class="text-xs text-gray-500 mt-0.5">{{ $tickMark['description'] }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500 text-sm">
                        No indicators available. 
                        <button wire:click="openCreateModal" type="button" class="text-blue-600 hover:text-blue-800 underline">
                            Create one now
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Manage Tick Marks Modal -->
        <div x-data="{ show: @entangle('showModal') }"
             x-show="show"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            
            <!-- Backdrop with blur -->
            <div class="fixed inset-0 transition-all duration-300" 
                 style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
                 @click="$wire.closeModal()"></div>

            <!-- Modal Content -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative rounded-lg shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto border border-gray-200"
                     style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"
                     @click.stop
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-90"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-90">
                    <div class="px-6 pt-5 pb-4 bg-white">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                    Manage Quality Indicators
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">Select multiple badges and create new ones</p>
                            </div>
                            <button 
                                wire:click="openCreateModal"
                                type="button"
                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Create New
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 max-h-96 overflow-y-auto">
                            @forelse($availableTickMarks as $tickMark)
                                <div 
                                    wire:click="toggleTickMark({{ $tickMark['id'] }})"
                                    class="flex items-start p-3 border-2 rounded-lg cursor-pointer transition-all {{ in_array($tickMark['id'], $selectedTickMarks) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}"
                                >
                                    <div class="flex items-center h-5">
                                        <input 
                                            type="checkbox" 
                                            checked="{{ in_array($tickMark['id'], $selectedTickMarks) ? 'checked' : '' }}"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 pointer-events-none"
                                        >
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between gap-2 mb-1">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center p-1 rounded" style="background-color: {{ $tickMark['bg_color'] }}; color: {{ $tickMark['text_color'] }};">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        {!! (new \App\Modules\Blog\Models\TickMark(['icon' => $tickMark['icon']]))->getIconHtml() !!}
                                                    </svg>
                                                </span>
                                                <label class="font-medium text-gray-900 cursor-pointer">
                                                    {{ $tickMark['label'] }}
                                                </label>
                                                @if($tickMark['is_system'])
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                        System
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <button 
                                                    wire:click.stop="openEditModal({{ $tickMark['id'] }})"
                                                    type="button"
                                                    class="text-gray-400 hover:text-blue-600 transition-colors"
                                                    title="Edit indicator"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                <button 
                                                    wire:click.stop="deleteTickMark({{ $tickMark['id'] }})"
                                                    type="button"
                                                    class="text-gray-400 hover:text-red-600 transition-colors"
                                                    title="Delete indicator"
                                                    onclick="return confirm('Are you sure you want to delete this indicator?')"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        @if($tickMark['description'])
                                            <p class="text-sm text-gray-500">{{ $tickMark['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2 text-center py-8 text-gray-500">
                                    No tick marks available. Create one to get started!
                                </div>
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t">
                        <button 
                            wire:click="closeModal"
                            type="button" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Cancel
                        </button>
                        <button 
                            wire:click="saveTickMarks"
                            type="button" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create New Tick Mark Modal -->
        <div x-data="{ show: @entangle('showCreateModal') }"
             x-show="show"
             x-cloak
             class="fixed inset-0 z-[60] overflow-y-auto"
             style="display: none;">
            
            <!-- Backdrop with blur -->
            <div class="fixed inset-0 transition-all duration-300" 
                 style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
                 @click="$wire.closeCreateModal()"></div>

            <!-- Modal Content -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative rounded-lg shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto border border-gray-200"
                     style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"
                     @click.stop
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-90"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-90">
                    <div class="px-6 pt-5 pb-4 bg-white">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="create-modal-title">
                                Create New Quality Indicator
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Define a custom badge for your posts</p>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Name -->
                            <div>
                                <label for="new-name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    wire:model="newTickMarkName"
                                    type="text" 
                                    id="new-name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g., Hot, Featured, Exclusive"
                                >
                                @error('newTickMarkName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Label -->
                            <div>
                                <label for="new-label" class="block text-sm font-medium text-gray-700 mb-1">
                                    Display Label <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    wire:model="newTickMarkLabel"
                                    type="text" 
                                    id="new-label"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Label shown to users"
                                >
                                @error('newTickMarkLabel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="new-description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Description
                                </label>
                                <textarea 
                                    wire:model="newTickMarkDescription"
                                    id="new-description"
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Brief description of this tick mark"
                                ></textarea>
                            </div>

                            <!-- Icon Picker with Preview -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Icon <span class="text-red-500">*</span>
                                </label>
                                <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3">
                                    <div class="grid grid-cols-6 gap-2">
                                        @php
                                            $icons = [
                                                ['value' => 'check-circle', 'label' => 'Check'],
                                                ['value' => 'star', 'label' => 'Star'],
                                                ['value' => 'trending-up', 'label' => 'Trending'],
                                                ['value' => 'crown', 'label' => 'Crown'],
                                                ['value' => 'flame', 'label' => 'Flame'],
                                                ['value' => 'sparkles', 'label' => 'Sparkles'],
                                                ['value' => 'badge-check', 'label' => 'Badge'],
                                                ['value' => 'lightning-bolt', 'label' => 'Lightning'],
                                                ['value' => 'heart', 'label' => 'Heart'],
                                                ['value' => 'shield', 'label' => 'Shield'],
                                                ['value' => 'gift', 'label' => 'Gift'],
                                                ['value' => 'bell', 'label' => 'Bell'],
                                                ['value' => 'bookmark', 'label' => 'Bookmark'],
                                                ['value' => 'flag', 'label' => 'Flag'],
                                                ['value' => 'eye', 'label' => 'Eye'],
                                                ['value' => 'lock', 'label' => 'Lock'],
                                                ['value' => 'unlock', 'label' => 'Unlock'],
                                                ['value' => 'thumbs-up', 'label' => 'Like'],
                                                ['value' => 'rocket', 'label' => 'Rocket'],
                                                ['value' => 'zap', 'label' => 'Zap'],
                                                ['value' => 'award', 'label' => 'Award'],
                                                ['value' => 'trophy', 'label' => 'Trophy'],
                                                ['value' => 'diamond', 'label' => 'Diamond'],
                                                ['value' => 'sun', 'label' => 'Sun'],
                                            ];
                                        @endphp
                                        @foreach($icons as $icon)
                                            <button 
                                                type="button"
                                                wire:click="$set('newTickMarkIcon', '{{ $icon['value'] }}')"
                                                class="flex flex-col items-center justify-center p-2 border-2 rounded-lg transition-all hover:border-blue-300 {{ $newTickMarkIcon === $icon['value'] ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}"
                                                title="{{ $icon['label'] }}"
                                            >
                                                <svg class="w-5 h-5 {{ $newTickMarkIcon === $icon['value'] ? 'text-blue-600' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    {!! (new \App\Modules\Blog\Models\TickMark(['icon' => $icon['value']]))->getIconHtml() !!}
                                                </svg>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Selected: <span class="font-medium">{{ ucfirst(str_replace('-', ' ', $newTickMarkIcon)) }}</span></p>
                            </div>

                            <!-- Custom Color Picker -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Color <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-3">
                                    <input 
                                        type="color" 
                                        wire:model.live="newTickMarkColor"
                                        class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                                    >
                                    <input 
                                        type="text" 
                                        wire:model.live="newTickMarkColor"
                                        placeholder="#3B82F6"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                    >
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Choose any color or enter hex code</p>
                            </div>

                            <!-- Live Preview -->
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <p class="text-xs font-medium text-gray-600 mb-2">Preview:</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" style="background-color: {{ $newTickMarkColor }}; color: {{ strlen($newTickMarkColor) === 7 ? (hexdec(substr($newTickMarkColor, 1, 2)) * 0.299 + hexdec(substr($newTickMarkColor, 3, 2)) * 0.587 + hexdec(substr($newTickMarkColor, 5, 2)) * 0.114 > 128 ? '#000000' : '#FFFFFF') : '#FFFFFF' }};">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        {!! (new \App\Modules\Blog\Models\TickMark(['icon' => $newTickMarkIcon]))->getIconHtml() !!}
                                    </svg>
                                    {{ $newTickMarkLabel ?: 'Your Label' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t">
                        <button 
                            wire:click="closeCreateModal"
                            type="button" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Cancel
                        </button>
                        <button 
                            wire:click="createNewTickMark"
                            type="button" 
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            Create & Add
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Tick Mark Modal (Same as Create but for editing) -->
        <div x-data="{ show: @entangle('showEditModal') }"
             x-show="show"
             x-cloak
             class="fixed inset-0 z-[60] overflow-y-auto"
             style="display: none;">
            
            <!-- Backdrop with blur -->
            <div class="fixed inset-0 transition-all duration-300" 
                 style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
                 @click="$wire.closeEditModal()"></div>

            <!-- Modal Content -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative rounded-lg shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto border border-gray-200"
                     style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"
                     @click.stop
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-90"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-90">
                    <div class="px-6 pt-5 pb-4 bg-white">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                Edit Quality Indicator
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Update the custom badge</p>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Name -->
                            <div>
                                <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    wire:model="newTickMarkName"
                                    type="text" 
                                    id="edit-name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g., Hot, Featured, Exclusive"
                                >
                                @error('newTickMarkName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Label -->
                            <div>
                                <label for="edit-label" class="block text-sm font-medium text-gray-700 mb-1">
                                    Display Label <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    wire:model="newTickMarkLabel"
                                    type="text" 
                                    id="edit-label"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Label shown to users"
                                >
                                @error('newTickMarkLabel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="edit-description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Description
                                </label>
                                <textarea 
                                    wire:model="newTickMarkDescription"
                                    id="edit-description"
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Brief description of this tick mark"
                                ></textarea>
                            </div>

                            <!-- Icon Picker -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Icon <span class="text-red-500">*</span>
                                </label>
                                <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3">
                                    <div class="grid grid-cols-6 gap-2">
                                        @php
                                            $icons = [
                                                ['value' => 'check-circle', 'label' => 'Check'],
                                                ['value' => 'star', 'label' => 'Star'],
                                                ['value' => 'trending-up', 'label' => 'Trending'],
                                                ['value' => 'crown', 'label' => 'Crown'],
                                                ['value' => 'flame', 'label' => 'Flame'],
                                                ['value' => 'sparkles', 'label' => 'Sparkles'],
                                                ['value' => 'badge-check', 'label' => 'Badge'],
                                                ['value' => 'lightning-bolt', 'label' => 'Lightning'],
                                                ['value' => 'heart', 'label' => 'Heart'],
                                                ['value' => 'shield', 'label' => 'Shield'],
                                                ['value' => 'gift', 'label' => 'Gift'],
                                                ['value' => 'bell', 'label' => 'Bell'],
                                                ['value' => 'bookmark', 'label' => 'Bookmark'],
                                                ['value' => 'flag', 'label' => 'Flag'],
                                                ['value' => 'eye', 'label' => 'Eye'],
                                                ['value' => 'lock', 'label' => 'Lock'],
                                                ['value' => 'unlock', 'label' => 'Unlock'],
                                                ['value' => 'thumbs-up', 'label' => 'Like'],
                                                ['value' => 'rocket', 'label' => 'Rocket'],
                                                ['value' => 'zap', 'label' => 'Zap'],
                                                ['value' => 'award', 'label' => 'Award'],
                                                ['value' => 'trophy', 'label' => 'Trophy'],
                                                ['value' => 'diamond', 'label' => 'Diamond'],
                                                ['value' => 'sun', 'label' => 'Sun'],
                                            ];
                                        @endphp
                                        @foreach($icons as $icon)
                                            <button 
                                                type="button"
                                                wire:click="$set('newTickMarkIcon', '{{ $icon['value'] }}')"
                                                class="flex flex-col items-center justify-center p-2 border-2 rounded-lg transition-all hover:border-blue-300 {{ $newTickMarkIcon === $icon['value'] ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}"
                                                title="{{ $icon['label'] }}"
                                            >
                                                <svg class="w-5 h-5 {{ $newTickMarkIcon === $icon['value'] ? 'text-blue-600' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    {!! (new \App\Modules\Blog\Models\TickMark(['icon' => $icon['value']]))->getIconHtml() !!}
                                                </svg>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Selected: <span class="font-medium">{{ ucfirst(str_replace('-', ' ', $newTickMarkIcon)) }}</span></p>
                            </div>

                            <!-- Custom Color Picker -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Color <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-3">
                                    <input 
                                        type="color" 
                                        wire:model.live="newTickMarkColor"
                                        class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                                    >
                                    <input 
                                        type="text" 
                                        wire:model.live="newTickMarkColor"
                                        placeholder="#3B82F6"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                    >
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Choose any color or enter hex code</p>
                            </div>

                            <!-- Live Preview -->
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <p class="text-xs font-medium text-gray-600 mb-2">Preview:</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" style="background-color: {{ $newTickMarkColor }}; color: {{ strlen($newTickMarkColor) === 7 ? (hexdec(substr($newTickMarkColor, 1, 2)) * 0.299 + hexdec(substr($newTickMarkColor, 3, 2)) * 0.587 + hexdec(substr($newTickMarkColor, 5, 2)) * 0.114 > 128 ? '#000000' : '#FFFFFF') : '#FFFFFF' }};">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        {!! (new \App\Modules\Blog\Models\TickMark(['icon' => $newTickMarkIcon]))->getIconHtml() !!}
                                    </svg>
                                    {{ $newTickMarkLabel ?: 'Your Label' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t">
                        <button 
                            wire:click="closeEditModal"
                            type="button" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Cancel
                        </button>
                        <button 
                            wire:click="updateTickMark"
                            type="button" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
