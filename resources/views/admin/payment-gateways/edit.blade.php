@extends('layouts.admin')

@section('title', 'Configure ' . $gateway->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.payment-gateways.index') }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Configure {{ $gateway->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">Update API credentials and settings</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            @if($gateway->is_active)
                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">Active</span>
            @else
                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-semibold rounded-full">Inactive</span>
            @endif
            
            @if($gateway->is_test_mode)
                <span class="px-3 py-1 bg-orange-100 text-orange-800 text-sm font-semibold rounded-full">Test Mode</span>
            @else
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">Live Mode</span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.payment-gateways.update', $gateway) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Configuration -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Settings -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Settings</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gateway Name</label>
                            <input type="text" name="name" value="{{ old('name', $gateway->name) }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $gateway->description) }}</textarea>
                        </div>

                        <!-- Logo Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gateway Logo</label>
                            
                            @if($gateway->logo)
                                <div class="mb-3 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Current Logo:</span>
                                        <img src="{{ asset('storage/' . $gateway->logo) }}" alt="{{ $gateway->name }}" class="h-12 w-auto">
                                    </div>
                                    
                                    <!-- Copyable Logo URL -->
                                    <div class="mt-3">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Logo URL (Click to copy)</label>
                                        <div class="flex items-center space-x-2">
                                            <input type="text" 
                                                   id="logoUrl" 
                                                   value="{{ asset('storage/' . $gateway->logo) }}" 
                                                   readonly 
                                                   class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white font-mono"
                                                   onclick="this.select()">
                                            <button type="button" 
                                                    onclick="copyLogoUrl()"
                                                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                                <svg id="copyIcon" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                                <span id="copyText">Copy</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <input type="file" 
                                   name="logo" 
                                   id="logoInput"
                                   accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="previewLogo(event)">
                            <p class="text-xs text-gray-500 mt-1">Recommended: PNG or SVG, max 2MB. Transparent background preferred.</p>
                            
                            <!-- Logo Preview -->
                            <div id="logoPreview" class="hidden mt-3 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                                <img id="previewImage" src="" alt="Preview" class="h-12 w-auto">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $gateway->sort_order) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                        </div>

                        <div class="flex items-center space-x-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ $gateway->is_active ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_test_mode" value="1" {{ $gateway->is_test_mode ? 'checked' : '' }}
                                       class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Test Mode</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- API Credentials -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        API Credentials
                    </h2>
                    
                    <div class="space-y-4">
                        @if($gateway->slug === 'bkash')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">App Key</label>
                                <input type="text" name="credentials[app_key]" 
                                       value="{{ old('credentials.app_key', $gateway->getCredential('app_key')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">App Secret</label>
                                <input type="password" name="credentials[app_secret]" 
                                       value="{{ old('credentials.app_secret', $gateway->getCredential('app_secret')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                <input type="text" name="credentials[username]" 
                                       value="{{ old('credentials.username', $gateway->getCredential('username')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <input type="password" name="credentials[password]" 
                                       value="{{ old('credentials.password', $gateway->getCredential('password')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Base URL</label>
                                <input type="url" name="credentials[base_url]" 
                                       value="{{ old('credentials.base_url', $gateway->getCredential('base_url')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm">
                                <p class="text-xs text-gray-500 mt-1">
                                    Sandbox: https://tokenized.sandbox.bka.sh/v1.2.0-beta<br>
                                    Live: https://tokenized.pay.bka.sh/v1.2.0-beta
                                </p>
                            </div>

                        @elseif($gateway->slug === 'nagad')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Merchant ID</label>
                                <input type="text" name="credentials[merchant_id]" 
                                       value="{{ old('credentials.merchant_id', $gateway->getCredential('merchant_id')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Merchant Number</label>
                                <input type="text" name="credentials[merchant_number]" 
                                       value="{{ old('credentials.merchant_number', $gateway->getCredential('merchant_number')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Public Key</label>
                                <textarea name="credentials[public_key]" rows="4"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-xs">{{ old('credentials.public_key', $gateway->getCredential('public_key')) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Private Key</label>
                                <textarea name="credentials[private_key]" rows="4"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-xs">{{ old('credentials.private_key', $gateway->getCredential('private_key')) }}</textarea>
                            </div>

                        @elseif($gateway->slug === 'sslcommerz')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Store ID</label>
                                <input type="text" name="credentials[store_id]" 
                                       value="{{ old('credentials.store_id', $gateway->getCredential('store_id')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Store Password</label>
                                <input type="password" name="credentials[store_password]" 
                                       value="{{ old('credentials.store_password', $gateway->getCredential('store_password')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Base URL</label>
                                <input type="url" name="credentials[base_url]" 
                                       value="{{ old('credentials.base_url', $gateway->getCredential('base_url')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm">
                                <p class="text-xs text-gray-500 mt-1">
                                    Sandbox: https://sandbox.sslcommerz.com<br>
                                    Live: https://securepay.sslcommerz.com
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Info -->
            <div class="space-y-6">
                <!-- Save Button -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <button type="submit" 
                            class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Configuration
                    </button>
                </div>

                <!-- Gateway Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-blue-900 mb-3">Gateway Information</h3>
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="text-blue-700">Slug:</dt>
                            <dd class="font-mono text-blue-900">{{ $gateway->slug }}</dd>
                        </div>
                        
                        @if($gateway->slug === 'sslcommerz')
                            <div>
                                <dt class="text-blue-700">Success URL:</dt>
                                <dd class="font-mono text-xs text-blue-900 break-all">{{ route('payment.sslcommerz.success') }}</dd>
                            </div>
                            <div>
                                <dt class="text-blue-700">Fail URL:</dt>
                                <dd class="font-mono text-xs text-blue-900 break-all">{{ route('payment.sslcommerz.fail') }}</dd>
                            </div>
                            <div>
                                <dt class="text-blue-700">Cancel URL:</dt>
                                <dd class="font-mono text-xs text-blue-900 break-all">{{ route('payment.sslcommerz.cancel') }}</dd>
                            </div>
                        @else
                            <div>
                                <dt class="text-blue-700">Callback URL:</dt>
                                <dd class="font-mono text-xs text-blue-900 break-all">{{ route('payment.' . $gateway->slug . '.callback') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Important Notes -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-yellow-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Important Notes
                    </h3>
                    <ul class="text-xs text-yellow-800 space-y-1 list-disc list-inside">
                        <li>Always test in Test Mode before going live</li>
                        <li>Keep credentials secure and never share them</li>
                        <li>Update Base URL when switching between test and live</li>
                        <li>Verify callback URLs are accessible</li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Copy logo URL to clipboard
function copyLogoUrl() {
    const input = document.getElementById('logoUrl');
    const copyText = document.getElementById('copyText');
    const copyIcon = document.getElementById('copyIcon');
    
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    
    navigator.clipboard.writeText(input.value).then(() => {
        // Change button text and icon
        copyText.textContent = 'Copied!';
        copyIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
        
        // Reset after 2 seconds
        setTimeout(() => {
            copyText.textContent = 'Copy';
            copyIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>';
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy: ', err);
        alert('Failed to copy URL');
    });
}

// Preview logo before upload
function previewLogo(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('logoPreview');
    const previewImage = document.getElementById('previewImage');
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            preview.classList.remove('hidden');
        }
        
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
}
</script>
@endpush

@endsection
