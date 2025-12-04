<div class="relative" x-data="{ open: @entangle('showResults') }">
    <div class="relative">
        <input type="text" 
               wire:model.live.debounce.300ms="query"
               placeholder="Search users..."
               class="w-full px-4 py-2 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
               @focus="open = true">
        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        
        @if($query)
        <button wire:click="clearSearch" 
                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
        @endif
    </div>

    <!-- Search Results Dropdown -->
    @if($showResults && $results->count() > 0)
    <div class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-lg border border-gray-200 max-h-96 overflow-y-auto"
         x-show="open"
         @click.away="open = false">
        <div class="p-2">
            @foreach($results as $user)
            <a href="{{ route('admin.users.show', $user->id) }}" 
               class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition">
                <div class="h-10 w-10 flex-shrink-0">
                    @if($user->media)
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $user->media->small_url }}" alt="{{ $user->name }}">
                    @elseif($user->avatar)
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                    @else
                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->email ?? $user->mobile }}</p>
                </div>
                <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </a>
            @endforeach
        </div>
    </div>
    @elseif($showResults && $query && $results->count() === 0)
    <div class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-lg border border-gray-200 p-4"
         x-show="open"
         @click.away="open = false">
        <p class="text-center text-gray-500">
            <i class="fas fa-search text-2xl mb-2 text-gray-300"></i>
            <br>No users found
        </p>
    </div>
    @endif
</div>
