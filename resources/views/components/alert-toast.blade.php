<!-- Alert Toast Component -->
<div x-data="{ 
        show: false, 
        type: 'success', 
        message: '',
        showToast(detail) {
            this.type = detail.type || 'success';
            this.message = detail.message || '';
            this.show = true;
            setTimeout(() => { this.show = false; }, 5000);
        }
     }" 
     @alert-toast.window="showToast($event.detail)"
     @show-toast.window="showToast($event.detail)"
     x-show="show"
     x-cloak
     class="fixed top-20 right-4 z-50 max-w-md"
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-x-full"
     x-transition:enter-end="opacity-100 transform translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-x-0"
     x-transition:leave-end="opacity-0 transform translate-x-full">
    
    <div :class="{
            'border-green-500 text-green-800': type === 'success',
            'border-red-500 text-red-800': type === 'error',
            'border-blue-500 text-blue-800': type === 'info',
            'border-yellow-500 text-yellow-800': type === 'warning'
         }"
         :style="{
            'background-color': type === 'success' ? 'rgba(240, 253, 244, 0.95)' : 
                               type === 'error' ? 'rgba(254, 242, 242, 0.95)' : 
                               type === 'info' ? 'rgba(239, 246, 255, 0.95)' : 
                               'rgba(254, 252, 232, 0.95)',
            'backdrop-filter': 'blur(10px)',
            '-webkit-backdrop-filter': 'blur(10px)'
         }"
         class="border-l-4 rounded-lg p-4 shadow-2xl border border-gray-200">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <!-- Success Icon -->
                <svg x-show="type === 'success'" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <!-- Error Icon -->
                <svg x-show="type === 'error'" class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <!-- Info Icon -->
                <svg x-show="type === 'info'" class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <!-- Warning Icon -->
                <svg x-show="type === 'warning'" class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium" x-text="message"></p>
            </div>
            <div class="ml-3 flex-shrink-0">
                <button @click="show = false" 
                        :class="{
                            'text-green-500 hover:text-green-700': type === 'success',
                            'text-red-500 hover:text-red-700': type === 'error',
                            'text-blue-500 hover:text-blue-700': type === 'info',
                            'text-yellow-500 hover:text-yellow-700': type === 'warning'
                        }"
                        class="inline-flex focus:outline-none transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
