<div>
    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         x-data="{ show: @entangle('showModal') }"
         x-show="show"
         x-cloak
         style="display: none;">
        
        {{-- Backdrop with blur --}}
        <div class="fixed inset-0 transition-all duration-300" 
             style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
             wire:click="closeModal"></div>

        {{-- Modal Container --}}
        <div class="flex min-h-screen items-center justify-center p-4">
            {{-- Modal Content --}}
            <div class="relative rounded-lg shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col border border-gray-200"
                 style="background-color: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"
                 @click.stop
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-90">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Image Uploader</h3>
                        <button type="button" wire:click="closeModal" 
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Tabs --}}
                    <div class="mt-4 flex space-x-4 border-b">
                        <button type="button"
                            wire:click="switchTab('library')" 
                            class="pb-2 px-1 {{ $activeTab === 'library' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500' }}">
                            Library
                        </button>
                        <button type="button"
                            wire:click="switchTab('upload')" 
                            class="pb-2 px-1 {{ $activeTab === 'upload' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500' }}">
                            Upload
                        </button>
                        <button type="button"
                            wire:click="switchTab('settings')" 
                            class="pb-2 px-1 {{ $activeTab === 'settings' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500' }}">
                            Settings
                        </button>
                    </div>
                </div>

                {{-- Flash Messages --}}
                @if(session()->has('success'))
                <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded">
                    {{ session('success') }}
                </div>
                @endif
                @if(session()->has('error'))
                <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded">
                    {{ session('error') }}
                </div>
                @endif

                {{-- Tab Contents --}}
                <div class="bg-gray-50 px-6 py-6 overflow-y-auto flex-1">
                    {{-- Library Tab --}}
                    @if($activeTab === 'library')
                    <div>
                        {{-- Search & Filters --}}
                        <div class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <input type="text" wire:model.live="search" placeholder="Search images..." 
                                class="px-4 py-2 border rounded-lg">
                            <select wire:model.live="mimeFilter" class="px-4 py-2 border rounded-lg">
                                <option value="">All Types</option>
                                <option value="image">Images</option>
                            </select>
                            <input type="date" wire:model.live="startDate" class="px-4 py-2 border rounded-lg">
                            <input type="date" wire:model.live="endDate" class="px-4 py-2 border rounded-lg">
                        </div>

                        {{-- Image Grid --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-4 max-h-96 overflow-y-auto">
                            @forelse($mediaLibrary as $media)
                            <div 
                                wire:click="toggleMediaSelection({{ $media->id }})"
                                class="relative cursor-pointer group rounded-lg overflow-hidden border-2 
                                {{ in_array($media->id, $selectedMedia) ? 'border-blue-500' : 'border-gray-200' }}">
                                <img src="{{ $media->small_url }}" alt="{{ $media->alt_text }}" 
                                    class="w-full h-32 object-cover">
                                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-50 transition-opacity flex items-center justify-center pointer-events-none">
                                    <div class="opacity-0 group-hover:opacity-100 text-white text-xs text-center p-2 transition-opacity">
                                        <p class="font-medium truncate">{{ $media->original_filename }}</p>
                                        <p>{{ $media->formatted_size }}</p>
                                        <p>{{ $media->width }} × {{ $media->height }}</p>
                                    </div>
                                </div>
                                @if(in_array($media->id, $selectedMedia))
                                <div class="absolute top-2 right-2 bg-blue-500 text-white rounded-full p-1 z-10">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>
                            @empty
                            <div class="col-span-full text-center py-8 text-gray-500">
                                No images found
                            </div>
                            @endforelse
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $mediaLibrary->links() }}
                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-end mt-4">
                            <button type="button" wire:click="selectFromLibrary" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Select {{ count($selectedMedia) }} Image(s)
                            </button>
                        </div>
                    </div>
                    @endif

                    {{-- Upload Tab --}}
                    @if($activeTab === 'upload')
                    <div x-data="{ 
                        dragging: false, 
                        showCropper: false,
                        currentCropIndex: null,
                        croppedImages: {}
                    }"
                    @cropped-image-saved.window="
                        croppedImages[$event.detail.index] = $event.detail.dataURL;
                        console.log('Cropped image saved for index:', $event.detail.index);
                        $wire.saveCroppedImage($event.detail.index, $event.detail.dataURL);
                    ">
                        {{-- Show Dropzone only when no files --}}
                        @if(count($uploadedFiles) === 0)
                        <div 
                            @dragover.prevent="dragging = true"
                            @dragleave.prevent="dragging = false"
                            @drop.prevent="dragging = false"
                            :class="{ 'border-blue-500 bg-blue-50': dragging, 'border-gray-300': !dragging }"
                            class="border-2 border-dashed rounded-lg p-12 text-center transition-colors">
                            <svg class="mx-auto h-16 w-16 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <div class="mt-6">
                                <label class="cursor-pointer">
                                    <span class="text-lg text-blue-600 hover:text-blue-700 font-semibold">Click to upload</span>
                                    <input type="file" wire:model="uploadedFiles" {{ $multiple ? 'multiple' : '' }} 
                                        accept="image/*" class="hidden">
                                </label>
                                <p class="text-gray-500 text-base mt-3">or drag and drop your images here</p>
                                <p class="text-gray-400 text-sm mt-2">PNG, JPG, WEBP up to {{ $maxFileSize }}MB</p>
                                <p class="text-gray-400 text-xs mt-1">Maximum {{ $maxWidth }}×{{ $maxHeight }}px</p>
                            </div>
                        </div>
                        @endif

                        {{-- Preview Uploaded Files --}}
                        @if(count($uploadedFiles) > 0)
                        <div class="mb-4" x-data="{ uploading: false }">
                            <div class="flex items-center justify-between mb-3 px-1">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Preview <span class="text-orange-600">({{ count($uploadedFiles) }})</span>
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Ready to upload • {{ number_format(array_sum(array_map(fn($f) => $f->getSize()/1024, $uploadedFiles)), 0) }} KB total
                                    </p>
                                </div>
                                <button type="button" 
                                    @click="uploading = true" 
                                    wire:click="uploadImages"
                                    :disabled="uploading"
                                    :class="{ 'opacity-75 cursor-not-allowed': uploading }"
                                    class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 font-medium text-sm">
                                    <svg x-show="!uploading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <svg x-show="uploading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="uploading ? 'Uploading...' : 'Upload Now'"></span>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($uploadedFiles as $index => $file)
                                <div class="group bg-white border-2 border-gray-200 rounded-xl overflow-hidden hover:border-blue-400 transition-all duration-200 hover:shadow-lg">
                                    {{-- Image --}}
                                    <div class="aspect-square relative">
                                        <img :src="croppedImages[{{ $index }}] || '{{ $file->temporaryUrl() }}'" 
                                             class="w-full h-full object-cover"
                                             x-data="{ imageIndex: {{ $index }} }">
                                        
                                        {{-- Overlay with buttons (on hover) --}}
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-end justify-center pb-3">
                                            <div class="flex gap-2">
                                                {{-- Edit & Crop Button --}}
                                                <button type="button" 
                                                    @click="$dispatch('open-cropper', { imageUrl: croppedImages[{{ $index }}] || '{{ $file->temporaryUrl() }}', index: {{ $index }} })"
                                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-lg transition-all duration-200 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"></path>
                                                    </svg>
                                                    Edit & Crop
                                                </button>
                                                {{-- Remove Button --}}
                                                <button type="button" 
                                                    wire:click="removeUploadedFile({{ $index }})"
                                                    class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg shadow-lg transition-all duration-200 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- File info below image --}}
                                    <div class="p-3 bg-gray-50 border-t border-gray-200">
                                        <p class="text-sm font-medium text-gray-900 truncate mb-1" title="{{ $file->getClientOriginalName() }}">
                                            {{ $file->getClientOriginalName() }}
                                        </p>
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span>{{ number_format($file->getSize()/1024, 0) }} KB</span>
                                            <span class="text-green-600 font-medium">Ready</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Settings Tab --}}
                    @if($activeTab === 'settings')
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Compression --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Default Compression (%)
                                </label>
                                <input type="number" wire:model="settingsCompression" min="0" max="100" 
                                    class="w-full px-4 py-2 border rounded-lg">
                            </div>

                            {{-- Max File Size --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Max File Size (MB)
                                </label>
                                <input type="number" wire:model="settingsMaxFileSize" min="1" 
                                    class="w-full px-4 py-2 border rounded-lg">
                            </div>

                            {{-- Large Size --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Large Size (Width)
                                </label>
                                <input type="number" wire:model="settingsLargeWidth" 
                                    class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Large Size (Height)
                                </label>
                                <input type="number" wire:model="settingsLargeHeight" 
                                    class="w-full px-4 py-2 border rounded-lg">
                            </div>

                            {{-- Medium Size --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Medium Size (Width)
                                </label>
                                <input type="number" wire:model="settingsMediumWidth" 
                                    class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Medium Size (Height)
                                </label>
                                <input type="number" wire:model="settingsMediumHeight" 
                                    class="w-full px-4 py-2 border rounded-lg">
                            </div>

                            {{-- Small Size --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Small Size (Width)
                                </label>
                                <input type="number" wire:model="settingsSmallWidth" 
                                    class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Small Size (Height)
                                </label>
                                <input type="number" wire:model="settingsSmallHeight" 
                                    class="w-full px-4 py-2 border rounded-lg">
                            </div>

                            {{-- Max Dimensions --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Max Width (px)
                                </label>
                                <input type="number" wire:model="settingsMaxWidth" 
                                    class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Max Height (px)
                                </label>
                                <input type="number" wire:model="settingsMaxHeight" 
                                    class="w-full px-4 py-2 border rounded-lg">
                            </div>

                            {{-- Enable Optimizer --}}
                            <div class="col-span-2">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="settingsEnableOptimizer" 
                                        class="rounded border-gray-300 text-blue-600">
                                    <span class="ml-2 text-sm font-medium text-gray-700">
                                        Enable Image Optimizer
                                    </span>
                                </label>
                            </div>
                        </div>

                        {{-- Save Button --}}
                        <div class="flex justify-end">
                            <button type="button" wire:click="saveSettings" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Save Settings
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
    
    {{-- Include Cropper Modal Component --}}
    <x-cropper-modal />
</div>
