{{-- 
/**
 * ModuleName: Site Settings Management (Enhanced with Livewire)
 * Purpose: Admin interface for managing site-wide settings with individual section saves
 * Features: Livewire components, Individual save buttons, Toast notifications, Modern UI
 * 
 * @category Admin Views
 * @package  Resources\Views\Admin
 * @updated  2025-11-12
 */
--}}

@extends('layouts.admin')

@section('title', 'Site Settings')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Enhanced Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Site Settings</h1>
                    <p class="text-gray-600 mt-1">Navigate through sections easily - Each section saves independently</p>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                Active
            </span>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Tab Navigation & Content Layout -->
    <div class="flex flex-col lg:flex-row gap-6" x-data="{ activeTab: '{{ array_key_first($settings->toArray()) }}', mobileMenuOpen: false }">
        <!-- Mobile Dropdown Navigation -->
        <div class="lg:hidden mb-4">
            <button 
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="w-full bg-white rounded-xl shadow-sm border border-gray-200 px-4 py-3 flex items-center justify-between"
            >
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                @foreach($settings as $group => $groupSettings)
                    @php
                        $icons = [
                            'general' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                            'appearance' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
                            'social' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                            'seo' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                            'invoice' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                            'login' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
                            'feedback' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z',
                            'author_page' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                        ];
                        $icon = $icons[$group] ?? 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4';
                    @endphp
                    <button
                        @click="activeTab = '{{ $group }}'; mobileMenuOpen = false;"
                        :class="activeTab === '{{ $group }}' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center space-x-3 px-4 py-3 border-b border-gray-100 last:border-b-0 transition-colors"
                    >
                        <div :class="activeTab === '{{ $group }}' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600'" class="w-8 h-8 rounded-lg flex items-center justify-center">
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
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        Settings Sections
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">{{ count($settings) }} sections available</p>
                </div>
                <nav class="p-2 space-y-1">
                    @foreach($settings as $group => $groupSettings)
                        @php
                            $icons = [
                                'general' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                                'appearance' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
                                'social' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                                'seo' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                                'invoice' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                'login' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
                                'author_page' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                                'blog' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
                                'feedback' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z',
                            ];
                            $icon = $icons[$group] ?? 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4';
                        @endphp
                        <button
                            @click="activeTab = '{{ $group }}'"
                            :class="activeTab === '{{ $group }}' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-gray-700 hover:bg-gray-50 border-transparent'"
                            class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-all border-2 text-left group"
                        >
                            <div :class="activeTab === '{{ $group }}' ? 'bg-blue-100' : 'bg-gray-100 group-hover:bg-blue-50'" class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                                <svg :class="activeTab === '{{ $group }}' ? 'text-blue-600' : 'text-gray-600 group-hover:text-blue-600'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium capitalize text-sm">{{ str_replace('_', ' ', $group) }}</div>
                                <div class="text-xs opacity-75">{{ count($groupSettings) }} setting(s)</div>
                            </div>
                            <svg :class="activeTab === '{{ $group }}' ? 'opacity-100' : 'opacity-0'" class="w-5 h-5 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    @endforeach
                </nav>
                <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                    <div class="text-xs text-gray-600">
                        <svg class="w-4 h-4 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Click any section to navigate instantly
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area (Tab Panels) -->
        <div class="flex-1">
            @foreach($settings as $group => $groupSettings)
                <div 
                    x-show="activeTab === '{{ $group }}'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                >
                    @livewire('admin.setting-section', ['group' => $group, 'groupSettings' => $groupSettings], key($group))
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
/* CKEditor Custom Styling */
.ck-editor__editable {
    min-height: 150px;
    max-height: 300px;
}

.ck.ck-editor__main>.ck-editor__editable {
    background: #ffffff;
    border-radius: 0 0 0.5rem 0.5rem;
}

/* Force list markers to display (override Tailwind reset) */
.ck-content ul,
.ck-content ol {
    margin-left: 20px;
}
</style>
@endpush

@push('scripts')
@vite('resources/js/site-settings-editor.js')
<script>
    // Toast Notification System
    document.addEventListener('livewire:init', () => {
        Livewire.on('setting-saved', (event) => {
            const data = event[0] || event;
            showToast(data.message, data.type);
        });
    });

    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');
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
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.transform = 'translateX(400px)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // Tab interface - no scroll spy needed since we're showing/hiding content
</script>

@endpush
@endsection
