<div>
    <!-- Construction Mode Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Site Construction Mode
                </h2>
                <p class="text-sm text-gray-600 mt-1">Enable maintenance mode to show a custom message to visitors</p>
            </div>
        </div>

        <!-- Success Message -->
        @if($successMessage)
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-start" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <p class="text-green-800 font-medium">{{ $successMessage }}</p>
                </div>
                <button @click="show = false" class="text-green-500 hover:text-green-700">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Maintenance Mode Toggle -->
        <div class="mb-8 {{ $maintenanceMode ? 'bg-yellow-50 border-yellow-300' : 'bg-gray-50 border-gray-200' }} border-2 rounded-xl p-6 transition-colors">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold {{ $maintenanceMode ? 'text-yellow-900' : 'text-gray-900' }} flex items-center">
                        @if($maintenanceMode)
                            <svg class="w-6 h-6 text-yellow-600 mr-2 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                        Maintenance Mode
                    </h3>
                    <p class="text-sm {{ $maintenanceMode ? 'text-yellow-700' : 'text-gray-600' }} mt-1">
                        @if($maintenanceMode)
                            <span class="font-semibold">Active:</span> Non-admin users will see the maintenance page
                        @else
                            Currently disabled. Site is accessible to all visitors
                        @endif
                    </p>
                </div>
                
                <!-- Toggle Switch -->
                <div class="ml-4">
                    <button 
                        wire:click="toggleMaintenanceMode"
                        wire:loading.attr="disabled"
                        type="button" 
                        class="{{ $maintenanceMode ? 'bg-yellow-600' : 'bg-gray-300' }} relative inline-flex h-8 w-14 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 disabled:opacity-50"
                    >
                        <span class="sr-only">Toggle maintenance mode</span>
                        <span 
                            class="{{ $maintenanceMode ? 'translate-x-6' : 'translate-x-0' }} pointer-events-none relative inline-block h-7 w-7 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        >
                            <span wire:loading wire:target="toggleMaintenanceMode" class="absolute inset-0 flex items-center justify-center">
                                <svg class="animate-spin h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Settings Form -->
        <form wire:submit="updateSettings">
            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="maintenanceTitle" class="block text-sm font-medium text-gray-900 mb-2">
                        Maintenance Page Title *
                    </label>
                    <input 
                        type="text" 
                        id="maintenanceTitle"
                        wire:model="maintenanceTitle"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('maintenanceTitle') border-red-500 @enderror"
                        placeholder="We'll be back soon!"
                    >
                    @error('maintenanceTitle')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div>
                    <label for="maintenanceMessage" class="block text-sm font-medium text-gray-900 mb-2">
                        Maintenance Message *
                    </label>
                    <textarea 
                        id="maintenanceMessage"
                        wire:model="maintenanceMessage"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('maintenanceMessage') border-red-500 @enderror"
                        placeholder="Sorry for the inconvenience. We're performing some maintenance..."
                    ></textarea>
                    @error('maintenanceMessage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-2">
                        Maintenance Page Image (Optional)
                    </label>
                    
                    @if($currentImage)
                        <div class="mb-4 relative inline-block">
                            <img src="{{ asset('storage/' . $currentImage) }}" alt="Maintenance image" class="h-32 w-auto rounded-lg shadow-sm border border-gray-200">
                            <button 
                                type="button"
                                wire:click="removeImage"
                                class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1.5 hover:bg-red-700 transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                    
                    <div class="mt-2">
                        <input 
                            type="file" 
                            wire:model="maintenanceImage"
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                        >
                        <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                    </div>
                    
                    @error('maintenanceImage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <div wire:loading wire:target="maintenanceImage" class="mt-2">
                        <div class="flex items-center text-sm text-indigo-600">
                            <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading...
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button 
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="updateSettings"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span wire:loading.remove wire:target="updateSettings">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Settings
                        </span>
                        <span wire:loading wire:target="updateSettings" class="flex items-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
