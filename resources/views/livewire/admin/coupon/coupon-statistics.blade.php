<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.coupons.index') }}" class="hover:text-blue-600">Coupons</a>
            <span>/</span>
            <span class="text-gray-900">{{ $coupon->code }} Statistics</span>
        </div>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Coupon Statistics</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $coupon->name }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Edit Coupon
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Uses -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Uses</h3>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $statistics['total_used'] }}</div>
            @if($coupon->usage_limit)
                <p class="text-sm text-gray-500 mt-1">of {{ $coupon->usage_limit }} limit</p>
            @else
                <p class="text-sm text-gray-500 mt-1">Unlimited</p>
            @endif
        </div>

        <!-- Unique Users -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Unique Users</h3>
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $statistics['unique_users'] }}</div>
            <p class="text-sm text-gray-500 mt-1">Customers</p>
        </div>

        <!-- Total Discount Given -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Discount</h3>
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">৳{{ number_format($statistics['total_discount_given'], 2) }}</div>
            <p class="text-sm text-gray-500 mt-1">Given to customers</p>
        </div>

        <!-- Usage Percentage -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Usage Rate</h3>
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            @if($coupon->usage_limit)
                <div class="text-3xl font-bold text-gray-900">{{ $statistics['usage_percentage'] }}%</div>
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-orange-500 to-red-500 h-2 rounded-full transition-all" 
                             style="width: {{ $statistics['usage_percentage'] }}%"></div>
                    </div>
                </div>
            @else
                <div class="text-3xl font-bold text-gray-900">∞</div>
                <p class="text-sm text-gray-500 mt-1">No limit set</p>
            @endif
        </div>
    </div>

    <!-- Coupon Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Coupon Information -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Coupon Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Code</label>
                    <div class="px-3 py-2 bg-gray-50 rounded border border-gray-200">
                        <code class="text-sm font-bold text-gray-900">{{ $coupon->code }}</code>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Type</label>
                    <div class="px-3 py-2 bg-gray-50 rounded border border-gray-200">
                        <span class="text-sm text-gray-900">{{ ucfirst($coupon->type) }}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Discount Value</label>
                    <div class="px-3 py-2 bg-gray-50 rounded border border-gray-200">
                        <span class="text-sm font-semibold text-gray-900">{{ $coupon->formatted_value }}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                    <div class="px-3 py-2 bg-gray-50 rounded border border-gray-200">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $coupon->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                @if($coupon->min_purchase_amount)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Min. Purchase</label>
                        <div class="px-3 py-2 bg-gray-50 rounded border border-gray-200">
                            <span class="text-sm text-gray-900">৳{{ number_format($coupon->min_purchase_amount, 2) }}</span>
                        </div>
                    </div>
                @endif
                @if($coupon->max_discount_amount)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Max. Discount</label>
                        <div class="px-3 py-2 bg-gray-50 rounded border border-gray-200">
                            <span class="text-sm text-gray-900">৳{{ number_format($coupon->max_discount_amount, 2) }}</span>
                        </div>
                    </div>
                @endif
                @if($coupon->starts_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Start Date</label>
                        <div class="px-3 py-2 bg-gray-50 rounded border border-gray-200">
                            <span class="text-sm text-gray-900">{{ $coupon->starts_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                @endif
                @if($coupon->expires_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Expiry Date</label>
                        <div class="px-3 py-2 bg-gray-50 rounded border border-gray-200">
                            <span class="text-sm text-gray-900">{{ $coupon->expires_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Features -->
            <div class="mt-4 flex flex-wrap gap-2">
                @if($coupon->free_shipping)
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                        Free Shipping
                    </span>
                @endif
                @if($coupon->first_order_only)
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">
                        First Order Only
                    </span>
                @endif
                @if($statistics['remaining_uses'] !== null)
                    <span class="px-3 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded-full">
                        {{ $statistics['remaining_uses'] }} Uses Remaining
                    </span>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                   class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition">
                    Edit Coupon
                </a>
                <button wire:click="$dispatch('toggle-status', { id: {{ $coupon->id }} })"
                        class="block w-full px-4 py-2 {{ $coupon->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-center rounded-lg transition">
                    {{ $coupon->is_active ? 'Deactivate' : 'Activate' }}
                </button>
                <a href="{{ route('admin.coupons.index') }}" 
                   class="block w-full px-4 py-2 bg-gray-100 text-gray-700 text-center rounded-lg hover:bg-gray-200 transition">
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Usage -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Usage History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Used At</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentUsage as $usage)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold">{{ substr($usage->name, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $usage->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $usage->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($usage->pivot->order_id)
                                    <a href="{{ route('admin.orders.show', $usage->pivot->order_id) }}" 
                                       class="text-blue-600 hover:text-blue-900 font-medium">
                                        #{{ $usage->pivot->order_id }}
                                    </a>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-green-600">৳{{ number_format($usage->pivot->discount_amount, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($usage->pivot->used_at)->format('M d, Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p>No usage history yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
