@props(['sectionKey', 'sectionName', 'description', 'enabled' => '1'])

<div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 mb-6" 
     x-data="{
         enabled: {{ $enabled === '1' ? 'true' : 'false' }},
         toggleSection() {
             console.log('Toggling section: {{ $sectionKey }}', 'enabled:', this.enabled);
             fetch('{{ route('admin.footer-management.toggle-section') }}', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
                 },
                 body: JSON.stringify({ 
                     section_key: '{{ $sectionKey }}', 
                     enabled: this.enabled 
                 })
             })
             .then(response => {
                 if (!response.ok) {
                     throw new Error(`HTTP error! status: ${response.status}`);
                 }
                 return response.json();
             })
             .then(data => {
                 if (data.success) {
                     const message = this.enabled ? 'Section enabled on footer!' : 'Section disabled on footer!';
                     window.dispatchEvent(new CustomEvent('show-toast', { 
                         detail: { message: message, type: 'success' } 
                     }));
                 } else {
                     this.enabled = !this.enabled;
                     window.dispatchEvent(new CustomEvent('show-toast', { 
                         detail: { message: data.message || 'Failed to update section', type: 'error' } 
                     }));
                 }
             })
             .catch(error => {
                 console.error('Toggle Error:', error);
                 this.enabled = !this.enabled;
                 window.dispatchEvent(new CustomEvent('show-toast', { 
                     detail: { message: 'Failed to update section: ' + error.message, type: 'error' } 
                 }));
             });
         }
     }">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               class="sr-only peer"
                               x-model="enabled"
                               @change="toggleSection()">
                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                    <div class="ml-3">
                        <span class="text-sm font-semibold text-gray-700">
                            <span x-show="enabled" class="text-green-600">✓ Visible on Footer</span>
                            <span x-show="!enabled" class="text-gray-500">✗ Hidden from Footer</span>
                        </span>
                    </div>
                </div>

                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900">{{ $sectionName }}</h3>
                    <p class="text-sm text-gray-600">{{ $description }}</p>
                </div>
            </div>
        </div>

        <!-- Status Badge -->
        <div>
            <span x-show="enabled" 
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Enabled
            </span>
            <span x-show="!enabled" 
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Disabled
            </span>
        </div>
    </div>
</div>
