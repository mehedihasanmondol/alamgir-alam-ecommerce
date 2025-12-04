@extends('layouts.admin')

@section('title', 'Theme Settings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Theme Settings</h1>
                <p class="text-sm text-gray-600 mt-1">Manage your website's color theme with Tailwind CSS classes</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                    Active: {{ $activeTheme->label ?? 'None' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Theme Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($themes as $theme)
        <div class="bg-white rounded-lg shadow-sm border-2 {{ $theme->is_active ? 'border-blue-500' : 'border-gray-200' }} overflow-hidden">
            <!-- Theme Preview -->
            <div class="p-6 {{ $theme->primary_bg }} bg-gradient-to-br from-opacity-90">
                <div class="space-y-3">
                    <!-- Header Preview -->
                    <div class="bg-white/90 backdrop-blur rounded-lg p-3 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold {{ $theme->primary_text }}">{{ $theme->label }}</h3>
                            @if($theme->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">Active</span>
                            @endif
                        </div>
                    </div>

                    <!-- Button Preview -->
                    <div class="space-y-2">
                        <div class="{{ $theme->button_primary_bg }} {{ $theme->button_primary_text }} px-4 py-2 rounded-lg text-sm font-medium text-center">
                            Primary Button
                        </div>
                        <div class="{{ $theme->button_secondary_bg }} {{ $theme->button_secondary_text }} px-4 py-2 rounded-lg text-sm font-medium text-center border {{ $theme->button_secondary_border }}">
                            Secondary Button
                        </div>
                    </div>

                    <!-- Badge Preview -->
                    <div class="flex space-x-2">
                        <span class="{{ $theme->badge_success_bg }} {{ $theme->badge_success_text }} text-xs px-2 py-1 rounded">Success</span>
                        <span class="{{ $theme->badge_danger_bg }} {{ $theme->badge_danger_text }} text-xs px-2 py-1 rounded">Error</span>
                        <span class="{{ $theme->badge_primary_bg }} {{ $theme->badge_primary_text }} text-xs px-2 py-1 rounded">Info</span>
                    </div>
                </div>
            </div>

            <!-- Theme Info & Actions -->
            <div class="p-4 bg-gray-50">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $theme->label }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $theme->is_predefined ? 'Predefined' : 'Custom' }} Theme
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-2">
                    @if(!$theme->is_active)
                        <form action="{{ route('admin.theme-settings.activate', $theme->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                Activate
                            </button>
                        </form>
                    @else
                        <button disabled class="w-full px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg cursor-not-allowed">
                            Active
                        </button>
                    @endif

                    <a href="{{ route('admin.theme-settings.edit', $theme->id) }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg text-center transition-colors">
                        Customize
                    </a>
                </div>

                <!-- Additional Actions -->
                <div class="mt-2 flex space-x-2">
                    @if(!$theme->is_active && !$theme->is_predefined)
                        <form action="{{ route('admin.theme-settings.destroy', $theme->id) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this theme?')" class="w-full px-3 py-1 bg-red-100 hover:bg-red-200 text-red-600 text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </form>
                    @endif

                    @if($theme->is_predefined)
                        <form action="{{ route('admin.theme-settings.reset', $theme->id) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit" onclick="return confirm('Reset to default colors?')" class="w-full px-3 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 text-xs font-medium rounded transition-colors">
                                Reset
                            </button>
                        </form>
                    @endif

                    <!-- Duplicate Button -->
                    <button onclick="openDuplicateModal({{ $theme->id }}, '{{ $theme->name }}')" class="flex-1 px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded transition-colors">
                        Duplicate
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- How to Use Guide -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-bold text-blue-900 mb-3">📘 How to Use Themes</h3>
        <div class="space-y-2 text-sm text-blue-800">
            <p><strong>In Blade Templates:</strong></p>
            <code class="block bg-white px-3 py-2 rounded text-xs text-gray-800 mt-1 mb-2">
                &lt;button class="{{ theme('button_primary_bg') }} {{ theme('button_primary_text') }}"&gt;Click Me&lt;/button&gt;
            </code>

            <p><strong>Helper Function:</strong></p>
            <code class="block bg-white px-3 py-2 rounded text-xs text-gray-800 mt-1 mb-2">
                theme('primary_bg') // Returns active theme's primary background class
            </code>

            <p><strong>Multiple Classes:</strong></p>
            <code class="block bg-white px-3 py-2 rounded text-xs text-gray-800 mt-1">
                theme_classes(['primary_bg', 'primary_text', 'rounded-lg'])
            </code>
        </div>
    </div>
</div>

<!-- Duplicate Theme Modal -->
<div id="duplicateModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Duplicate Theme</h3>
        <form id="duplicateForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Theme Name (Unique ID)</label>
                    <input type="text" name="name" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., custom-blue">
                    <p class="text-xs text-gray-500 mt-1">Lowercase, no spaces (use hyphens)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Label</label>
                    <input type="text" name="label" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., Custom Blue Theme">
                </div>
            </div>
            <div class="flex space-x-3 mt-6">
                <button type="button" onclick="closeDuplicateModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Duplicate
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openDuplicateModal(themeId, themeName) {
    const modal = document.getElementById('duplicateModal');
    const form = document.getElementById('duplicateForm');
    form.action = `/admin/theme-settings/${themeId}/duplicate`;
    form.querySelector('input[name="name"]').value = themeName + '-copy';
    modal.classList.remove('hidden');
}

function closeDuplicateModal() {
    document.getElementById('duplicateModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('duplicateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDuplicateModal();
    }
});
</script>
@endpush
@endsection
