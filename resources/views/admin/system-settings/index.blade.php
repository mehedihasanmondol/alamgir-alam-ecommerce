{{-- 
/**
 * ModuleName: System Settings Management (Livewire)
 * Purpose: Admin interface for managing system-wide settings
 * Features: Cache Management, Maintenance Mode
 * 
 * @category Admin Views
 * @package  Resources\Views\Admin
 * @created  2025-11-25
 */
--}}

@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Enhanced Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">System Settings</h1>
                    <p class="text-gray-600 mt-1">Manage system configurations and performance settings</p>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                <span class="w-2 h-2 bg-indigo-400 rounded-full mr-2 animate-pulse"></span>
                System Active
            </span>
        </div>
    </div>

    <!-- Tab Navigation & Content Layout -->
    <div class="flex flex-col lg:flex-row gap-6" x-data="{ activeTab: 'cache', mobileMenuOpen: false }">
        <!-- Mobile Dropdown Navigation -->
        <div class="lg:hidden mb-4">
            <button 
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="w-full bg-white rounded-xl shadow-sm border border-gray-200 px-4 py-3 flex items-center justify-between"
            >
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <div>
                        <div class="font-semibold text-gray-900 capitalize text-sm" x-text="activeTab === 'cache' ? 'Cache Management' : 'Construction Mode'"></div>
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
                @foreach($sections as $key => $section)
                    <button
                        @click="activeTab = '{{ $key }}'; mobileMenuOpen = false;"
                        :class="activeTab === '{{ $key }}' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center space-x-3 px-4 py-3 border-b border-gray-100 last:border-b-0 transition-colors"
                    >
                        <div :class="activeTab === '{{ $key }}' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-600'" class="w-8 h-8 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $section['icon'] }}"></path>
                            </svg>
                        </div>
                        <div class="flex-1 text-left">
                            <div class="font-medium text-sm">{{ $section['title'] }}</div>
                            <div class="text-xs opacity-75">{{ $section['description'] }}</div>
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
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        Settings Sections
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">{{ count($sections) }} sections available</p>
                </div>
                <nav class="p-2 space-y-1">
                    @foreach($sections as $key => $section)
                        <button
                            @click="activeTab = '{{ $key }}'"
                            :class="activeTab === '{{ $key }}' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'text-gray-700 hover:bg-gray-50 border-transparent'"
                            class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-all border-2 text-left group"
                        >
                            <div :class="activeTab === '{{ $key }}' ? 'bg-indigo-100' : 'bg-gray-100 group-hover:bg-indigo-50'" class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                                <svg :class="activeTab === '{{ $key }}' ? 'text-indigo-600' : 'text-gray-600 group-hover:text-indigo-600'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $section['icon'] }}"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-sm">{{ $section['title'] }}</div>
                                <div class="text-xs opacity-75">{{ $section['description'] }}</div>
                            </div>
                            <svg :class="activeTab === '{{ $key }}' ? 'opacity-100' : 'opacity-0'" class="w-5 h-5 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    @endforeach
                </nav>
                <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                    <div class="text-xs text-gray-600">
                        <svg class="w-4 h-4 inline mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Click any section to navigate instantly
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area (Tab Panels) -->
        <div class="flex-1">
            @foreach($sections as $key => $section)
                <div 
                    x-show="activeTab === '{{ $key }}'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                >
                    @livewire('admin.system-settings.' . $section['component'], key($key))
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
