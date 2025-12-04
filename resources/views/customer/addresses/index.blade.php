@extends('layouts.customer')

@section('title', 'My Addresses')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Addresses</h1>
                <p class="text-gray-600 mt-1">Manage your shipping and billing addresses</p>
            </div>
            <div>
                <button onclick="Livewire.dispatch('openAddressModal')" 
                        class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add New Address
                </button>
            </div>
        </div>
    </div>

    <!-- Addresses List Component -->
    @livewire('customer.address-manager')
</div>
@endsection
