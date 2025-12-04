<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden hover:shadow-md transition-shadow duration-200">
    <!-- Enhanced Group Header -->
    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <!-- Icon based on group -->
                @php
                    $icons = [
                        'general' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>',
                        'appearance' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>',
                        'social' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>',
                        'seo' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>',
                        'invoice' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
                        'login' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>',
                        'homepage' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>',
                        'blog' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>',
                        'stock' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>',
                        'feedback' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>',
                        'author_page' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>',
                    ];
                    $icon = $icons[$group] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>';
                @endphp
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="setting-item bg-white p-5 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                    <label class="block text-sm font-semibold text-gray-800 mb-3">
                        <div class="flex items-center justify-between">
                            <span>{{ $setting->label }}</span>
                            @if($setting->type === 'image')
                                <span class="text-xs font-normal px-2 py-1 bg-purple-100 text-purple-700 rounded">Image Upload</span>
                            @elseif($setting->type === 'boolean')
                                <span class="text-xs font-normal px-2 py-1 bg-green-100 text-green-700 rounded">Toggle</span>
                            @elseif($setting->type === 'number')
                                <span class="text-xs font-normal px-2 py-1 bg-indigo-100 text-indigo-700 rounded">Number</span>
                            @elseif($setting->type === 'select')
                                <span class="text-xs font-normal px-2 py-1 bg-teal-100 text-teal-700 rounded">Dropdown</span>
                            @elseif($setting->type === 'tinymce')
                                <span class="text-xs font-normal px-2 py-1 bg-orange-100 text-orange-700 rounded">Rich Editor</span>
                            @elseif($setting->type === 'ckeditor')
                                <span class="text-xs font-normal px-2 py-1 bg-orange-100 text-orange-700 rounded">Rich Editor</span>
                            @elseif($setting->type === 'textarea')
                                <span class="text-xs font-normal px-2 py-1 bg-blue-100 text-blue-700 rounded">Long Text</span>
                            @else
                                <span class="text-xs font-normal px-2 py-1 bg-gray-100 text-gray-700 rounded">Text</span>
                            @endif
                        </div>
                        @if($setting->description)
                            <span class="block text-xs text-gray-500 font-normal mt-2 leading-relaxed">{{ $setting->description }}</span>
                        @endif
                    </label>

                    @if($setting->type === 'text')
                        <input 
                            type="text" 
                            wire:model="settings.{{ $setting->key }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="Enter {{ strtolower($setting->label) }}"
                        >

                    @elseif($setting->type === 'number')
                        <input 
                            type="number" 
                            wire:model="settings.{{ $setting->key }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="Enter {{ strtolower($setting->label) }}"
                            min="1"
                            step="1"
                        >

                    @elseif($setting->type === 'textarea')
                        <textarea 
                            wire:model="settings.{{ $setting->key }}"
                            rows="5"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="Enter {{ strtolower($setting->label) }}"
                        ></textarea>

                    @elseif($setting->type === 'tinymce')
                        <div wire:ignore>
                            <textarea 
                                id="tinymce-{{ $setting->key }}"
                                class="tinymce-editor"
                                x-data="{
                                    init() {
                                        const textarea = this.$el;
                                        tinymce.init({
                                            target: textarea,
                                            height: 500,
                                            menubar: false,
                                            plugins: [
                                                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                                                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                                                'insertdatetime', 'media', 'table', 'help', 'wordcount'
                                            ],
                                            toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code | help',
                                            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                                            setup: function(editor) {
                                                editor.on('init', function() {
                                                    editor.setContent(@js($settings[$setting->key] ?? ''));
                                                });
                                                editor.on('blur', function() {
                                                    @this.set('settings.{{ $setting->key }}', editor.getContent());
                                                });
                                            }
                                        });
                                    }
                                }"
                                x-init="init()"
                            ></textarea>
                        </div>

                    @elseif($setting->type === 'ckeditor')
                        <div wire:ignore>
                            <textarea 
                                id="ckeditor-{{ $setting->key }}"
                                class="ckeditor-content-minimal"
                                data-setting-key="{{ $setting->key }}"
                            >{{ $settings[$setting->key] ?? '' }}</textarea>
                        </div>

                    @elseif($setting->type === 'select')
                        @if($setting->key === 'homepage_type' && isset($homepageTypes))
                            <!-- Homepage Type Select -->
                            <select 
                                wire:model.live="settings.{{ $setting->key }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white">
                                <option value="">Select homepage type...</option>
                                @foreach($homepageTypes as $type)
                                    <option value="{{ $type['key'] }}">
                                        {{ $type['label'] }} - {{ $type['description'] }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Show description for selected type -->
                            @if(isset($settings['homepage_type']) && $settings['homepage_type'])
                                @php
                                    $selectedType = collect($homepageTypes)->firstWhere('key', $settings['homepage_type']);
                                @endphp
                                @if($selectedType)
                                    <div class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-blue-900">{{ $selectedType['label'] }}</p>
                                                <p class="text-xs text-blue-700 mt-1">{{ $selectedType['description'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            
                        @elseif($setting->key === 'currency_position')
                            <!-- Currency Position Select -->
                            <select 
                                wire:model="settings.{{ $setting->key }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white">
                                <option value="">Select position...</option>
                                <option value="before">Before Amount (e.g., $29.99)</option>
                                <option value="after">After Amount (e.g., 29.99$)</option>
                            </select>
                            
                        @elseif($setting->key === 'homepage_author_id' && isset($authors))
                            <!-- Author Select -->
                            <select 
                                wire:model="settings.{{ $setting->key }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                @if(!isset($settings['homepage_type']) || $settings['homepage_type'] !== 'author_profile') disabled @endif>
                                <option value="">Select an author...</option>
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}">
                                        {{ $author->name }}
                                        @if($author->authorProfile && $author->authorProfile->job_title)
                                            - {{ $author->authorProfile->job_title }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            
                            @if(!isset($settings['homepage_type']) || $settings['homepage_type'] !== 'author_profile')
                                <p class="mt-2 text-xs text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Select "Author Profile" as homepage type to enable this option
                                </p>
                            @endif
                        @else
                            <!-- Generic Select -->
                            @php
                                $options = [];
                                if ($setting->options) {
                                    $options = is_string($setting->options) ? json_decode($setting->options, true) : $setting->options;
                                }
                            @endphp
                            <select 
                                wire:model="settings.{{ $setting->key }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white">
                                <option value="">Select an option...</option>
                                @if(is_array($options))
                                    @foreach($options as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                @endif
                            </select>
                        @endif

                    @elseif($setting->type === 'image')
                        <div class="space-y-4">
                            {{-- Show new image preview if selected, otherwise show existing image --}}
                            @if(isset($images[$setting->key]))
                                <div class="relative inline-block group">
                                    <img 
                                        src="{{ $images[$setting->key]->temporaryUrl() }}" 
                                        alt="Preview"
                                        class="max-h-40 rounded-xl border-2 border-blue-300 shadow-sm"
                                    >
                                    <span class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded font-medium shadow">New Upload</span>
                                    <div class="absolute -bottom-6 left-0 right-0 text-center">
                                        <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded">Will be saved as WebP</span>
                                    </div>
                                </div>
                            @elseif($setting->value)
                                <div class="relative inline-block group">
                                    <img 
                                        src="{{ asset('storage/' . $setting->value) }}" 
                                        alt="{{ $setting->label }}"
                                        class="max-h-40 rounded-xl border-2 border-gray-200 shadow-sm group-hover:shadow-md transition-shadow"
                                    >
                                    <button 
                                        type="button"
                                        wire:click="removeImage('{{ $setting->key }}')"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 transition shadow-lg opacity-0 group-hover:opacity-100"
                                        title="Remove image"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
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
                                <label class="flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg cursor-pointer hover:from-blue-600 hover:to-blue-700 transition-all shadow-sm hover:shadow">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Choose File</span>
                                    <input 
                                        type="file" 
                                        wire:model="images.{{ $setting->key }}"
                                        accept="image/*"
                                        class="hidden"
                                    >
                                </label>
                                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-2 rounded-lg">PNG, JPG, WEBP (Max 2MB)</span>
                            </div>
                            
                            @if(isset($images[$setting->key]))
                                <div wire:loading wire:target="images.{{ $setting->key }}" class="text-blue-600 text-sm flex items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Uploading...
                                </div>
                            @endif
                        </div>

                    @elseif($setting->type === 'boolean')
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <span class="text-sm text-gray-700">Enable this feature</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    wire:model="settings.{{ $setting->key }}"
                                    class="sr-only peer"
                                >
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                    @endif

                    @error('settings.' . $setting->key)
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
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition font-medium flex items-center shadow-sm hover:shadow-lg disabled:opacity-75 disabled:cursor-not-allowed">
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
