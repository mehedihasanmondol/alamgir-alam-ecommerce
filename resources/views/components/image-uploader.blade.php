@props([
    'multiple' => false,
    'disk' => 'public',
    'maxFileSize' => 5,
    'maxWidth' => 4000,
    'maxHeight' => 4000,
    'preserveOriginal' => false,
    'defaultCompression' => 70,
    'libraryScope' => 'global',
    'targetField' => null,
    'previewUrl' => null,
    'previewAlt' => 'Preview',
    'inputName' => null,
    'value' => null,
])

<div x-data="{
        previewImage: @js($previewUrl),
        previewAlt: @js($previewAlt),
        mediaId: @js($value),
        targetField: @js($targetField),
        hasInput: @js($inputName ? true : false),
        
        init() {
            // Set initial value to hidden input if exists
            if (this.hasInput && this.$refs.mediaInput) {
                this.$refs.mediaInput.value = this.mediaId || '';
            }
            
            // Listen for image upload event
            window.addEventListener('imageUploaded', (event) => {
                if (event.detail && event.detail.field === this.targetField && event.detail.media && event.detail.media.length > 0) {
                    this.previewImage = event.detail.media[0].large_url;
                    this.mediaId = event.detail.media[0].id;
                    
                    // Update hidden input if exists
                    if (this.hasInput && this.$refs.mediaInput) {
                        this.$refs.mediaInput.value = this.mediaId;
                    }
                    
                    // Dispatch custom event for media upload with specific field
                    window.dispatchEvent(new CustomEvent('media-uploaded-' + this.targetField, {
                        detail: { mediaId: this.mediaId }
                    }));
                    
                    // Emit to parent component
                    this.$dispatch('image-updated', event.detail);
                }
            });
            
            // Listen for image selection from library
            window.addEventListener('imageSelected', (event) => {
                if (event.detail && event.detail.field === this.targetField && event.detail.media && event.detail.media.length > 0) {
                    this.previewImage = event.detail.media[0].large_url;
                    this.mediaId = event.detail.media[0].id;
                    
                    // Update hidden input if exists
                    if (this.hasInput && this.$refs.mediaInput) {
                        this.$refs.mediaInput.value = this.mediaId;
                    }
                    
                    // Dispatch custom event for media selection with specific field
                    window.dispatchEvent(new CustomEvent('media-uploaded-' + this.targetField, {
                        detail: { mediaId: this.mediaId }
                    }));
                    
                    // Emit to parent component
                    this.$dispatch('image-updated', event.detail);
                }
            });
        },
        
        removeImage() {
            this.previewImage = null;
            this.mediaId = null;
            
            // Clear hidden input if exists
            if (this.hasInput && this.$refs.mediaInput) {
                this.$refs.mediaInput.value = '';
            }
            
            this.$dispatch('image-removed');
        }
    }" class="relative">
    {{-- Trigger/Preview Area --}}
    <div 
        @click="$dispatch('open-uploader-modal', { field: @js($targetField) })"
        class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-blue-500 transition"
        x-show="!previewImage">
        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        <p class="mt-2 text-sm text-gray-600">Click to upload image</p>
        <p class="text-xs text-gray-400 mt-1">Max {{ $maxFileSize }}MB</p>
    </div>

    {{-- Preview with Controls --}}
    <div 
        x-show="previewImage"
        class="relative rounded-lg overflow-hidden border-2 border-gray-200 group"
        style="max-width: 300px;">
        <img :src="previewImage" :alt="previewAlt" class="w-full h-48 object-cover">
        
        {{-- Overlay Controls --}}
        <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-50 transition-opacity flex items-center justify-center gap-4 pointer-events-none">
            <button type="button"
                @click.stop="$dispatch('open-uploader-modal', { field: @js($targetField) })"
                class="opacity-0 group-hover:opacity-100 transition-opacity px-4 py-2 bg-white text-gray-700 rounded-lg text-sm font-medium pointer-events-auto">
                Replace
            </button>
            <button type="button"
                @click.stop="removeImage()"
                class="opacity-0 group-hover:opacity-100 transition-opacity px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium pointer-events-auto">
                Remove
            </button>
        </div>
        
        {{-- Remove Button (Always Visible) --}}
        <button type="button"
            @click.stop="removeImage()"
            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 z-10">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"></path>
            </svg>
        </button>
    </div>

    {{-- Hidden input to store media ID --}}
    @if($inputName)
    <input type="hidden" 
           name="{{ $inputName }}" 
           x-ref="mediaInput"
           :value="mediaId || ''">
    @endif
    
    {{-- Livewire Component --}}
    <livewire:universal-image-uploader 
        wire:key="uploader-{{ $targetField }}"
        :multiple="$multiple"
        :disk="$disk"
        :max-file-size="$maxFileSize"
        :max-width="$maxWidth"
        :max-height="$maxHeight"
        :preserve-original="$preserveOriginal"
        :default-compression="$defaultCompression"
        :library-scope="$libraryScope"
        :target-field="$targetField"
    />
</div>
