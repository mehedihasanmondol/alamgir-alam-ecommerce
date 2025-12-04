@props([
    'name' => '',
    'title' => '',
    'maxWidth' => '2xl',
    'show' => false,
])

@php
$maxWidthClass = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    '4xl' => 'max-w-4xl',
    '5xl' => 'max-w-5xl',
    '6xl' => 'max-w-6xl',
    '7xl' => 'max-w-7xl',
][$maxWidth];
@endphp

<div x-data="{ show: @js($show) }"
     @if($name)
     @open-dialog-{{ $name }}.window="show = true"
     @close-dialog-{{ $name }}.window="show = false"
     @endif
     x-show="show"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     @keydown.escape.window="show = false">
    
    <!-- Backdrop with blur effect -->
    <div x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 transition-all"
         style="background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
         @click="show = false">
    </div>
    
    <!-- Dialog Container -->
    <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
        <div x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             @click.stop
             class="relative w-full {{ $maxWidthClass }} bg-white rounded-lg shadow-2xl border border-gray-200 flex flex-col"
             style="backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); max-height: calc(100vh - 100px);">
            
            <!-- Header (Fixed) -->
            @if($title)
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50 flex-shrink-0">
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                <button @click="show = false" 
                        type="button"
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            @endif
            
            <!-- Content (Scrollable) -->
            <div class="px-6 py-4 overflow-y-auto flex-1">
                {{ $slot }}
            </div>
            
            <!-- Footer (Fixed) -->
            @isset($footer)
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
                {{ $footer }}
            </div>
            @endisset
        </div>
    </div>
</div>
