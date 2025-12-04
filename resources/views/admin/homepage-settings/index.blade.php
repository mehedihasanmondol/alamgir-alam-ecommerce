@extends('layouts.admin')

@section('title', 'Homepage Settings')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Enhanced Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Homepage Settings</h1>
                    <p class="text-gray-600 mt-1">Navigate through sections easily - Each section saves independently</p>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('home') }}" target="_blank" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Preview Homepage
            </a>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                Active
            </span>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Tab Navigation & Content Layout -->
    @php
        $settingsGroups = \App\Models\HomepageSetting::getAllGrouped();
        $firstGroup = array_key_first($settingsGroups);
    @endphp
    <div class="flex flex-col lg:flex-row gap-6" x-data="{ 
                activeTab: 'hero_sliders',
                mobileMenuOpen: false
            }">
        <!-- Mobile Dropdown Navigation -->
        <div class="lg:hidden mb-4">
            <button 
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="w-full bg-white rounded-xl shadow-sm border border-gray-200 px-4 py-3 flex items-center justify-between"
            >
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <div>
                        <div class="font-semibold text-gray-900 capitalize text-sm" x-text="activeTab.replace('_', ' ')"></div>
                        <div class="text-xs text-gray-500">Settings Section</div>
                    </div>
                </div>
                <svg :class="mobileMenuOpen ? 'rotate-180' : ''" class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <!-- Mobile Dropdown Menu -->
            <div 
                x-show="mobileMenuOpen" 
                x-transition
                @click.away="mobileMenuOpen = false"
                class="mt-2 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden"
            >
                <!-- Hero Sliders Mobile Menu Item -->
                <button
                    @click="activeTab = 'hero_sliders'; mobileMenuOpen = false;"
                    :class="activeTab === 'hero_sliders' ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50'"
                    class="w-full flex items-center space-x-3 px-4 py-3 border-b border-gray-100 transition-colors"
                >
                    <div :class="activeTab === 'hero_sliders' ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 text-gray-600'" class="w-8 h-8 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 text-left">
                        <div class="font-medium capitalize text-sm">Hero Sliders</div>
                        <div class="text-xs opacity-75">Manage sliders</div>
                    </div>
                </button>

                @foreach($settingsGroups as $group => $groupSettings)
                    @php
                        $icons = [
                            'general' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                            'featured' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                            'banner' => 'M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h3a1 1 0 011 1v2a1 1 0 01-1 1h-1v10a2 2 0 01-2 2H7a2 2 0 01-2-2V8H4a1 1 0 01-1-1V5a1 1 0 011-1h3z',
                            'top_header' => 'M4 6h16M4 12h16M4 18h16',
                        ];
                        $icon = $icons[$group] ?? 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4';
                    @endphp
                    <button
                        @click="activeTab = '{{ $group }}'; mobileMenuOpen = false;"
                        :class="activeTab === '{{ $group }}' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center space-x-3 px-4 py-3 border-b border-gray-100 last:border-b-0 transition-colors"
                    >
                        <div :class="activeTab === '{{ $group }}' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600'" class="w-8 h-8 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
                            </svg>
                        </div>
                        <div class="flex-1 text-left">
                            <div class="font-medium capitalize text-sm">{{ str_replace('_', ' ', $group) }}</div>
                            <div class="text-xs opacity-75">{{ count($groupSettings) }} setting(s)</div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Sidebar Navigation (Desktop) -->
        <div class="hidden lg:block lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-4">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        Settings Sections
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">{{ count($settingsGroups) + 1 }} sections available</p>
                </div>
                <nav class="p-2 space-y-1">
                    <!-- Hero Sliders Navigation -->
                    <button
                        @click="activeTab = 'hero_sliders'"
                        :class="activeTab === 'hero_sliders' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'text-gray-700 hover:bg-gray-50 border-transparent'"
                        class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-all border-2 text-left group"
                    >
                        <div :class="activeTab === 'hero_sliders' ? 'bg-purple-100' : 'bg-gray-100 group-hover:bg-purple-50'" class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                            <svg :class="activeTab === 'hero_sliders' ? 'text-purple-600' : 'text-gray-600 group-hover:text-purple-600'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium capitalize text-sm">Hero Sliders</div>
                            <div class="text-xs opacity-75">Manage sliders</div>
                        </div>
                    </button>

                    @foreach($settingsGroups as $group => $groupSettings)
                        @php
                            $icons = [
                                'general' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                                'featured' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                                'banner' => 'M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h3a1 1 0 011 1v2a1 1 0 01-1 1h-1v10a2 2 0 01-2 2H7a2 2 0 01-2-2V8H4a1 1 0 01-1-1V5a1 1 0 011-1h3z',
                                'top_header' => 'M4 6h16M4 12h16M4 18h16',
                            ];
                            $icon = $icons[$group] ?? 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4';
                        @endphp
                        <button
                            @click="activeTab = '{{ $group }}'"
                            :class="activeTab === '{{ $group }}' ? 'bg-green-50 text-green-700 border-green-200' : 'text-gray-700 hover:bg-gray-50 border-transparent'"
                            class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-all border-2 text-left group"
                        >
                            <div :class="activeTab === '{{ $group }}' ? 'bg-green-100' : 'bg-gray-100 group-hover:bg-green-50'" class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                                <svg :class="activeTab === '{{ $group }}' ? 'text-green-600' : 'text-gray-600 group-hover:text-green-600'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium capitalize text-sm">{{ str_replace('_', ' ', $group) }}</div>
                                <div class="text-xs opacity-75">{{ count($groupSettings) }} setting(s)</div>
                            </div>
                        </button>
                    @endforeach
                </nav>
                <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                    <div class="text-xs text-gray-600">
                        <svg class="w-4 h-4 inline mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Click any section to navigate instantly
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area (Tab Panels) -->
        <div class="flex-1">
            <!-- Hero Sliders Section -->
            <div 
                x-show="activeTab === 'hero_sliders'"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
            >
                @livewire('admin.hero-slider-manager')
            </div>

            @foreach($settingsGroups as $group => $groupSettings)
                <div 
                    x-show="activeTab === '{{ $group }}'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                >
                    @livewire('admin.homepage-setting-section', ['group' => $group, 'groupSettings' => $groupSettings], key($group))
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toast Notification System
    document.addEventListener('livewire:init', () => {
        Livewire.on('setting-saved', (event) => {
            const data = event[0] || event;
            showToast(data.message, data.type);
        });

        Livewire.on('slider-saved', (event) => {
            const data = event[0] || event;
            showToast(data.message, data.type);
        });
    });

    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;

        const toast = document.createElement('div');
        
        // Define toast colors and icons based on type
        const styles = {
            success: {
                bg: 'bg-green-500',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
            },
            error: {
                bg: 'bg-red-500',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
            },
            info: {
                bg: 'bg-blue-500',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
            }
        };
        
        const style = styles[type] || styles.success;
        
        toast.className = `${style.bg} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 min-w-80 transform transition-all duration-300 translate-x-0 opacity-100`;
        toast.innerHTML = `
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${style.icon}
            </svg>
            <p class="flex-1 font-medium">${message}</p>
            <button onclick="this.parentElement.remove()" class="hover:bg-white/20 rounded p-1 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }
        }, 4000);
    }
</script>
@endpush
@endsection
