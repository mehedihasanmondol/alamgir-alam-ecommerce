<div class="inline-flex items-center gap-1" x-data="{ showTooltip: false }">
    @if(!$post)
        <span class="text-xs text-gray-400">Loading...</span>
    @else
        <!-- Verified -->
        <button 
            wire:click="toggleVerification"
            type="button"
            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full transition-colors {{ $isVerified ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}"
            title="Verified"
        >
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </button>

        <!-- Editor's Choice -->
        <button 
            wire:click="toggleEditorChoice"
            type="button"
            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full transition-colors {{ $isEditorChoice ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-600' }}"
            title="Editor's Choice"
        >
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
        </button>

        <!-- Trending -->
        <button 
            wire:click="toggleTrending"
            type="button"
            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full transition-colors {{ $isTrending ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600' }}"
            title="Trending"
        >
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
            </svg>
        </button>

        <!-- Premium -->
        <button 
            wire:click="togglePremium"
            type="button"
            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full transition-colors {{ $isPremium ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' }}"
            title="Premium"
        >
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </button>

        <!-- Loading Indicator -->
        <div wire:loading class="inline-flex items-center">
            <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    @endif
</div>
