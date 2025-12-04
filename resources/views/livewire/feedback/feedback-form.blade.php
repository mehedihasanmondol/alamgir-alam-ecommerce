<div class="bg-white rounded-lg border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Share Your Feedback</h3>
    
    

    <form wire:submit.prevent="submit">
        {{-- Customer Information --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Your Name <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model="customer_name" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Enter your name">
                @error('customer_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email @if(\App\Models\SiteSetting::get('feedback_email_required', '0') === '1')<span class="text-red-500">*</span>@else<span class="text-gray-400">(Optional)</span>@endif
                </label>
                <input type="email" wire:model="customer_email"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="your@email.com">
                @error('customer_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Mobile Number <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model="customer_mobile"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="01XXXXXXXXX">
                @error('customer_mobile') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Address (Optional)
                </label>
                <input type="text" wire:model="customer_address"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Your address">
                @error('customer_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Rating --}}
        @if(\App\Models\SiteSetting::get('feedback_rating_enabled', '1') === '1')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Rating <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center space-x-1">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" wire:click="setRating({{ $i }})" class="focus:outline-none">
                        <svg class="w-8 h-8 {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 transition-colors" 
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                @endfor
            </div>
            @error('rating') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        @endif

        {{-- Feedback --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Your Feedback <span class="text-red-500">*</span>
            </label>
            <textarea wire:model="feedback" rows="5"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Share your thoughts and experience..."></textarea>
            <p class="mt-1 text-sm text-gray-500">Minimum 10 characters</p>
            @error('feedback') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Image Upload --}}
        @if($showImages)
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Add Photos (Optional)
            </label>
            <div class="flex items-center space-x-4">
                <label class="cursor-pointer">
                    <input type="file" wire:model="images" multiple accept="image/*" class="hidden">
                    <div class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Choose Images
                    </div>
                </label>
                <span class="text-sm text-gray-500">Max 5 images, 5MB each</span>
            </div>

            @if($images)
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($images as $index => $image)
                        <div class="relative">
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                            <button type="button" wire:click="removeImage({{ $index }})"
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full hover:bg-red-600 focus:outline-none flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            @error('images.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        @endif
        
        @if($successMessage)
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ $successMessage }}
            </div>
        @endif

        @if($errorMessage)
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ $errorMessage }}
            </div>
        @endif

        {{-- Submit Button --}}
        <button type="submit" 
                class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50"
                wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="submit">Submit Feedback</span>
            <span wire:loading wire:target="submit">Submitting...</span>
        </button>
    </form>
</div>
