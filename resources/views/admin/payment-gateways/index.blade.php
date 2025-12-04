@extends('layouts.admin')

@section('title', 'Payment Gateways')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Payment Gateways</h1>
            <p class="mt-1 text-sm text-gray-600">Manage online payment gateway configurations</p>
        </div>
    </div>

    <!-- Payment Gateways List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($gateways as $gateway)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Gateway Header -->
                <div class="p-6 {{ $gateway->is_active ? 'bg-green-50 border-b-2 border-green-500' : 'bg-gray-50' }}">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900">{{ $gateway->name }}</h3>
                        <div class="flex items-center space-x-2">
                            @if($gateway->is_test_mode)
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">
                                    Test Mode
                                </span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                    Live Mode
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($gateway->logo)
                        <img src="{{ asset('storage/' . $gateway->logo) }}" alt="{{ $gateway->name }}" class="h-12 w-auto mb-4">
                    @endif

                    @if($gateway->description)
                        <p class="text-sm text-gray-600">{{ $gateway->description }}</p>
                    @endif
                </div>

                <!-- Gateway Details -->
                <div class="p-6">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           class="sr-only peer"
                                           {{ $gateway->is_active ? 'checked' : '' }}
                                           onchange="toggleGatewayStatus({{ $gateway->id }}, this)">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                    <span class="ml-3 text-sm font-medium {{ $gateway->is_active ? 'text-green-600' : 'text-gray-500' }}">
                                        {{ $gateway->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </label>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500">Slug</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $gateway->slug }}</dd>
                        </div>

                        @if($gateway->is_active)
                            <div class="pt-3 border-t border-gray-200">
                                <div class="flex items-center text-sm text-green-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Available for customers</span>
                                </div>
                            </div>
                        @endif
                    </dl>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('admin.payment-gateways.edit', $gateway) }}" 
                           class="flex-1 px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            Configure
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Payment Gateway Information</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Configure API credentials for each gateway before activating</li>
                        <li>Test Mode allows you to test payments without real transactions</li>
                        <li>Active gateways will be available for customers to use</li>
                        <li>Always test in Test Mode before switching to Live Mode</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleGatewayStatus(gatewayId, checkbox) {
    const isActive = checkbox.checked;
    
    fetch(`/admin/payment-gateways/${gatewayId}/toggle`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const message = isActive ? 'Payment gateway activated successfully' : 'Payment gateway deactivated successfully';
            window.dispatchEvent(new CustomEvent('alert-toast', { 
                detail: { type: 'success', message: message } 
            }));
            
            // Reload page to update UI
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            // Revert checkbox
            checkbox.checked = !isActive;
            window.dispatchEvent(new CustomEvent('alert-toast', { 
                detail: { type: 'error', message: 'Failed to update gateway status' } 
            }));
        }
    })
    .catch(error => {
        // Revert checkbox
        checkbox.checked = !isActive;
        window.dispatchEvent(new CustomEvent('alert-toast', { 
            detail: { type: 'error', message: 'Failed to update gateway status' } 
        }));
    });
}
</script>
@endpush
@endsection
