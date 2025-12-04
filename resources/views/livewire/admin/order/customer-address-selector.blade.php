<div>
    <!-- Address Selection Button -->
    @if($selectedUserId && ($savedAddresses->count() > 0 || $userProfile))
    <button type="button" 
            wire:click="openAddressModal" 
            class="text-sm font-medium text-blue-600 hover:text-blue-700 flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
        </svg>
        Select from Address Book
    </button>
    @endif

    <!-- Address Selection Modal -->
    @if($showAddressModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity" 
                 style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
                 wire:click="closeAddressModal"></div>
            
            <div class="relative rounded-lg shadow-xl max-w-2xl w-full p-6 border border-gray-200 max-h-[80vh] overflow-y-auto"
                 style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Select Shipping Address</h3>
                    <button wire:click="closeAddressModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Address Options -->
                <div class="space-y-3">
                    <!-- Profile Address -->
                    @if($userProfile && ($userProfile->name || $userProfile->mobile || $userProfile->email || $userProfile->address))
                    <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-green-500 cursor-pointer transition-colors"
                         wire:click="selectAddress('{{ $userProfile->name }}', '{{ $userProfile->mobile ?? $userProfile->phone ?? "" }}', '{{ $userProfile->email }}', '{{ addslashes($userProfile->address ?? "") }}')">
                        <div class="flex items-start">
                            <div class="bg-blue-100 rounded-lg p-2 mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">Customer Profile</h4>
                                <p class="text-sm text-gray-700 mt-1">{{ $userProfile->name }}</p>
                                @if($userProfile->mobile || $userProfile->phone)
                                <p class="text-sm text-gray-600">📱 {{ $userProfile->mobile ?? $userProfile->phone }}</p>
                                @endif
                                @if($userProfile->email)
                                <p class="text-sm text-gray-600">✉️ {{ $userProfile->email }}</p>
                                @endif
                                @if($userProfile->address)
                                <p class="text-sm text-gray-600">{{ $userProfile->address }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Saved Addresses -->
                    @foreach($savedAddresses as $address)
                    <div class="border-2 {{ $address->is_default ? 'border-green-500 bg-green-50' : 'border-gray-200' }} rounded-lg p-4 hover:border-green-500 cursor-pointer transition-colors"
                         wire:click="selectAddress('{{ $address->name }}', '{{ $address->phone }}', '{{ $address->email ?? "" }}', '{{ addslashes($address->address) }}')">
                        <div class="flex items-start">
                            <div class="bg-green-100 rounded-lg p-2 mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-semibold text-gray-900">{{ $address->label }}</h4>
                                    @if($address->is_default)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Default</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-700 mt-1">{{ $address->name }}</p>
                                <p class="text-sm text-gray-600">📱 {{ $address->phone }}</p>
                                @if($address->email)
                                <p class="text-sm text-gray-600">✉️ {{ $address->email }}</p>
                                @endif
                                <p class="text-sm text-gray-600">{{ $address->address }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($savedAddresses->isEmpty() && !$userProfile)
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    <p class="text-gray-500">No saved addresses found for this customer</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
