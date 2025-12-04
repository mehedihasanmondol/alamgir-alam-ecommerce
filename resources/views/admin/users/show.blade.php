@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600">Users</a>
            <span>/</span>
            <span class="text-gray-900">User Details</span>
        </div>
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.users.edit', $user->id) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-150">
                    <i class="fas fa-edit mr-2"></i>Edit User
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center">
                    @if($user->media)
                        <img src="{{ $user->media->medium_url }}" 
                             alt="{{ $user->name }}" 
                             class="h-32 w-32 rounded-full object-cover mx-auto mb-4">
                    @elseif($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" 
                             alt="{{ $user->name }}" 
                             class="h-32 w-32 rounded-full object-cover mx-auto mb-4">
                    @else
                        <div class="h-32 w-32 rounded-full bg-blue-500 flex items-center justify-center text-white text-4xl font-bold mx-auto mb-4">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    
                    <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-gray-600 mt-1">ID: #{{ $user->id }}</p>
                    
                    <div class="mt-4 flex justify-center gap-2">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Contact Information</h3>
                        <div class="space-y-2">
                            @if($user->email)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-envelope text-gray-400 w-5"></i>
                                <span class="ml-2 text-gray-900">{{ $user->email }}</span>
                            </div>
                            @endif
                            @if($user->mobile)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-phone text-gray-400 w-5"></i>
                                <span class="ml-2 text-gray-900">{{ $user->mobile }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($user->address || $user->city || $user->country)
                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Address</h3>
                        <div class="text-sm text-gray-900">
                            @if($user->address)
                                <p>{{ $user->address }}</p>
                            @endif
                            @if($user->city || $user->state)
                                <p>{{ $user->city }}{{ $user->state ? ', ' . $user->state : '' }}</p>
                            @endif
                            @if($user->country)
                                <p>{{ $user->country }} {{ $user->postal_code }}</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Account Info</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Joined:</span>
                                <span class="text-gray-900 font-medium">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Last Login:</span>
                                <span class="text-gray-900 font-medium">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</span>
                            </div>
                            @if($user->email_verified_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email Verified:</span>
                                <span class="text-green-600 font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>Yes
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Details & Activity -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Assigned Roles -->
            @if($user->roles->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Roles</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($user->roles as $role)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900">{{ $role->name }}</h4>
                            <span class="px-2 py-1 text-xs rounded-full {{ $role->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $role->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        @if($role->description)
                        <p class="text-sm text-gray-600">{{ $role->description }}</p>
                        @endif
                        <div class="mt-3">
                            <p class="text-xs text-gray-500">{{ $role->permissions->count() }} permissions</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                @if($user->activities->count() > 0)
                <div class="space-y-4">
                    @foreach($user->activities->take(10) as $activity)
                    <div class="flex items-start gap-4 pb-4 border-b border-gray-100 last:border-0">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-history text-blue-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $activity->description }}</p>
                            <div class="mt-1 flex items-center gap-3 text-xs text-gray-500">
                                <span>
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $activity->created_at->diffForHumans() }}
                                </span>
                                @if($activity->ip_address)
                                <span>
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $activity->ip_address }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-history text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No activity recorded yet</p>
                </div>
                @endif
            </div>

            <!-- Social Login Connections -->
            @if($user->google_id || $user->facebook_id || $user->apple_id)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Social Login Connections</h3>
                <div class="space-y-3">
                    @if($user->google_id)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fab fa-google text-2xl text-red-500"></i>
                            <div>
                                <p class="font-medium text-gray-900">Google</p>
                                <p class="text-xs text-gray-500">Connected</p>
                            </div>
                        </div>
                        <span class="text-green-600"><i class="fas fa-check-circle"></i></span>
                    </div>
                    @endif
                    @if($user->facebook_id)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fab fa-facebook text-2xl text-blue-600"></i>
                            <div>
                                <p class="font-medium text-gray-900">Facebook</p>
                                <p class="text-xs text-gray-500">Connected</p>
                            </div>
                        </div>
                        <span class="text-green-600"><i class="fas fa-check-circle"></i></span>
                    </div>
                    @endif
                    @if($user->apple_id)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fab fa-apple text-2xl text-gray-900"></i>
                            <div>
                                <p class="font-medium text-gray-900">Apple</p>
                                <p class="text-xs text-gray-500">Connected</p>
                            </div>
                        </div>
                        <span class="text-green-600"><i class="fas fa-check-circle"></i></span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
