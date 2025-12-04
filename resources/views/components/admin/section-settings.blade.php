@props(['sectionEnabled', 'sectionTitle', 'toggleRoute', 'updateTitleRoute', 'sectionName'])

<div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6" x-data="{ 
    enabled: {{ $sectionEnabled === '1' ? 'true' : 'false' }}, 
    title: '{{ addslashes($sectionTitle) }}',
    editingTitle: false,
    tempTitle: '{{ addslashes($sectionTitle) }}',
    toggleSection() {
        fetch('{{ $toggleRoute }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ enabled: this.enabled })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const message = this.enabled ? 'Section enabled successfully!' : 'Section disabled successfully!';
                window.dispatchEvent(new CustomEvent('show-toast', { 
                    detail: { message: message, type: 'success' } 
                }));
            } else {
                // Revert toggle on failure
                this.enabled = !this.enabled;
                window.dispatchEvent(new CustomEvent('show-toast', { 
                    detail: { message: 'Failed to update section status', type: 'error' } 
                }));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert toggle on error
            this.enabled = !this.enabled;
            window.dispatchEvent(new CustomEvent('show-toast', { 
                detail: { message: 'Failed to update section status', type: 'error' } 
            }));
        });
    },
    updateTitle() {
        if (this.tempTitle.trim() === '') {
            window.dispatchEvent(new CustomEvent('show-toast', { 
                detail: { message: 'Title cannot be empty', type: 'error' } 
            }));
            return;
        }

        fetch('{{ $updateTitleRoute }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ title: this.tempTitle })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.title = this.tempTitle;
                this.editingTitle = false;
                window.dispatchEvent(new CustomEvent('show-toast', { 
                    detail: { message: 'Section title updated successfully!', type: 'success' } 
                }));
            } else {
                window.dispatchEvent(new CustomEvent('show-toast', { 
                    detail: { message: 'Failed to update section title', type: 'error' } 
                }));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.dispatchEvent(new CustomEvent('show-toast', { 
                detail: { message: 'Failed to update section title', type: 'error' } 
            }));
        });
    }
}">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <div class="flex items-center space-x-4">
                <!-- Toggle Switch -->
                <div class="flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               class="sr-only peer"
                               x-model="enabled"
                               @change="toggleSection()">
                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                    <span class="ml-3 text-sm font-semibold text-gray-700">
                        <span x-show="enabled" class="text-green-600">Section Enabled</span>
                        <span x-show="!enabled" class="text-gray-500">Section Disabled</span>
                    </span>
                </div>

                <!-- Title Display/Edit -->
                <div class="flex-1">
                    <div x-show="!editingTitle" class="flex items-center space-x-2">
                        <h3 class="text-lg font-bold text-gray-900" x-text="title"></h3>
                        <button 
                            @click="editingTitle = true; tempTitle = title"
                            class="text-blue-600 hover:text-blue-800 p-1 rounded-md hover:bg-blue-100 transition"
                            title="Edit section title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div x-show="editingTitle" class="flex items-center space-x-2" x-cloak>
                        <input 
                            type="text" 
                            x-model="tempTitle"
                            @keydown.enter="updateTitle()"
                            @keydown.escape="editingTitle = false"
                            class="px-3 py-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Section title">
                        <button 
                            @click="updateTitle()"
                            class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm">
                            Save
                        </button>
                        <button 
                            @click="editingTitle = false"
                            class="px-3 py-1 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            
            <p class="text-sm text-gray-600 mt-2">
                <span x-show="enabled">This section will be displayed on the homepage.</span>
                <span x-show="!enabled">This section is hidden from the homepage.</span>
                Click the pencil icon to edit the section title.
            </p>
        </div>

        <!-- Status Badge -->
        <div>
            <span x-show="enabled" 
                  class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Visible on Homepage
            </span>
            <span x-show="!enabled" 
                  class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                </svg>
                Hidden from Homepage
            </span>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
