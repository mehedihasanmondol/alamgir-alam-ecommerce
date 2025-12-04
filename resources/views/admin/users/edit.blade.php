@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header with Animation -->
    <div class="mb-8 animate-fade-in">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                    <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Users
                    </a>
                    <span>/</span>
                    <span class="text-gray-900 font-medium">Edit User</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Edit User: {{ $user->name }}
                </h1>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>

    <div class="max-w-5xl">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-xl">
            @csrf
            @method('PUT')

            <div class="p-8 space-y-8">
                <!-- Basic Information -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b-2 border-blue-100">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Basic Information</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-blue-600 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Full Name <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('name') border-red-500 @enderror"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-blue-600 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Email
                                </span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-blue-600 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    Mobile
                                </span>
                            </label>
                            <input type="text" 
                                   name="mobile" 
                                   value="{{ old('mobile', $user->mobile) }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('mobile') border-red-500 @enderror">
                            @error('mobile')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-blue-600 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Password <span class="text-gray-500 text-xs font-normal">(Leave blank to keep current)</span>
                                </span>
                            </label>
                            <input type="password" 
                                   name="password" 
                                   placeholder="Minimum 4 characters"
                                   autocomplete="new-password"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('password') border-red-500 @enderror">
                            <p class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Minimum 4 characters required
                            </p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-blue-600 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Role <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <select name="role" 
                                    id="user-role-select"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('role') border-red-500 @enderror"
                                    onchange="toggleAuthorSection()"
                                    required>
                                <option value="">Select Role</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="author" {{ old('role', $user->role) == 'author' ? 'selected' : '' }}>Author</option>
                                <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Customer</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="group md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 group-hover:text-blue-600 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Avatar
                                </span>
                            </label>
                            
                            @livewire('admin.user.user-avatar-handler', ['user' => $user])
                            
                            @error('media_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b-2 border-green-100">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Address Information</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2 group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-green-600 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Address
                                </span>
                            </label>
                            <textarea name="address" 
                                      rows="3"
                                      placeholder="Enter full address"
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('address') border-red-500 @enderror">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-green-600 transition-colors">City</label>
                            <input type="text" 
                                   name="city" 
                                   value="{{ old('city', $user->city) }}"
                                   placeholder="City name"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('city') border-red-500 @enderror">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-green-600 transition-colors">State</label>
                            <input type="text" 
                                   name="state" 
                                   value="{{ old('state', $user->state) }}"
                                   placeholder="State/Province"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('state') border-red-500 @enderror">
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-green-600 transition-colors">Country</label>
                            <input type="text" 
                                   name="country" 
                                   value="{{ old('country', $user->country) }}"
                                   placeholder="Country name"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('country') border-red-500 @enderror">
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-green-600 transition-colors">Postal Code</label>
                            <input type="text" 
                                   name="postal_code" 
                                   value="{{ old('postal_code', $user->postal_code) }}"
                                   placeholder="ZIP/Postal code"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('postal_code') border-red-500 @enderror">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Roles -->
                @if($roles->count() > 0)
                <div class="space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b-2 border-purple-100">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Additional Roles</h3>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                        <label class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-300 cursor-pointer transition-all duration-200 group">
                            <input type="checkbox" 
                                   name="roles[]" 
                                   value="{{ $role->id }}"
                                   {{ $user->roles->contains($role->id) || in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 w-5 h-5">
                            <span class="text-sm font-semibold text-gray-700 group-hover:text-purple-700">{{ $role->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Author Information (Only visible for Author role) -->
                <div id="author-info-section" 
                     class="space-y-6 {{ old('role', $user->role) == 'author' || $user->hasRole('author') ? '' : 'hidden' }}"
                     x-data="{ showSection: {{ old('role', $user->role) == 'author' || $user->hasRole('author') ? 'true' : 'false' }} }">
                    <div class="flex items-center gap-3 pb-4 border-b-2 border-orange-100">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Author Profile Information</h3>
                        <span class="ml-auto text-xs bg-orange-100 text-orange-800 px-3 py-1 rounded-full font-semibold">
                            Author-Specific Fields
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Bio -->
                        <div class="md:col-span-2 group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-orange-600 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                    </svg>
                                    Author Bio
                                </span>
                            </label>
                            <textarea name="author_bio" 
                                      rows="4"
                                      placeholder="Write a brief bio about this author"
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">{{ old('author_bio', $user->authorProfile->bio ?? '') }}</textarea>
                        </div>

                        <!-- Job Title -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-orange-600 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Job Title
                                </span>
                            </label>
                            <input type="text" 
                                   name="author_job_title" 
                                   value="{{ old('author_job_title', $user->authorProfile->job_title ?? '') }}"
                                   placeholder="e.g., Health Writer, Nutritionist"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                        </div>

                        <!-- Website -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-orange-600 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                    </svg>
                                    Website URL
                                </span>
                            </label>
                            <input type="url" 
                                   name="author_website" 
                                   value="{{ old('author_website', $user->authorProfile->website ?? '') }}"
                                   placeholder="https://example.com"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                        </div>

                        <!-- Social Links -->
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                                Social Media Links
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Twitter -->
                                <div class="group">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Twitter Handle</label>
                                    <div class="flex items-center">
                                        <span class="px-3 py-3 bg-gray-100 border-2 border-r-0 border-gray-200 rounded-l-lg text-gray-600 text-sm">@</span>
                                        <input type="text" 
                                               name="author_twitter" 
                                               value="{{ old('author_twitter', $user->authorProfile->twitter ?? '') }}"
                                               placeholder="username"
                                               class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-r-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                                    </div>
                                </div>

                                <!-- Facebook -->
                                <div class="group">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Facebook Username</label>
                                    <input type="text" 
                                           name="author_facebook" 
                                           value="{{ old('author_facebook', $user->authorProfile->facebook ?? '') }}"
                                           placeholder="username"
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                                </div>

                                <!-- LinkedIn -->
                                <div class="group">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">LinkedIn Username</label>
                                    <input type="text" 
                                           name="author_linkedin" 
                                           value="{{ old('author_linkedin', $user->authorProfile->linkedin ?? '') }}"
                                           placeholder="username"
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                                </div>

                                <!-- Instagram -->
                                <div class="group">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Instagram Handle</label>
                                    <div class="flex items-center">
                                        <span class="px-3 py-3 bg-gray-100 border-2 border-r-0 border-gray-200 rounded-l-lg text-gray-600 text-sm">@</span>
                                        <input type="text" 
                                               name="author_instagram" 
                                               value="{{ old('author_instagram', $user->authorProfile->instagram ?? '') }}"
                                               placeholder="username"
                                               class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-r-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                                    </div>
                                </div>

                                <!-- GitHub -->
                                <div class="group">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">GitHub Username</label>
                                    <input type="text" 
                                           name="author_github" 
                                           value="{{ old('author_github', $user->authorProfile->github ?? '') }}"
                                           placeholder="username"
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                                </div>

                                <!-- YouTube -->
                                <div class="group">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">YouTube Channel</label>
                                    <div class="flex items-center">
                                        <span class="px-3 py-3 bg-gray-100 border-2 border-r-0 border-gray-200 rounded-l-lg text-gray-600 text-sm">@</span>
                                        <input type="text" 
                                               name="author_youtube" 
                                               value="{{ old('author_youtube', $user->authorProfile->youtube ?? '') }}"
                                               placeholder="channelname"
                                               class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-r-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                                    </div>
                                </div>

                                <!-- WhatsApp -->
                                <div class="group">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">WhatsApp Number</label>
                                    <input type="text" 
                                           name="author_whatsapp" 
                                           value="{{ old('author_whatsapp', $user->authorProfile->whatsapp ?? '') }}"
                                           placeholder="8801XXXXXXXXX (with country code)"
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                                    <p class="text-xs text-gray-500 mt-1">Include country code (e.g., 8801712345678)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Author Avatar -->
                        <div class="md:col-span-2 group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 group-hover:text-orange-600 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Author Profile Picture
                                    <span class="text-xs text-gray-500 font-normal">(Optional - separate from user avatar)</span>
                                </span>
                            </label>
                            
                            @livewire('admin.user.author-avatar-handler', ['authorProfile' => $user->authorProfile])
                            
                            @error('author_media_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Featured Author -->
                        <div class="md:col-span-2 bg-gradient-to-r from-orange-50 to-yellow-50 p-4 rounded-xl border-2 border-orange-100">
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <input type="checkbox" 
                                       name="author_is_featured" 
                                       value="1"
                                       {{ old('author_is_featured', $user->authorProfile->is_featured ?? false) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-5 h-5">
                                <div>
                                    <span class="text-sm font-bold text-gray-900 group-hover:text-orange-700 transition-colors">Featured Author</span>
                                    <p class="text-xs text-gray-600 mt-0.5">Display this author in the featured authors section</p>
                                </div>
                            </label>
                        </div>

                        <!-- Display Order -->
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 group-hover:text-orange-600 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                    </svg>
                                    Display Order
                                </span>
                            </label>
                            <input type="number" 
                                   name="author_display_order" 
                                   value="{{ old('author_display_order', $user->authorProfile->display_order ?? 0) }}"
                                   min="0"
                                   placeholder="0"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first in lists</p>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border-2 border-blue-100">
                    <label class="flex items-center space-x-4 cursor-pointer group">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-6 h-6">
                        <div>
                            <span class="text-base font-bold text-gray-900 group-hover:text-blue-700 transition-colors">Active User</span>
                            <p class="text-xs text-gray-600 mt-1">Enable this user to access the system</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-gray-100 border-t-2 border-gray-200 flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Last updated: {{ $user->updated_at->diffForHumans() }}
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.users.index') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-white hover:border-gray-400 font-semibold transition-all duration-200 shadow-sm hover:shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update User
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Universal Image Uploader -->
<livewire:universal-image-uploader />

@endsection

@push('scripts')
<script>

function toggleAuthorSection() {
    const roleSelect = document.getElementById('user-role-select');
    const authorSection = document.getElementById('author-info-section');
    
    if (roleSelect && authorSection) {
        const selectedRole = roleSelect.value;
        
        if (selectedRole === 'author') {
            authorSection.classList.remove('hidden');
        } else {
            authorSection.classList.add('hidden');
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleAuthorSection();
});
</script>
@endpush
