{{-- Toast Notification Component --}}
<div x-data="{ 
    show: false, 
    message: '', 
    type: 'success',
    init() {
        window.addEventListener('show-toast', event => {
            this.message = event.detail.message;
            this.type = event.detail.type || 'success';
            this.show = true;
            setTimeout(() => { this.show = false; }, 3000);
        });
    }
}" 
x-show="show"
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0 transform translate-y-2"
x-transition:enter-end="opacity-100 transform translate-y-0"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0"
class="fixed top-4 right-4 z-50 max-w-sm"
style="display: none;">
    <div :class="{
        'bg-green-50 border-green-200 text-green-800': type === 'success',
        'bg-red-50 border-red-200 text-red-800': type === 'error',
        'bg-blue-50 border-blue-200 text-blue-800': type === 'info',
        'bg-yellow-50 border-yellow-200 text-yellow-800': type === 'warning'
    }" class="border rounded-lg shadow-lg p-4 flex items-start gap-3">
        <!-- Icon -->
        <div class="flex-shrink-0">
            <svg x-show="type === 'success'" class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg x-show="type === 'error'" class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg x-show="type === 'info'" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg x-show="type === 'warning'" class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <!-- Message -->
        <div class="flex-1">
            <p class="text-sm font-medium" x-text="message"></p>
        </div>
        
        <!-- Close Button -->
        <button @click="show = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

@if(session('success') || session('error') || session('info') || session('warning'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            window.dispatchEvent(new CustomEvent('show-toast', { 
                detail: { message: '{{ session('success') }}', type: 'success' } 
            }));
        @endif
        @if(session('error'))
            window.dispatchEvent(new CustomEvent('show-toast', { 
                detail: { message: '{{ session('error') }}', type: 'error' } 
            }));
        @endif
        @if(session('info'))
            window.dispatchEvent(new CustomEvent('show-toast', { 
                detail: { message: '{{ session('info') }}', type: 'info' } 
            }));
        @endif
        @if(session('warning'))
            window.dispatchEvent(new CustomEvent('show-toast', { 
                detail: { message: '{{ session('warning') }}', type: 'warning' } 
            }));
        @endif
    });
</script>
@endif
