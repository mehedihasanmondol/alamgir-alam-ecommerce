<!-- Confirmation Modal Component -->
<div x-data="{ show: false, title: '', message: '', onConfirm: null }" 
     @confirm-modal.window="
        show = true; 
        title = $event.detail.title || 'Confirm Action'; 
        message = $event.detail.message || 'Are you sure?';
        onConfirm = $event.detail.onConfirm;
     "
     x-show="show"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop with blur -->
    <div class="fixed inset-0 transition-all duration-300" 
         style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
         @click="show = false"></div>
    
    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative rounded-lg shadow-2xl max-w-md w-full p-6 border border-gray-200"
             style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"
             @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90">
            
            <!-- Icon -->
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <!-- Title -->
            <h3 class="text-lg font-bold text-gray-900 text-center mb-2" x-text="title"></h3>
            
            <!-- Message -->
            <p class="text-sm text-gray-600 text-center mb-6" x-text="message"></p>
            
            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3">
                <button @click="show = false" 
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancel
                </button>
                <button @click="if(onConfirm) onConfirm(); show = false;" 
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
