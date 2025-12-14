@extends('layouts.admin')

@section('title', 'YouTube Feedback Management')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <svg class="w-8 h-8 mr-3 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                        </svg>
                        YouTube Feedback Management
                    </h1>
                    <p class="text-gray-600 mt-2">Manage YouTube comment imports and settings</p>
                </div>
                <a href="{{ route('admin.feedback.youtube.setup') }}"
                    class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg transition-colors"
                    title="Setup Guide & Documentation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </a>
            </div>
            <a href="{{ route('admin.site-settings.index') }}#feedback_sites"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Settings
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Comments</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">{{ number_format($stats['pending']) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Approved</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['approved']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Rejected</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ number_format($stats['rejected']) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Import Button --}}
        <div class="flex justify-end">
            <button onclick="openImportModal()" class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Import Comments Now
            </button>
        </div>

        {{-- Comments Datatable --}}
        @livewire('admin.feedback.youtube-comment-table')
    </div>
</div>

{{-- Confirmation Modal --}}
<div id="confirmModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900" id="confirmTitle">Confirm Action</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="confirmMessage"></p>
                </div>
                <div class="flex gap-3 mt-4 px-4">
                    <button onclick="closeConfirmModal()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button id="confirmButton"
                        class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Result Modal --}}
<div id="resultModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full" id="resultIconContainer">
                <svg class="h-6 w-6" id="resultIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
            </div>
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900" id="resultTitle"></h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="resultMessage"></p>
                </div>
                <div class="flex justify-center mt-4">
                    <button onclick="closeResultModal()"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Import Modal --}}
<div id="importModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Import YouTube Comments</h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date (Optional)</label>
                    <input type="date" id="modal_date_from"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Leave empty to import from all time</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date (Optional)</label>
                    <input type="date" id="modal_date_to"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        value="{{ date('Y-m-d') }}">
                    <p class="text-xs text-gray-500 mt-1">Leave empty for current date</p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-blue-800">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        💡 Use date range to import older comments or specific time periods. Duplicates will be automatically skipped.
                    </p>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button onclick="closeImportModal()"
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button onclick="startImportFromModal()" id="modal-import-btn"
                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <span id="modal-import-text">Import Now</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Progress Modal --}}
<div id="progressModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-[500px] shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center animate-pulse">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Importing YouTube Comments</h3>
            <p id="progressMessage" class="text-sm text-gray-600 text-center mb-6">Initializing import...</p>

            {{-- Progress Bar --}}
            <div class="mb-6">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Progress</span>
                    <span id="progressPercent">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div id="progressBar" class="bg-blue-600 h-3 rounded-full transition-all duration-500 ease-out" style="width: 0%"></div>
                </div>
            </div>

            {{-- Statistics Grid --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-600">Videos Processed</p>
                    <p class="text-lg font-bold text-gray-900">
                        <span id="videosProcessed">0</span> / <span id="totalVideos">0</span>
                    </p>
                </div>
                <div class="bg-green-50 rounded-lg p-3">
                    <p class="text-xs text-green-600">Comments Imported</p>
                    <p class="text-lg font-bold text-green-900" id="commentsImported">0</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-3">
                    <p class="text-xs text-yellow-600">Duplicates Skipped</p>
                    <p class="text-lg font-bold text-yellow-900" id="duplicatesSkipped">0</p>
                </div>
                <div class="bg-red-50 rounded-lg p-3">
                    <p class="text-xs text-red-600">Errors</p>
                    <p class="text-lg font-bold text-red-900" id="errorsCount">0</p>
                </div>
            </div>

            <div class="text-center">
                <p class="text-xs text-gray-500">Please wait while we import your comments...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Modal Control Functions
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }

    function closeResultModal() {
        document.getElementById('resultModal').classList.add('hidden');
    }

    // Show confirmation modal
    function showConfirm(message, onConfirm) {
        document.getElementById('confirmMessage').textContent = message;
        document.getElementById('confirmModal').classList.remove('hidden');

        document.getElementById('confirmButton').onclick = function() {
            closeConfirmModal();
            onConfirm();
        };
    }

    // Show result modal
    function showResult(title, message, isSuccess = true) {
        const iconContainer = document.getElementById('resultIconContainer');
        const icon = document.getElementById('resultIcon');
        const titleEl = document.getElementById('resultTitle');
        const messageEl = document.getElementById('resultMessage');

        if (isSuccess) {
            iconContainer.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100';
            icon.className = 'h-6 w-6 text-green-600';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
            titleEl.className = 'text-lg font-medium text-green-900';
        } else {
            iconContainer.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100';
            icon.className = 'h-6 w-6 text-red-600';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
            titleEl.className = 'text-lg font-medium text-red-900';
        }

        titleEl.textContent = title;
        messageEl.textContent = message;
        document.getElementById('resultModal').classList.remove('hidden');
    }

    // Close modals when clicking outside
    ['importModal', 'confirmModal', 'resultModal'].forEach(modalId => {
        document.getElementById(modalId)?.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    });

    function testConnection() {
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Testing...';

        fetch('{{ route("admin.feedback.youtube.test") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResult('Connection Successful', data.message, true);
                } else {
                    showResult('Connection Failed', data.message, false);
                }
            })
            .catch(error => {
                showResult('Error', error.message, false);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    }

    function startImportFromModal() {
        const dateFrom = document.getElementById('modal_date_from').value;
        const dateTo = document.getElementById('modal_date_to').value;

        closeImportModal();

        // Construct confirmation message
        let confirmMsg = 'This will import YouTube comments';
        if (dateFrom || dateTo) {
            confirmMsg += ' from ' + (dateFrom || 'all time') + ' to ' + (dateTo || 'now');
        }
        confirmMsg += '. Continue?';

        showConfirm(confirmMsg, function() {
            executeImport(dateFrom, dateTo);
        });
    }

    function executeImport(dateFrom, dateTo) {
        const btn = document.getElementById('modal-import-btn');
        const text = document.getElementById('modal-import-text');
        const originalText = text.textContent;

        btn.disabled = true;
        text.innerHTML = '<svg class="animate-spin w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Importing...';

        fetch('{{ route("admin.feedback.youtube.import") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    date_from: dateFrom,
                    date_to: dateTo
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResult('Import Successful', data.message, true);
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    showResult('Import Failed', data.message, false);
                }
            })
            .catch(error => {
                showResult('Error', error.message, false);
            })
            .finally(() => {
                btn.disabled = false;
                text.textContent = originalText;
            });
    }
</script>
@endpush
@endsection