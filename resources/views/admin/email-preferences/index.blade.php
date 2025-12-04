@extends('layouts.admin')

@section('title', 'Email Preferences Management')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Email Preferences Management</h1>
                <p class="mt-1 text-sm text-gray-600">Manage customer email notification preferences</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <!-- <a href="{{ route('admin.email-preferences.schedule-setup') }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i>
                    Schedule Setup
                </a> -->
                <a href="{{ route('admin.email-preferences.mail-setup') }}" 
                   class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    Mail Editor
                </a>
                <a href="{{ route('admin.email-preferences.guideline') }}" 
                   class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-book"></i>
                    Setup Guide
                </a>
                
                <button onclick="exportPreferences('csv')" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-download"></i>
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Customers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_customers']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Order Updates</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['order_updates_enabled']) }}</p>
                    <p class="text-xs text-gray-500">{{ round(($stats['order_updates_enabled'] / max($stats['total_customers'], 1)) * 100) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Promotions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['promotions_enabled']) }}</p>
                    <p class="text-xs text-gray-500">{{ round(($stats['promotions_enabled'] / max($stats['total_customers'], 1)) * 100) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-newspaper text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Newsletter</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['newsletter_enabled']) }}</p>
                    <p class="text-xs text-gray-500">{{ round(($stats['newsletter_enabled'] / max($stats['total_customers'], 1)) * 100) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Recommendations</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['recommendations_enabled']) }}</p>
                    <p class="text-xs text-gray-500">{{ round(($stats['recommendations_enabled'] / max($stats['total_customers'], 1)) * 100) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-5">
            <form method="GET" action="{{ route('admin.email-preferences.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name, email, or mobile..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Order Updates Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Updates</label>
                    <select name="order_updates" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="1" {{ request('order_updates') === '1' ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ request('order_updates') === '0' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>

                <!-- Promotions Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Promotions</label>
                    <select name="promotions" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="1" {{ request('promotions') === '1' ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ request('promotions') === '0' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>

                <!-- Newsletter Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Newsletter</label>
                    <select name="newsletter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="1" {{ request('newsletter') === '1' ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ request('newsletter') === '0' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>

                <!-- Recommendations Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recommendations</label>
                    <select name="recommendations" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All</option>
                        <option value="1" {{ request('recommendations') === '1' ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ request('recommendations') === '0' ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="md:col-span-6 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('admin.email-preferences.index') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-shopping-cart"></i> Order Updates
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-percentage"></i> Promotions
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-newspaper"></i> Newsletter
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-star"></i> Recommendations
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member Since</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="user-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $user->id }}">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                                <span class="text-sm text-gray-500">{{ $user->email }}</span>
                                @if($user->mobile)
                                    <span class="text-xs text-gray-400">{{ $user->mobile }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="togglePreference({{ $user->id }}, 'order_updates', {{ $user->email_order_updates ? 'true' : 'false' }})"
                                    class="preference-toggle inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors {{ $user->email_order_updates ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                <i class="fas fa-{{ $user->email_order_updates ? 'check' : 'times' }} mr-1"></i>
                                {{ $user->email_order_updates ? 'ON' : 'OFF' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="togglePreference({{ $user->id }}, 'promotions', {{ $user->email_promotions ? 'true' : 'false' }})"
                                    class="preference-toggle inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors {{ $user->email_promotions ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                <i class="fas fa-{{ $user->email_promotions ? 'check' : 'times' }} mr-1"></i>
                                {{ $user->email_promotions ? 'ON' : 'OFF' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="togglePreference({{ $user->id }}, 'newsletter', {{ $user->email_newsletter ? 'true' : 'false' }})"
                                    class="preference-toggle inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors {{ $user->email_newsletter ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                <i class="fas fa-{{ $user->email_newsletter ? 'check' : 'times' }} mr-1"></i>
                                {{ $user->email_newsletter ? 'ON' : 'OFF' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="togglePreference({{ $user->id }}, 'recommendations', {{ $user->email_recommendations ? 'true' : 'false' }})"
                                    class="preference-toggle inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors {{ $user->email_recommendations ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                <i class="fas fa-{{ $user->email_recommendations ? 'check' : 'times' }} mr-1"></i>
                                {{ $user->email_recommendations ? 'ON' : 'OFF' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-gray-300 text-5xl mb-3"></i>
                                <p class="text-gray-500 text-lg">No customers found</p>
                                <p class="text-gray-400 text-sm mt-1">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Bulk Actions (shows when checkboxes are selected) -->
    <div id="bulk-actions" class="hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-lg border border-gray-200 px-6 py-4">
        <div class="flex items-center gap-4">
            <span class="text-sm font-medium text-gray-700">
                <span id="selected-count">0</span> selected
            </span>
            <div class="flex gap-2">
                <select id="bulk-preference" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Select Preference</option>
                    <option value="order_updates">Order Updates</option>
                    <option value="promotions">Promotions</option>
                    <option value="newsletter">Newsletter</option>
                    <option value="recommendations">Recommendations</option>
                </select>
                <button onclick="bulkUpdate(true)" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                    <i class="fas fa-check mr-1"></i> Enable
                </button>
                <button onclick="bulkUpdate(false)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                    <i class="fas fa-times mr-1"></i> Disable
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle individual preference
async function togglePreference(userId, preference, currentValue) {
    const newValue = !currentValue;
    
    try {
        const response = await fetch(`/admin/email-preferences/${userId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                preference: preference,
                enabled: newValue
            })
        });

        const data = await response.json();

        if (data.success) {
            // Reload page to update UI
            location.reload();
        } else {
            alert(data.message || 'Failed to update preference');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while updating preference');
    }
}

// Bulk update preferences
async function bulkUpdate(enabled) {
    const preference = document.getElementById('bulk-preference').value;
    if (!preference) {
        alert('Please select a preference type');
        return;
    }

    const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    if (selectedIds.length === 0) {
        alert('Please select at least one customer');
        return;
    }

    if (!confirm(`Are you sure you want to ${enabled ? 'enable' : 'disable'} ${preference.replace('_', ' ')} for ${selectedIds.length} customer(s)?`)) {
        return;
    }

    try {
        const response = await fetch('/admin/email-preferences/bulk-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                user_ids: selectedIds,
                preference: preference,
                enabled: enabled
            })
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Failed to update preferences');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while updating preferences');
    }
}

// Export preferences
function exportPreferences(format) {
    window.location.href = `/admin/email-preferences/export?format=${format}`;
}

// Handle checkbox selection
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');

    selectAll?.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const selected = document.querySelectorAll('.user-checkbox:checked').length;
        selectedCount.textContent = selected;
        
        if (selected > 0) {
            bulkActions.classList.remove('hidden');
        } else {
            bulkActions.classList.add('hidden');
        }

        selectAll.checked = selected === checkboxes.length && checkboxes.length > 0;
    }
});
</script>
@endpush
@endsection
