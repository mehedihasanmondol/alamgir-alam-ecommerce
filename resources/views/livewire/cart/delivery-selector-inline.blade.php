<div class="flex items-center text-sm">
    @if($selectedZone || $selectedMethod)
        <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: { modalId: 'delivery-selector-modal' } }))"
                class="flex items-center space-x-2 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition-colors group"
                title="Change delivery method">
            <svg class="w-4 h-4 text-blue-600 group-hover:text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
            </svg>
            <div class="flex items-center space-x-1">
                @if($selectedZone)
                    <span class="font-medium text-gray-900 group-hover:text-blue-700">{{ $selectedZone->name }}</span>
                @endif
                @if($selectedZone && $selectedMethod)
                    <span class="text-gray-400">•</span>
                @endif
                @if($selectedMethod)
                    <span class="text-gray-600 group-hover:text-blue-600">{{ $selectedMethod->name }}</span>
                    @if($selectedMethod->icon)
                        <span class="ml-1">{{ $selectedMethod->icon }}</span>
                    @endif
                @endif
            </div>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    @else
        <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: { modalId: 'delivery-selector-modal' } }))"
                class="flex items-center text-blue-600 hover:text-blue-700 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors"
                title="Select delivery method">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span>Select delivery method</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
    @endif
</div>
