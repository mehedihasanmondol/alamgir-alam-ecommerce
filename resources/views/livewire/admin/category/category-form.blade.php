<div x-data="{ mediaId: @entangle('media_id') }">
    <!-- Image Upload Section -->
    <div>
        <label for="media_id" class="block text-sm font-medium text-gray-700 mb-1">
            Category Image
        </label>
        
        <!-- Hidden input synced with Livewire via Alpine -->
        <input type="hidden" name="media_id" x-model="mediaId">
        
        @if($selectedImage)
            <!-- Selected Image Preview -->
            <div class="mt-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Selected Image</label>
                <div class="relative inline-block">
                    <img src="{{ $selectedImage['thumbnail_url'] }}" 
                         alt="Category Image" 
                         class="h-32 w-32 object-cover rounded-lg border-2 border-gray-300">
                    <button type="button" 
                            wire:click="removeImage"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @else
            <!-- Upload Buttons -->
            <div class="flex gap-3 mt-2">
                <button type="button"
                        onclick="Livewire.dispatch('openMediaLibrary', { field: 'category_image', multiple: false })"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Select from Library
                </button>
                <button type="button"
                        onclick="Livewire.dispatch('openUploader', { field: 'category_image', multiple: false })"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload New Image
                </button>
            </div>
        @endif
        
        <p class="mt-1 text-xs text-gray-500">Select an optimized image from your media library or upload a new one</p>
        @error('media_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Universal Image Uploader Component -->
    <livewire:universal-image-uploader />
</div>
