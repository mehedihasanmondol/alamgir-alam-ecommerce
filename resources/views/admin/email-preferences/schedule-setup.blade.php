@extends('layouts.admin')

@section('title', 'Email Schedule Setup')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Email Schedule Setup</h1>
            <p class="text-gray-600 mt-1">Configure automated email campaign schedules</p>
        </div>
        <a href="{{ route('admin.email-preferences.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <!-- Alert -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-info-circle text-blue-400 mt-1"></i>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Note:</strong> Changes to schedules require updating <code>bootstrap/app.php</code> and running <code>php artisan config:clear</code>
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Newsletter Schedule -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-newspaper text-green-600 mr-3"></i>
                    Newsletter
                </h2>
            </div>
            <div class="p-6">
                <form id="newsletter-schedule">
                    <input type="hidden" name="type" value="newsletter">
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="enabled" class="rounded text-green-600 mr-2" {{ $schedules['newsletter']['enabled'] ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-gray-700">Enable Automated Sending</span>
                        </label>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Frequency</label>
                        <select name="frequency" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="daily" {{ $schedules['newsletter']['frequency'] == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ $schedules['newsletter']['frequency'] == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ $schedules['newsletter']['frequency'] == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Day of Week</label>
                        <select name="day" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="0" {{ $schedules['newsletter']['day'] == 0 ? 'selected' : '' }}>Sunday</option>
                            <option value="1" {{ $schedules['newsletter']['day'] == 1 ? 'selected' : '' }}>Monday</option>
                            <option value="2" {{ $schedules['newsletter']['day'] == 2 ? 'selected' : '' }}>Tuesday</option>
                            <option value="3" {{ $schedules['newsletter']['day'] == 3 ? 'selected' : '' }}>Wednesday</option>
                            <option value="4" {{ $schedules['newsletter']['day'] == 4 ? 'selected' : '' }}>Thursday</option>
                            <option value="5" {{ $schedules['newsletter']['day'] == 5 ? 'selected' : '' }}>Friday</option>
                            <option value="6" {{ $schedules['newsletter']['day'] == 6 ? 'selected' : '' }}>Saturday</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Time</label>
                        <input type="time" name="time" value="{{ $schedules['newsletter']['time'] }}" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Timezone</label>
                        <select name="timezone" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="Asia/Dhaka" {{ $schedules['newsletter']['timezone'] == 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka (UTC+6)</option>
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">America/New York (EST)</option>
                            <option value="Europe/London">Europe/London (GMT)</option>
                        </select>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg mb-4">
                        <p class="text-xs text-gray-600">
                            <strong>Current:</strong> Every Monday at 09:00 AM
                        </p>
                    </div>

                    <button type="button" onclick="saveSchedule('newsletter')" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Save Schedule
                    </button>
                </form>
            </div>
        </div>

        <!-- Promotional Schedule -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-red-50">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-percentage text-orange-600 mr-3"></i>
                    Promotional
                </h2>
            </div>
            <div class="p-6">
                <form id="promotional-schedule">
                    <input type="hidden" name="type" value="promotional">
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="enabled" class="rounded text-orange-600 mr-2" {{ $schedules['promotional']['enabled'] ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-gray-700">Enable Automated Sending</span>
                        </label>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Frequency</label>
                        <select name="frequency" class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                            <option value="daily" {{ $schedules['promotional']['frequency'] == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ $schedules['promotional']['frequency'] == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ $schedules['promotional']['frequency'] == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Day of Week</label>
                        <select name="day" class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                            <option value="0" {{ $schedules['promotional']['day'] == 0 ? 'selected' : '' }}>Sunday</option>
                            <option value="1" {{ $schedules['promotional']['day'] == 1 ? 'selected' : '' }}>Monday</option>
                            <option value="2" {{ $schedules['promotional']['day'] == 2 ? 'selected' : '' }}>Tuesday</option>
                            <option value="3" {{ $schedules['promotional']['day'] == 3 ? 'selected' : '' }}>Wednesday</option>
                            <option value="4" {{ $schedules['promotional']['day'] == 4 ? 'selected' : '' }}>Thursday</option>
                            <option value="5" {{ $schedules['promotional']['day'] == 5 ? 'selected' : '' }}>Friday</option>
                            <option value="6" {{ $schedules['promotional']['day'] == 6 ? 'selected' : '' }}>Saturday</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Time</label>
                        <input type="time" name="time" value="{{ $schedules['promotional']['time'] }}" class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Timezone</label>
                        <select name="timezone" class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                            <option value="Asia/Dhaka" {{ $schedules['promotional']['timezone'] == 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka (UTC+6)</option>
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">America/New York (EST)</option>
                            <option value="Europe/London">Europe/London (GMT)</option>
                        </select>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg mb-4">
                        <p class="text-xs text-gray-600">
                            <strong>Current:</strong> Every Friday at 10:00 AM
                        </p>
                    </div>

                    <button type="button" onclick="saveSchedule('promotional')" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Save Schedule
                    </button>
                </form>
            </div>
        </div>

        <!-- Recommendation Schedule -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-star text-purple-600 mr-3"></i>
                    Recommendation
                </h2>
            </div>
            <div class="p-6">
                <form id="recommendation-schedule">
                    <input type="hidden" name="type" value="recommendation">
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="enabled" class="rounded text-purple-600 mr-2" {{ $schedules['recommendation']['enabled'] ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-gray-700">Enable Automated Sending</span>
                        </label>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Frequency</label>
                        <select name="frequency" class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="daily" {{ $schedules['recommendation']['frequency'] == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ $schedules['recommendation']['frequency'] == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ $schedules['recommendation']['frequency'] == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Day of Week</label>
                        <select name="day" class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="0" {{ $schedules['recommendation']['day'] == 0 ? 'selected' : '' }}>Sunday</option>
                            <option value="1" {{ $schedules['recommendation']['day'] == 1 ? 'selected' : '' }}>Monday</option>
                            <option value="2" {{ $schedules['recommendation']['day'] == 2 ? 'selected' : '' }}>Tuesday</option>
                            <option value="3" {{ $schedules['recommendation']['day'] == 3 ? 'selected' : '' }}>Wednesday</option>
                            <option value="4" {{ $schedules['recommendation']['day'] == 4 ? 'selected' : '' }}>Thursday</option>
                            <option value="5" {{ $schedules['recommendation']['day'] == 5 ? 'selected' : '' }}>Friday</option>
                            <option value="6" {{ $schedules['recommendation']['day'] == 6 ? 'selected' : '' }}>Saturday</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Time</label>
                        <input type="time" name="time" value="{{ $schedules['recommendation']['time'] }}" class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Timezone</label>
                        <select name="timezone" class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="Asia/Dhaka" {{ $schedules['recommendation']['timezone'] == 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka (UTC+6)</option>
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">America/New York (EST)</option>
                            <option value="Europe/London">Europe/London (GMT)</option>
                        </select>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg mb-4">
                        <p class="text-xs text-gray-600">
                            <strong>Current:</strong> Every Wednesday at 02:00 PM
                        </p>
                    </div>

                    <button type="button" onclick="saveSchedule('recommendation')" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Save Schedule
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Cron Setup Reminder -->
    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Important:</strong> Make sure your server cron job is running: <code class="bg-yellow-100 px-2 py-1 rounded">* * * * * php artisan schedule:run</code>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function saveSchedule(type) {
    const form = document.getElementById(`${type}-schedule`);
    const formData = new FormData(form);
    
    // Convert to JSON
    const data = {
        type: formData.get('type'),
        enabled: form.querySelector('[name="enabled"]').checked,
        frequency: formData.get('frequency'),
        day: parseInt(formData.get('day')),
        time: formData.get('time'),
        timezone: formData.get('timezone')
    };
    
    // Show loading
    const button = form.querySelector('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    button.disabled = true;
    
    fetch('{{ route("admin.email-preferences.update-schedule") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('✅ ' + result.message);
        } else {
            alert('❌ ' + result.message);
        }
    })
    .catch(error => {
        alert('❌ Failed to save schedule');
        console.error(error);
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}
</script>
@endsection
