@extends('layouts.admin')

@section('title', 'Email Content Editor')

@push('styles')
<style>
/* CKEditor Custom Styling */
.ck-content ul,
.ck-content ol {
    margin-left: 20px;
}

.char-counter {
    margin-top: 10px;
    font-size: 12px;
    color: #6b7280;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- WordPress-style Top Bar -->
    <div class="bg-white border-b border-gray-200 -mx-4 -mt-6 px-4 py-3 mb-6 sticky top-16 z-10 shadow-sm">
        <div class="flex items-center justify-between max-w-7xl mx-auto">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.email-preferences.index') }}" 
                   class="text-gray-600 hover:text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Email Preferences
                </a>
                <span class="text-gray-300">|</span>
                <h1 class="text-xl font-semibold text-gray-900">Email Content Editor</h1>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="loadTemplate('welcome')" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-bolt mr-1"></i> Load Template
                </button>
                <button type="button" onclick="updatePreview()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-eye mr-1"></i> Preview
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Main Editor Area -->
            <div class="lg:col-span-3 space-y-6">
                <form id="email-form">
                    <!-- Email Type & Subject Card -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 space-y-4">
                            <!-- Email Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Type *</label>
                                <select id="email-type" name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="newsletter">📧 Newsletter</option>
                                    <option value="promotional">🎁 Promotional Offer</option>
                                    <option value="recommendation">⭐ Product Recommendation</option>
                                </select>
                            </div>

                            <!-- Subject -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subject Line *</label>
                                <input type="text" 
                                       id="email-subject" 
                                       name="subject" 
                                       placeholder="Enter email subject..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       maxlength="100">
                                <div class="mt-1 flex justify-between text-xs text-gray-500">
                                    <span>💡 Keep it under 50 characters for best results</span>
                                    <span id="subject-length">0/100</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-4 mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Email Content *</label>
                            <textarea name="content" 
                                      id="ckeditor" 
                                      class="ckeditor-content"></textarea>
                            
                            <!-- Word Counter -->
                            <div class="char-counter" id="word-count"></div>


                        </div>
                    </div>

                
                    <!-- Test Email Section -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-vial text-blue-600 mr-2"></i>
                            Send Test Email
                        </h3>
                        <div class="space-y-3">
                            <input type="email" 
                                   id="test-email" 
                                   placeholder="Enter test email address..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <button type="button" 
                                    onclick="sendTestEmail()" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg transition font-semibold shadow-md hover:shadow-lg">
                                <i class="fas fa-paper-plane mr-2"></i>Send Test Email
                            </button>
                            <p class="text-xs text-gray-600 flex items-start">
                                <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                                <span>Preview your email before sending to subscribers. Make sure SMTP is configured in your .env file.</span>
                            </p>
                        </div>
                    </div>
                </form>

                <!-- Preview Section -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-eye text-purple-600 mr-2"></i>
                            Email Preview
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">See how your email will look to recipients</p>
                    </div>
                    <div class="p-6 bg-gray-50">
                        <div id="email-preview" class="bg-white p-8 rounded-lg border-2 border-dashed border-gray-300 shadow-sm min-h-[400px] prose prose-lg max-w-none">
                            <p class="text-gray-400 text-center italic">Your email preview will appear here...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Quick Templates -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                        Quick Templates
                    </h3>
                    <div class="space-y-2">
                        <button type="button" onclick="loadTemplate('welcome')" class="w-full text-left bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 p-3 rounded-lg text-sm transition border border-blue-200">
                            <div class="flex items-center">
                                <i class="fas fa-hand-wave text-blue-600 mr-3 text-lg"></i>
                                <div>
                                    <div class="font-semibold text-gray-900">Welcome Email</div>
                                    <div class="text-xs text-gray-600">Friendly greeting template</div>
                                </div>
                            </div>
                        </button>
                        <button type="button" onclick="loadTemplate('promotion')" class="w-full text-left bg-gradient-to-r from-orange-50 to-orange-100 hover:from-orange-100 hover:to-orange-200 p-3 rounded-lg text-sm transition border border-orange-200">
                            <div class="flex items-center">
                                <i class="fas fa-tag text-orange-600 mr-3 text-lg"></i>
                                <div>
                                    <div class="font-semibold text-gray-900">Promotional Offer</div>
                                    <div class="text-xs text-gray-600">Discount & sale template</div>
                                </div>
                            </div>
                        </button>
                        <button type="button" onclick="loadTemplate('announcement')" class="w-full text-left bg-gradient-to-r from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 p-3 rounded-lg text-sm transition border border-green-200">
                            <div class="flex items-center">
                                <i class="fas fa-bullhorn text-green-600 mr-3 text-lg"></i>
                                <div>
                                    <div class="font-semibold text-gray-900">Announcement</div>
                                    <div class="text-xs text-gray-600">Important news template</div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Variables -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-code text-purple-500 mr-2"></i>
                        Available Variables
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="bg-gray-50 p-2 rounded border">
                            <div class="flex items-center justify-between">
                                <code class="text-xs text-gray-700">@{{ $user->name }}</code>
                                <button onclick="copyToClipboard('@{{ $user->name }}')" class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">Customer name</p>
                        </div>

                        <div class="bg-gray-50 p-2 rounded border">
                            <div class="flex items-center justify-between">
                                <code class="text-xs text-gray-700">@{{ $user->email }}</code>
                                <button onclick="copyToClipboard('@{{ $user->email }}')" class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">Customer email</p>
                        </div>

                        <div class="bg-gray-50 p-2 rounded border">
                            <div class="flex items-center justify-between">
                                <code class="text-xs text-gray-700 break-all">@{{ config('app.name') }}</code>
                                <button onclick="copyToClipboard('@{{ config(\'app.name\') }}')" class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">Site name</p>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                    <h3 class="font-bold text-blue-900 mb-2 text-sm">💡 Pro Tips</h3>
                    <ul class="text-xs text-blue-700 space-y-2">
                        <li>• Keep subject lines under 50 characters</li>
                        <li>• Use clear call-to-action buttons</li>
                        <li>• Test on multiple email clients</li>
                        <li>• Always send test email first</li>
                        <li>• Personalize with customer variables</li>
                    </ul>
                </div>

                <!-- Stats -->
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg shadow p-6 border border-indigo-200">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-indigo-600 mr-2"></i>
                        Email Stats
                    </h3>
                    <div class="space-y-4">
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <p class="text-xs text-gray-600 mb-1">Content Word Count</p>
                            <p id="content-words" class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent">0</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <p class="text-xs text-gray-600 mb-1">Estimated Read Time</p>
                            <p id="read-time" class="text-2xl font-bold text-gray-700">0 min</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@vite('resources/js/email-editor.js')
@endpush
@endsection
