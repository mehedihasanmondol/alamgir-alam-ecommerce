<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden hover:shadow-md transition-shadow duration-200">
    <!-- Enhanced Group Header -->
    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <!-- Icon based on group -->
                @php
                    $icons = [
                        'general' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>',
                        'featured' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>',
                        'banner' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h3a1 1 0 011 1v2a1 1 0 01-1 1h-1v10a2 2 0 01-2 2H7a2 2 0 01-2-2V8H4a1 1 0 01-1-1V5a1 1 0 011-1h3z"></path>',
                        'top_header' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>',
                    ];
                    $icon = $icons[$group] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>';
                @endphp
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $icon !!}
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 capitalize">
                        {{ str_replace('_', ' ', $group) }} Settings
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">{{ count($groupSettings) }} setting(s)</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs text-gray-500 font-medium">Last updated: {{ now()->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <form wire:submit.prevent="save">
        <!-- Group Settings -->
        <div class="p-6 space-y-6 bg-gray-50">
            @foreach($groupSettings as $setting)
                <div class="setting-item bg-white p-5 rounded-lg border border-gray-200 hover:border-green-300 transition-colors">
                    <label class="block text-sm font-semibold text-gray-800 mb-3">
                        <div class="flex items-center justify-between">
                            <span>{{ $setting['description'] ?? ucfirst(str_replace('_', ' ', $setting['key'])) }}</span>
                            @if($setting['type'] === 'image')
                                <span class="text-xs font-normal px-2 py-1 bg-purple-100 text-purple-700 rounded">Image Upload</span>
                            @elseif($setting['type'] === 'boolean')
                                <span class="text-xs font-normal px-2 py-1 bg-green-100 text-green-700 rounded">Toggle</span>
                            @elseif($setting['type'] === 'textarea')
                                <span class="text-xs font-normal px-2 py-1 bg-blue-100 text-blue-700 rounded">Long Text</span>
                            @else
                                <span class="text-xs font-normal px-2 py-1 bg-gray-100 text-gray-700 rounded">Text</span>
                            @endif
                        </div>
                        @if($setting['description'])
                            <span class="block text-xs text-gray-500 font-normal mt-2 leading-relaxed">{{ $setting['description'] }}</span>
                        @endif
                    </label>

                    @if($setting['type'] === 'text')
                        <input 
                            type="text" 
                            wire:model="settings.{{ $setting['key'] }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                            placeholder="Enter {{ strtolower($setting['description'] ?? $setting['key']) }}"
                        >

                    @elseif($setting['type'] === 'textarea')
                        <textarea 
                            wire:model="settings.{{ $setting['key'] }}"
                            rows="5"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                            placeholder="Enter {{ strtolower($setting['description'] ?? $setting['key']) }}"
                        ></textarea>

                    @elseif($setting['type'] === 'image')
                        <div class="space-y-4">
                            @if($setting['value'])
                                <div class="relative inline-block group">
                                    <img 
                                        src="{{ asset('storage/' . $setting['value']) }}" 
                                        alt="{{ $setting['description'] ?? $setting['key'] }}"
                                        class="max-h-40 rounded-xl border-2 border-gray-200 shadow-sm group-hover:shadow-md transition-shadow"
                                    >
                                    <button 
                                        type="button"
                                        wire:click="removeImage('{{ $setting['key'] }}')"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 transition shadow-lg opacity-0 group-hover:opacity-100"
                                        title="Remove image"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @elseif(isset($images[$setting['key']]))
                                <div class="relative inline-block">
                                    <img 
                                        src="{{ $images[$setting['key']]->temporaryUrl() }}" 
                                        alt="Preview"
                                        class="max-h-40 rounded-xl border-2 border-green-300 shadow-sm"
                                    >
                                    <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded">New</span>
                                </div>
                            @else
                                <div class="flex items-center justify-center w-full h-32 bg-gray-100 border-2 border-dashed border-gray-300 rounded-xl">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No image uploaded</p>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="flex items-center space-x-3">
                                <label class="flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg cursor-pointer hover:from-green-600 hover:to-green-700 transition-all shadow-sm hover:shadow">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Choose File</span>
                                    <input 
                                        type="file" 
                                        wire:model="images.{{ $setting['key'] }}"
                                        accept="image/*"
                                        class="hidden"
                                    >
                                </label>
                                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-2 rounded-lg">PNG, JPG, WEBP (Max 2MB)</span>
                            </div>
                            
                            @if(isset($images[$setting['key']]))
                                <div wire:loading wire:target="images.{{ $setting['key'] }}" class="text-green-600 text-sm flex items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Uploading...
                                </div>
                            @endif
                        </div>

                    @elseif($setting['type'] === 'boolean')
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <span class="text-sm text-gray-700">Enable this feature</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    wire:model="settings.{{ $setting['key'] }}"
                                    class="sr-only peer"
                                >
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>

                    @endif

                    @error('settings.' . $setting['key'])
                        <div class="flex items-center mt-2 text-red-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-xs font-medium">{{ $message }}</p>
                        </div>
                    @enderror
                </div>
            @endforeach
        </div>

        <!-- Individual Save Button for this section -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                <span class="font-medium">Changes are pending</span> • Make sure to save your settings
            </div>
            <div class="flex items-center space-x-3">
                <button 
                    type="button"
                    wire:click="resetForm"
                    wire:loading.attr="disabled"
                    wire:target="save,resetForm"
                    class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset
                </button>
                <button 
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition font-medium flex items-center shadow-sm hover:shadow-lg disabled:opacity-75 disabled:cursor-not-allowed">
                    <svg wire:loading.remove wire:target="save" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg wire:loading wire:target="save" class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="save">Save {{ ucfirst(str_replace('_', ' ', $group)) }} Settings</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </form>
</div>
