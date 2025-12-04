@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.email-preferences.index') }}" class="text-blue-600 hover:text-blue-700 mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Email Preferences
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Newsletter Subscribers</h1>
                <p class="mt-1 text-sm text-gray-600">Customers who opted in to receive newsletters</p>
            </div>
            <div class="flex gap-3">
                <button onclick="copyEmails()" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-copy"></i>
                    Copy All Emails
                </button>
                <button onclick="exportSubscribers()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-download"></i>
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Subscribers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($subscribers->total()) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Emails</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($subscribers->total()) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-newspaper text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Ready for Campaign</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($subscribers->total()) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscribers List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-5 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Subscriber List</h3>
            <p class="text-sm text-gray-600 mt-1">All customers who subscribed to your newsletter</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subscribed Since</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subscribers as $subscriber)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-500 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($subscriber->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $subscriber->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $subscriber->email }}</div>
                            <button onclick="copyToClipboard('{{ $subscriber->email }}')" 
                                    class="text-xs text-blue-600 hover:text-blue-700 mt-1">
                                <i class="fas fa-copy mr-1"></i>Copy
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $subscriber->created_at->format('M d, Y') }}
                            <span class="text-xs text-gray-400 block">{{ $subscriber->created_at->diffForHumans() }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-gray-300 text-5xl mb-3"></i>
                                <p class="text-gray-500 text-lg">No newsletter subscribers yet</p>
                                <p class="text-gray-400 text-sm mt-1">Customers who opt in will appear here</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($subscribers->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $subscribers->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Copy single email to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Email copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Failed to copy email');
    });
}

// Copy all emails
function copyEmails() {
    const emails = @json($subscribers->pluck('email')->toArray());
    const emailList = emails.join(', ');
    
    navigator.clipboard.writeText(emailList).then(() => {
        showNotification(`${emails.length} email(s) copied to clipboard!`);
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Failed to copy emails');
    });
}

// Export subscribers
function exportSubscribers() {
    window.location.href = '/admin/email-preferences/export?format=csv&filter=newsletter';
}

// Show notification
function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity';
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>
@endpush
@endsection
