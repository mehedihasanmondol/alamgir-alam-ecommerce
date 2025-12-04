<div class="space-y-6" x-data="imageUploader({{ $maxUploadSize }})">
    {{-- Upload Section --}}
    <div class="bg-white rounded-lg border-2 border-dashed border-gray-300 p-6">
        <div class="text-center">
            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Upload Product Images</h3>
            <p class="text-sm text-gray-500 mb-4">Images will be converted to WebP format. Max size: <strong>{{ $maxUploadSizeFormatted }}</strong></p>
            
            <div class="flex items-center justify-center">
                <label class="cursor-pointer">
                    <span class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-images mr-2"></i>
                        Choose Images
                    </span>
                    <input type="file" 
                           wire:model="images" 
                           multiple 
                           accept="image/*"
                           @change="validateFiles($event)"
                           class="hidden">
                </label>
            </div>

            @if($images)
            <div class="mt-4">
                <p class="text-sm text-gray-600 mb-2">{{ count($images) }} image(s) selected</p>
                <button wire:click="uploadImages" 
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50">
                    <span wire:loading.remove wire:target="uploadImages">
                        <i class="fas fa-upload mr-2"></i> Upload Images
                    </span>
                    <span wire:loading wire:target="uploadImages">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Uploading...
                    </span>
                </button>
            </div>
            @endif

            <div wire:loading wire:target="images" class="mt-4">
                <p class="text-sm text-blue-600">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Processing images...
                </p>
            </div>

            @error('images.*')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror

            {{-- Frontend validation error --}}
            <div x-show="validationError" x-cloak class="mt-4">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-800 text-sm" x-text="validationError"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Existing Images Gallery --}}
    @if(!empty($existingImages))
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Images ({{ count($existingImages) }})</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($existingImages as $image)
            <div class="relative group">
                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 border-2 {{ $image['is_primary'] ? 'border-blue-500' : 'border-gray-200' }}">
                    <img src="{{ Storage::url($image['image_path']) }}" 
                         alt="Product image"
                         class="w-full h-full object-cover">
                </div>

                {{-- Primary Badge --}}
                @if($image['is_primary'])
                <div class="absolute top-2 left-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-600 text-white">
                        <i class="fas fa-star mr-1"></i> Primary
                    </span>
                </div>
                @endif

                {{-- Actions --}}
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <div class="flex gap-2">
                        @if(!$image['is_primary'])
                        <button wire:click="setPrimary({{ $image['id'] }})"
                                class="p-2 bg-white rounded-full text-blue-600 hover:bg-blue-50 transition-colors"
                                title="Set as primary">
                            <i class="fas fa-star"></i>
                        </button>
                        @endif
                        
                        <button type="button"
                                onclick="window.dispatchEvent(new CustomEvent('confirm-modal', { 
                                    detail: { 
                                        title: 'Delete Image', 
                                        message: 'Are you sure you want to delete this image?',
                                        onConfirm: () => @this.call('deleteImage', {{ $image['id'] }})
                                    } 
                                }))"
                                class="p-2 bg-white rounded-full text-red-600 hover:bg-red-50 transition-colors"
                                title="Delete image">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                {{-- Sort Order --}}
                <div class="absolute bottom-2 right-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-medium bg-gray-900 bg-opacity-75 text-white">
                        {{ $image['sort_order'] }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Tip:</strong> The primary image will be displayed as the main product image. Click the star icon to set a different primary image.
            </p>
        </div>
    </div>
    @else
    <div class="bg-gray-50 rounded-lg border border-gray-200 p-8 text-center">
        <i class="fas fa-image text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">No images uploaded yet. Upload your first product image above.</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
function imageUploader(maxUploadSize) {
    return {
        validationError: '',
        maxUploadSize: maxUploadSize,

        validateFiles(event) {
            this.validationError = '';
            const files = event.target.files;
            
            if (!files || files.length === 0) {
                return true;
            }

            let hasError = false;
            let errorMessages = [];

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // Check file size
                if (file.size > this.maxUploadSize) {
                    hasError = true;
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    const maxSizeMB = (this.maxUploadSize / (1024 * 1024)).toFixed(2);
                    errorMessages.push(`File "${file.name}" (${fileSizeMB} MB) exceeds maximum size of ${maxSizeMB} MB`);
                }

                // Check if it's an image
                if (!file.type.startsWith('image/')) {
                    hasError = true;
                    errorMessages.push(`File "${file.name}" is not an image`);
                }
            }

            if (hasError) {
                this.validationError = errorMessages.join('. ');
                event.target.value = ''; // Clear the input
                return false;
            }

            return true;
        },

        formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
    }
}
</script>
@endpush
