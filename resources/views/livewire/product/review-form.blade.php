<div class="bg-white rounded-lg border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-2">Write a Review</h3>
    
    <!-- Purchase Requirement Notice -->
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-start">
        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1">
            <p class="text-sm text-blue-800">
                <strong>Note:</strong> Only customers who have purchased this product can write a review.
            </p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="submit">
        <!-- Rating -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Rating <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center space-x-1">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" 
                            wire:click="setRating({{ $i }})"
                            class="focus:outline-none">
                        <svg class="w-8 h-8 {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 transition-colors" 
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                @endfor
            </div>
            @error('rating')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Title -->
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                Review Title (Optional)
            </label>
            <input type="text" 
                   id="title"
                   wire:model="title"
                   placeholder="Summarize your experience"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Review Text -->
        <div class="mb-4">
            <label for="review" class="block text-sm font-medium text-gray-700 mb-2">
                Your Review <span class="text-red-500">*</span>
            </label>
            <textarea id="review"
                      wire:model="review"
                      rows="5"
                      placeholder="Share your thoughts about this product..."
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            <p class="mt-1 text-sm text-gray-500">Minimum 10 characters</p>
            @error('review')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Image Upload -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Add Photos (Optional)
            </label>
            <div class="flex items-center space-x-4">
                <label class="cursor-pointer">
                    <input type="file" 
                           wire:model="images"
                           multiple
                           accept="image/*"
                           class="hidden">
                    <div class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Choose Images
                    </div>
                </label>
                <span class="text-sm text-gray-500">Max 5 images, 2MB each</span>
            </div>

            <!-- Image Preview -->
            @if($images)
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($images as $index => $image)
                        <div class="relative">
                            <img src="{{ $image->temporaryUrl() }}" 
                                 alt="Preview" 
                                 class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                            <button type="button"
                                    wire:click="removeImage({{ $index }})"
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full hover:bg-red-600 focus:outline-none">
                                <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            @error('images.*')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Guest Info -->
        @guest
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="reviewerName" class="block text-sm font-medium text-gray-700 mb-2">
                        Your Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="reviewerName"
                           wire:model="reviewerName"
                           placeholder="Enter your name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('reviewerName')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="reviewerEmail" class="block text-sm font-medium text-gray-700 mb-2">
                        Your Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="reviewerEmail"
                           wire:model="reviewerEmail"
                           placeholder="Enter your email"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('reviewerEmail')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endguest

        <!-- Inline Messages (near submit button) -->
        @if($errorMessage)
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-start">
                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-800">{{ $errorMessage }}</p>
                </div>
            </div>
        @endif

        @if($successMessage)
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-start">
                <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-800">{{ $successMessage }}</p>
                </div>
            </div>
        @endif

        <!-- Submit Button -->
        <div class="flex items-center justify-end">
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <span wire:loading.remove>Submit Review</span>
                <span wire:loading class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Submitting...
                </span>
            </button>
        </div>
    </form>
</div>
