@extends('layouts.admin')

@section('title', 'Edit Chamber')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Top Bar -->
    <div class="bg-white border-b border-gray-200 -mx-4 -mt-6 px-4 py-3 mb-6 sticky top-16 z-10 shadow-sm">
        <div class="flex items-center justify-between max-w-5xl mx-auto">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.chambers.index') }}" 
                   class="text-gray-600 hover:text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Chambers
                </a>
                <span class="text-gray-300">|</span>
                <h1 class="text-xl font-semibold text-gray-900">Edit: {{ $chamber->name }}</h1>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.chambers.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" form="chamber-form"
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Update Chamber
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto">
        <form id="chamber-form" action="{{ route('admin.chambers.update', $chamber) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                        
                        <!-- Chamber Name -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Chamber Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', $chamber->name) }}" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., Dhaka Chamber">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Address
                            </label>
                            <textarea name="address" 
                                      rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Full address of the chamber">{{ old('address', $chamber->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone & Email -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone
                                </label>
                                <input type="text" 
                                       name="phone" 
                                       value="{{ old('phone', $chamber->phone) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="01700000000">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input type="email" 
                                       name="email" 
                                       value="{{ old('email', $chamber->email) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="chamber@example.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea name="description" 
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Brief description about the chamber">{{ old('description', $chamber->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Operating Hours -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Operating Hours</h2>
                        
                        @php
                            $days = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                            $dayLabels = [
                                'saturday' => 'Saturday',
                                'sunday' => 'Sunday',
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday'
                            ];
                            $operatingHours = old('operating_hours', $chamber->operating_hours ?? []);
                        @endphp

                        <div class="space-y-3">
                            @foreach($days as $day)
                                @php
                                    $hours = $operatingHours[$day] ?? ['is_open' => false];
                                @endphp
                                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                                    <div class="w-32">
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   name="operating_hours[{{ $day }}][is_open]" 
                                                   value="1"
                                                   {{ ($hours['is_open'] ?? false) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-sm font-medium text-gray-700">{{ $dayLabels[$day] }}</span>
                                        </label>
                                    </div>
                                    <div class="flex-1 flex items-center gap-2">
                                        <input type="time" 
                                               name="operating_hours[{{ $day }}][open]" 
                                               value="{{ $hours['open'] ?? '09:00' }}"
                                               class="px-3 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <span class="text-gray-500">to</span>
                                        <input type="time" 
                                               name="operating_hours[{{ $day }}][close]" 
                                               value="{{ $hours['close'] ?? '17:00' }}"
                                               class="px-3 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Break Time -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Break Time (Optional)</h2>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Break Start (minutes from midnight)
                                </label>
                                <input type="number" 
                                       name="break_start" 
                                       value="{{ old('break_start', $chamber->break_start) }}"
                                       min="0"
                                       max="1440"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="780 (1:00 PM)">
                                <p class="mt-1 text-xs text-gray-500">Example: 780 = 1:00 PM (13 × 60)</p>
                                @error('break_start')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Break Duration (minutes)
                                </label>
                                <input type="number" 
                                       name="break_duration" 
                                       value="{{ old('break_duration', $chamber->break_duration) }}"
                                       min="0"
                                       max="180"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="60">
                                @error('break_duration')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Settings -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Settings</h3>
                        
                        <!-- Slot Duration -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Slot Duration (minutes) <span class="text-red-500">*</span>
                            </label>
                            <select name="slot_duration" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="15" {{ old('slot_duration', $chamber->slot_duration) == 15 ? 'selected' : '' }}>15 minutes</option>
                                <option value="30" {{ old('slot_duration', $chamber->slot_duration) == 30 ? 'selected' : '' }}>30 minutes</option>
                                <option value="45" {{ old('slot_duration', $chamber->slot_duration) == 45 ? 'selected' : '' }}>45 minutes</option>
                                <option value="60" {{ old('slot_duration', $chamber->slot_duration) == 60 ? 'selected' : '' }}>1 hour</option>
                            </select>
                            @error('slot_duration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $chamber->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Enable appointment booking for this chamber</p>
                        </div>

                        <!-- Display Order -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Display Order
                            </label>
                            <input type="number" 
                                   name="display_order" 
                                   value="{{ old('display_order', $chamber->display_order) }}"
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                            @error('display_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Statistics</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Appointments:</span>
                                <span class="font-semibold">{{ $chamber->appointments()->count() }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Pending:</span>
                                <span class="font-semibold text-orange-600">{{ $chamber->appointments()->where('status', 'pending')->count() }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Confirmed:</span>
                                <span class="font-semibold text-blue-600">{{ $chamber->appointments()->where('status', 'confirmed')->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
