@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">
                    <i class="fas fa-arrow-up mr-1"></i>{{ $newUsersThisMonth }}
                </span>
                <span class="text-gray-600 ml-2">new this month</span>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($activeUsers) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-user-check text-2xl text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-600">
                    {{ $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0 }}% of total
                </span>
            </div>
        </div>

        <!-- Inactive Users -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Inactive Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($inactiveUsers) }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-user-times text-2xl text-red-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-600">
                    {{ $totalUsers > 0 ? round(($inactiveUsers / $totalUsers) * 100, 1) : 0 }}% of total
                </span>
            </div>
        </div>

        <!-- Total Roles -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Roles</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $roleDistribution->count() }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-shield-alt text-2xl text-purple-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <a href="{{ route('admin.roles.index') }}" class="text-purple-600 hover:text-purple-800 font-medium">
                    Manage Roles <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Data -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- User Growth Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line text-blue-600 mr-2"></i>User Growth (Last 7 Days)
            </h3>
            <div class="space-y-3">
                @foreach($userGrowth as $day)
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 w-20">{{ $day['date'] }}</span>
                    <div class="flex-1 mx-4">
                        <div class="bg-gray-200 rounded-full h-4 overflow-hidden">
                            <div class="bg-blue-500 h-full rounded-full transition-all duration-300" 
                                 style="width: {{ $day['count'] > 0 ? min(($day['count'] / max(array_column($userGrowth, 'count'))) * 100, 100) : 0 }}%">
                            </div>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 w-12 text-right">{{ $day['count'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Role Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-pie text-purple-600 mr-2"></i>Role Distribution
            </h3>
            <div class="space-y-4">
                @foreach($roleDistribution as $role)
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700 capitalize">{{ $role->role }}</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $role->count }} users</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300 {{ $role->role === 'admin' ? 'bg-purple-500' : 'bg-blue-500' }}" 
                             style="width: {{ $totalUsers > 0 ? ($role->count / $totalUsers) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Users and Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-user-plus text-green-600 mr-2"></i>Recent Users
                    </h3>
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentUsers as $user)
                <div class="p-4 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
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
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-users text-4xl mb-2 text-gray-300"></i>
                    <p>No users yet</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-history text-orange-600 mr-2"></i>Recent Activities
                </h3>
            </div>
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($recentActivities as $activity)
                <div class="p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full flex items-center justify-center
                                {{ $activity->activity_type === 'login' ? 'bg-green-100' : '' }}
                                {{ $activity->activity_type === 'logout' ? 'bg-gray-100' : '' }}
                                {{ $activity->activity_type === 'created' ? 'bg-blue-100' : '' }}
                                {{ $activity->activity_type === 'updated' ? 'bg-yellow-100' : '' }}
                                {{ $activity->activity_type === 'deleted' ? 'bg-red-100' : '' }}">
                                <i class="fas 
                                    {{ $activity->activity_type === 'login' ? 'fa-sign-in-alt text-green-600' : '' }}
                                    {{ $activity->activity_type === 'logout' ? 'fa-sign-out-alt text-gray-600' : '' }}
                                    {{ $activity->activity_type === 'created' ? 'fa-plus text-blue-600' : '' }}
                                    {{ $activity->activity_type === 'updated' ? 'fa-edit text-yellow-600' : '' }}
                                    {{ $activity->activity_type === 'deleted' ? 'fa-trash text-red-600' : '' }}
                                    text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">
                                <span class="font-medium">{{ $activity->user->name ?? 'Unknown' }}</span>
                                <span class="text-gray-600">{{ $activity->description }}</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-clock mr-1"></i>{{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-history text-4xl mb-2 text-gray-300"></i>
                    <p>No activities yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Active Users -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-star text-yellow-500 mr-2"></i>Top Active Users
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activities</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Active</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topActiveUsers as $index => $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($index === 0)
                                    <i class="fas fa-trophy text-yellow-500 text-xl"></i>
                                @elseif($index === 1)
                                    <i class="fas fa-medal text-gray-400 text-xl"></i>
                                @elseif($index === 2)
                                    <i class="fas fa-medal text-orange-600 text-xl"></i>
                                @else
                                    <span class="text-gray-600 font-semibold">{{ $index + 1 }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
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
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm font-semibold text-gray-900">{{ $user->activities_count }}</span>
                                <span class="text-xs text-gray-500 ml-2">activities</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                View Profile
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                            <p class="text-lg">No active users yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
