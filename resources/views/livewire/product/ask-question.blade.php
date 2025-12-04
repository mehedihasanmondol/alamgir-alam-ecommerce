<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <!-- Ask Question Button -->
    <button wire:click="openModal" 
            class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
        Ask a Question
    </button>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 overflow-y-auto" style="z-index: 9999;">
            <!-- Background overlay with blur -->
            <div class="fixed inset-0 transition-opacity" 
                 style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
                 wire:click="closeModal"></div>

            <!-- Modal container -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <!-- Modal panel -->
                <div class="relative rounded-lg shadow-xl max-w-2xl w-full border border-gray-200"
                     style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                    <div class="bg-white px-6 pt-5 pb-4 rounded-t-lg">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Ask a Question</h3>
                            <button wire:click="closeModal" 
                                    class="text-gray-400 hover:text-gray-500 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Form -->
                    <form wire:submit.prevent="submitQuestion" class="px-6 pb-6">
                        <!-- Question Field -->
                        <div class="mb-4">
                            <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                                Your Question <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="question" 
                                      id="question"
                                      rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                      placeholder="What would you like to know about this product?"></textarea>
                            @error('question')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Minimum 10 characters, maximum 500 characters</p>
                        </div>

                        @guest
                            <!-- Name Field (for guests) -->
                            <div class="mb-4">
                                <label for="userName" class="block text-sm font-medium text-gray-700 mb-2">
                                    Your Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       wire:model="userName" 
                                       id="userName"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter your name">
                                @error('userName')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Field (for guests) -->
                            <div class="mb-4">
                                <label for="userEmail" class="block text-sm font-medium text-gray-700 mb-2">
                                    Your Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       wire:model="userEmail" 
                                       id="userEmail"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter your email">
                                @error('userEmail')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">We'll notify you when your question is answered</p>
                            </div>
                        @endguest

                        <!-- Submit Error -->
                        @error('submit')
                            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-800">
                                    <strong>Error:</strong> {{ $message }}
                                </p>
                            </div>
                        @enderror

                        <!-- Disclaimer -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>Note:</strong> Your question will be reviewed before being published. Please ensure it's relevant to the product and follows our community guidelines.
                            </p>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center space-x-3 mt-6">
                            <button type="button" 
                                    wire:click="closeModal"
                                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                Submit Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
