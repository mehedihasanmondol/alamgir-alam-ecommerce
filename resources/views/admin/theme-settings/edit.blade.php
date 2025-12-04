@extends('layouts.admin')

@section('title', 'Edit Theme - ' . $theme->label)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Theme: {{ $theme->label }}</h1>
                <p class="text-sm text-gray-600 mt-1">Customize theme colors using Tailwind CSS utility classes</p>
            </div>
            <a href="{{ route('admin.theme-settings.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                ← Back to Themes
            </a>
        </div>
    </div>

    <form action="{{ route('admin.theme-settings.update', $theme->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <!-- Basic Info -->
            <div class="mb-6 pb-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Theme Name</label>
                        <input type="text" value="{{ $theme->name }}" disabled class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Cannot change theme identifier</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Label <span class="text-red-500">*</span></label>
                        <input type="text" name="label" value="{{ old('label', $theme->label) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('label')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Color Classes -->
            @foreach($categories as $categoryName => $fields)
            <div class="mb-6 pb-6 border-b border-gray-200 last:border-0">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ $categoryName }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($fields as $fieldKey => $fieldLabel)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $fieldLabel }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="{{ $fieldKey }}" 
                               value="{{ old($fieldKey, $theme->$fieldKey) }}" 
                               required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                               placeholder="e.g., bg-blue-600">
                        @error($fieldKey)
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        
                        <!-- Preview Badge -->
                        <div class="mt-2">
                            <span class="{{ $theme->$fieldKey }} px-3 py-1 rounded text-xs inline-block">Preview</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            <!-- Tailwind Color Reference -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h4 class="text-sm font-bold text-blue-900 mb-2">🎨 Tailwind CSS Color Reference</h4>
                <div class="text-xs text-blue-800 space-y-1">
                    <p><strong>Background:</strong> bg-blue-600, bg-red-500, bg-green-700, bg-gray-100, bg-white, bg-black</p>
                    <p><strong>Text:</strong> text-blue-600, text-red-500, text-green-700, text-gray-900, text-white</p>
                    <p><strong>Border:</strong> border-blue-600, border-red-500, border-gray-300</p>
                    <p><strong>Hover:</strong> hover:bg-blue-700, hover:text-red-600</p>
                    <p><strong>Focus:</strong> focus:ring-blue-500, focus:border-blue-500</p>
                    <p><strong>Opacity:</strong> bg-opacity-75, text-opacity-50</p>
                    <p class="mt-2"><a href="https://tailwindcss.com/docs/customizing-colors" target="_blank" class="text-blue-600 hover:underline">View Full Tailwind Color Palette →</a></p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.theme-settings.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Save Theme
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
