@extends('layouts.admin')

@section('title', 'Contact Settings')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Contact Page Settings</h1>
            <p class="mt-1 text-sm text-gray-600">Manage contact information, FAQs, and Google Maps integration</p>
        </div>
    </div>
</div>

<!-- Tabs Container -->
<div x-data="{ activeTab: 'settings' }" class="bg-white rounded-lg shadow">
    <!-- Tab Navigation -->
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px space-x-8 px-6" aria-label="Tabs">
            <button 
                @click="activeTab = 'settings'"
                :class="activeTab === 'settings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                <i class="fas fa-cog mr-2"></i>
                Contact Settings
            </button>
            <button 
                @click="activeTab = 'faqs'"
                :class="activeTab === 'faqs' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                <i class="fas fa-question-circle mr-2"></i>
                FAQs Management
                <span class="ml-2 bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">{{ $faqs->count() }}</span>
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="p-6">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Settings Tab -->
        <div x-show="activeTab === 'settings'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <form action="{{ route('admin.contact.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @foreach($settings as $group => $groupSettings)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ ucfirst($group) }} Settings
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($groupSettings as $setting)
                        <div class="{{ $setting->type === 'image' ? 'md:col-span-2' : '' }}">
                            <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                @if($setting->description)
                                <span class="block text-xs text-gray-500 font-normal mt-1">{{ $setting->description }}</span>
                                @endif
                            </label>
                            
                            @if($setting->type === 'image')
                                <!-- Image Upload Field -->
                                <div class="space-y-3">
                                    @if($setting->value)
                                    <div class="relative inline-block">
                                        <img src="{{ asset('storage/' . $setting->value) }}" 
                                             alt="SEO Image" 
                                             class="h-32 w-auto rounded-lg border border-gray-300">
                                        <span class="absolute top-1 right-1 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                            <i class="fas fa-check-circle mr-1"></i>Current
                                        </span>
                                    </div>
                                    @endif
                                    <input 
                                        type="file"
                                        id="{{ $setting->key }}"
                                        name="{{ $setting->key }}"
                                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    >
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Recommended: 1200x630px for optimal social media sharing. Max 5MB. Will be converted to WebP.
                                    </p>
                                </div>
                            @elseif($setting->type === 'textarea')
                                <textarea 
                                    id="{{ $setting->key }}"
                                    name="settings[{{ $setting->key }}]"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    rows="3"
                                >{{ old('settings.'.$setting->key, $setting->value) }}</textarea>
                            @elseif($setting->type === 'boolean')
                                <select 
                                    id="{{ $setting->key }}"
                                    name="settings[{{ $setting->key }}]"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>No</option>
                                </select>
                            @else
                                <input 
                                    type="{{ $setting->type }}"
                                    id="{{ $setting->key }}"
                                    name="settings[{{ $setting->key }}]"
                                    value="{{ old('settings.'.$setting->key, $setting->value) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="flex justify-end pt-6">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Settings
                    </button>
                </div>
            </form>

            <!-- Google Maps API Key Notice -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-900 mb-2"><i class="fas fa-info-circle mr-2"></i>Google Maps Setup</h4>
                <p class="text-sm text-blue-800 mb-2">To enable Google Maps on the contact page:</p>
                <ol class="text-sm text-blue-800 list-decimal list-inside space-y-1">
                    <li>Get a Google Maps API key from <a href="https://console.cloud.google.com/google/maps-apis" target="_blank" class="underline">Google Cloud Console</a></li>
                    <li>Add it to your <code class="bg-blue-100 px-2 py-1 rounded">.env</code> file: <code class="bg-blue-100 px-2 py-1 rounded">GOOGLE_MAPS_API_KEY=your_api_key</code></li>
                    <li>Update <code class="bg-blue-100 px-2 py-1 rounded">config/services.php</code> if needed</li>
                </ol>
            </div>
        </div>

        <!-- FAQs Tab -->
        <div x-show="activeTab === 'faqs'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            @livewire('admin.contact-faq-manager', ['faqs' => $faqs])
        </div>
    </div>
</div>
@endsection
