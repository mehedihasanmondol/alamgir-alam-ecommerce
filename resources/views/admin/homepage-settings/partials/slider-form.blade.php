<div class="space-y-4">
    <!-- Title -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Title <span class="text-red-500">*</span>
        </label>
        <input type="text" 
               name="title" 
               value="{{ old('title', $slider->title ?? '') }}" 
               required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('title')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Subtitle -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Subtitle
        </label>
        <input type="text" 
               name="subtitle" 
               value="{{ old('subtitle', $slider->subtitle ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('subtitle')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Image Upload with Preview -->
    <div x-data="{ 
        imagePreview: '{{ isset($slider) && $slider->image ? $slider->image_url : '' }}',
        fileName: '',
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.fileName = file.name;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        clearPreview() {
            this.imagePreview = '';
            this.fileName = '';
            $refs.imageInput.value = '';
        }
    }">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Slider Image <span class="text-red-500">*</span>
        </label>
        
        <!-- Image Preview -->
        <div x-show="imagePreview" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             class="mb-4">
            <div class="relative group">
                <img :src="imagePreview" 
                     alt="Preview" 
                     class="w-full h-48 object-cover rounded-lg border-2 border-gray-300 shadow-sm">
                
                <!-- Image Overlay with Actions -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-black/0 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-end justify-center pb-4">
                    <button type="button"
                            @click="clearPreview()"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium shadow-lg transform transition-transform hover:scale-105">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Remove Image
                    </button>
                </div>
            </div>
            
            <!-- File Name Display -->
            <p x-show="fileName" class="text-xs text-gray-600 mt-2 flex items-center">
                <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span x-text="fileName"></span>
            </p>
        </div>
        
        <!-- Upload Button/Input -->
        <div class="relative">
            <input type="file" 
                   x-ref="imageInput"
                   name="image" 
                   accept="image/*"
                   @change="previewImage($event)"
                   {{ !isset($slider) ? 'required' : '' }}
                   class="block w-full text-sm text-gray-500 
                          file:mr-4 file:py-2.5 file:px-4 
                          file:rounded-lg file:border-0 
                          file:text-sm file:font-semibold 
                          file:bg-blue-50 file:text-blue-700 
                          hover:file:bg-blue-100
                          file:transition-colors file:duration-200
                          cursor-pointer border border-gray-300 rounded-lg
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        
        <div class="mt-2 flex items-start gap-2">
            <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-xs text-gray-500">
                <p class="font-medium">Recommended specifications:</p>
                <ul class="list-disc list-inside mt-1 space-y-0.5">
                    <li>Size: 1920x400px (16:9 aspect ratio)</li>
                    <li>Format: JPG, PNG, or WebP</li>
                    <li>Max file size: 2MB</li>
                </ul>
            </div>
        </div>
        
        @error('image')
            <div class="mt-2 flex items-center gap-2 text-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-xs font-medium">{{ $message }}</p>
            </div>
        @enderror
    </div>

    <!-- Link -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Link URL
        </label>
        <input type="url" 
               name="link" 
               value="{{ old('link', $slider->link ?? '') }}"
               placeholder="https://example.com"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('link')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Button Text -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Button Text
        </label>
        <input type="text" 
               name="button_text" 
               value="{{ old('button_text', $slider->button_text ?? '') }}"
               placeholder="Shop Now"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        @error('button_text')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Order -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Display Order
        </label>
        <input type="number" 
               name="order" 
               value="{{ old('order', $slider->order ?? 0) }}"
               min="0"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
        @error('order')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Active Status -->
    <div>
        <label class="flex items-center">
            <input type="checkbox" 
                   name="is_active" 
                   value="1"
                   {{ old('is_active', $slider->is_active ?? true) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-700">Active (show on homepage)</span>
        </label>
    </div>
</div>
