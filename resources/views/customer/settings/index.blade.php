@extends('layouts.customer')

@section('title', 'Account Settings')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900">Account Settings</h1>
        <p class="text-gray-600 mt-1">Manage your account security and preferences</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Change Password -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Change Password</h2>
                <p class="text-sm text-gray-600 mt-1">Update your password to keep your account secure</p>
            </div>

            <form method="POST" action="{{ route('customer.password.update') }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Current Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="current_password" 
                               id="current_password" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('current_password') border-red-500 @enderror">
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            New Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Must be at least 8 characters long</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm New Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Update Password
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Email Preferences -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Email Preferences</h2>
                <p class="text-sm text-gray-600 mt-1">Choose what emails you want to receive</p>
            </div>

            <form method="POST" action="{{ route('customer.preferences.update') }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <!-- Order Updates -->
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" 
                               name="email_order_updates" 
                               value="1" 
                               {{ auth()->user()->email_order_updates ?? true ? 'checked' : '' }}
                               class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Order Updates</p>
                            <p class="text-xs text-gray-500">Receive notifications about your order status</p>
                        </div>
                    </label>

                    <!-- Promotional Emails -->
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" 
                               name="email_promotions" 
                               value="1" 
                               {{ auth()->user()->email_promotions ?? false ? 'checked' : '' }}
                               class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Promotional Emails</p>
                            <p class="text-xs text-gray-500">Get special offers and discounts</p>
                        </div>
                    </label>

                    <!-- Newsletter -->
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" 
                               name="email_newsletter" 
                               value="1" 
                               {{ auth()->user()->email_newsletter ?? false ? 'checked' : '' }}
                               class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Newsletter</p>
                            <p class="text-xs text-gray-500">Weekly updates about new products and features</p>
                        </div>
                    </label>

                    <!-- Product Recommendations -->
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" 
                               name="email_recommendations" 
                               value="1" 
                               {{ auth()->user()->email_recommendations ?? false ? 'checked' : '' }}
                               class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Product Recommendations</p>
                            <p class="text-xs text-gray-500">Personalized product suggestions based on your interests</p>
                        </div>
                    </label>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Save Preferences
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white rounded-lg shadow-sm border-2 border-red-200">
        <div class="p-6 border-b border-red-200 bg-red-50">
            <h2 class="text-lg font-semibold text-red-900">Danger Zone</h2>
            <p class="text-sm text-red-700 mt-1">Irreversible actions for your account</p>
        </div>

        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Delete Account</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Once you delete your account, there is no going back. Please be certain.
                    </p>
                </div>
                <button onclick="confirm('Are you sure you want to delete your account? This action cannot be undone.') && document.getElementById('delete-account-form').submit()" 
                        class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                    Delete Account
                </button>
            </div>

            <form id="delete-account-form" 
                  method="POST" 
                  action="{{ route('customer.account.delete') }}" 
                  class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>
@endsection
