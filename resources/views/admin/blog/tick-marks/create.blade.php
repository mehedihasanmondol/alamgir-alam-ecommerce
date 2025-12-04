@extends('layouts.admin')

@section('title', 'Create Tick Mark')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.blog.tick-marks.index') }}" class="hover:text-gray-900">Tick Marks</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span>Create New</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Create New Tick Mark</h1>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.blog.tick-marks.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <!-- Name -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Name <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                value="{{ old('name') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                placeholder="e.g., Hot, Featured, Exclusive"
                required
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Label -->
        <div class="mb-6">
            <label for="label" class="block text-sm font-medium text-gray-700 mb-2">
                Display Label <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                name="label" 
                id="label" 
                value="{{ old('label') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('label') border-red-500 @enderror"
                placeholder="Label shown to users"
                required
            >
            @error('label')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Description
            </label>
            <textarea 
                name="description" 
                id="description" 
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                placeholder="Brief description of this tick mark"
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Icon -->
        <div class="mb-6">
            <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                Icon <span class="text-red-500">*</span>
            </label>
            <select 
                name="icon" 
                id="icon"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('icon') border-red-500 @enderror"
                required
            >
                <option value="check-circle" {{ old('icon') == 'check-circle' ? 'selected' : '' }}>Check Circle</option>
                <option value="star" {{ old('icon') == 'star' ? 'selected' : '' }}>Star</option>
                <option value="trending-up" {{ old('icon') == 'trending-up' ? 'selected' : '' }}>Trending Up</option>
                <option value="crown" {{ old('icon') == 'crown' ? 'selected' : '' }}>Crown</option>
                <option value="flame" {{ old('icon') == 'flame' ? 'selected' : '' }}>Flame</option>
                <option value="sparkles" {{ old('icon') == 'sparkles' ? 'selected' : '' }}>Sparkles</option>
                <option value="badge-check" {{ old('icon') == 'badge-check' ? 'selected' : '' }}>Badge Check</option>
                <option value="lightning-bolt" {{ old('icon') == 'lightning-bolt' ? 'selected' : '' }}>Lightning Bolt</option>
            </select>
            @error('icon')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Color -->
        <div class="mb-6">
            <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                Color <span class="text-red-500">*</span>
            </label>
            <select 
                name="color" 
                id="color"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('color') border-red-500 @enderror"
                required
            >
                <option value="blue" {{ old('color') == 'blue' ? 'selected' : '' }}>Blue</option>
                <option value="purple" {{ old('color') == 'purple' ? 'selected' : '' }}>Purple</option>
                <option value="red" {{ old('color') == 'red' ? 'selected' : '' }}>Red</option>
                <option value="yellow" {{ old('color') == 'yellow' ? 'selected' : '' }}>Yellow</option>
                <option value="green" {{ old('color') == 'green' ? 'selected' : '' }}>Green</option>
                <option value="orange" {{ old('color') == 'orange' ? 'selected' : '' }}>Orange</option>
                <option value="pink" {{ old('color') == 'pink' ? 'selected' : '' }}>Pink</option>
                <option value="indigo" {{ old('color') == 'indigo' ? 'selected' : '' }}>Indigo</option>
                <option value="gray" {{ old('color') == 'gray' ? 'selected' : '' }}>Gray</option>
            </select>
            @error('color')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <input type="hidden" name="bg_color" id="bg_color" value="{{ old('bg_color', 'bg-blue-500') }}">
            <input type="hidden" name="text_color" id="text_color" value="{{ old('text_color', 'text-white') }}">
        </div>

        <!-- Sort Order -->
        <div class="mb-6">
            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                Sort Order
            </label>
            <input 
                type="number" 
                name="sort_order" 
                id="sort_order" 
                value="{{ old('sort_order', 0) }}"
                min="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('sort_order') border-red-500 @enderror"
            >
            @error('sort_order')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
        </div>

        <!-- Active Status -->
        <div class="mb-6">
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    name="is_active" 
                    id="is_active" 
                    value="1"
                    {{ old('is_active', true) ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Active (tick mark will be available for use)
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-6 border-t">
            <a href="{{ route('admin.blog.tick-marks.index') }}" 
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button 
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Create Tick Mark
            </button>
        </div>
    </form>
</div>

<script>
    // Auto-generate bg_color and text_color based on selected color
    document.getElementById('color').addEventListener('change', function() {
        const color = this.value;
        document.getElementById('bg_color').value = 'bg-' + color + '-500';
        document.getElementById('text_color').value = 'text-white';
    });
</script>
@endsection
