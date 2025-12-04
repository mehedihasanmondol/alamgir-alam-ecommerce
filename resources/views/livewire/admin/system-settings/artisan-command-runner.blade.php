{{-- 
/**
 * ModuleName: Artisan Command Runner (Livewire Component)
 * Purpose: Execute whitelisted artisan commands from admin panel
 * Features: Predefined commands, custom commands, output display, security logging
 * 
 * @category Admin Livewire Views
 * @package  Resources\Views\Livewire\Admin\SystemSettings
 * @created  2025-12-02
 */
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-indigo-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Artisan Command Runner</h2>
                    <p class="text-sm text-gray-600 mt-0.5">Execute Laravel artisan commands safely</p>
                </div>
            </div>
            <div class="hidden sm:flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                    Secured
                </span>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if($successMessage)
        <div class="mx-6 mt-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-green-800">Success</h3>
                <p class="text-sm text-green-700 mt-1">{{ $successMessage }}</p>
            </div>
            <button wire:click="clearOutput" class="ml-3 text-green-600 hover:text-green-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if($errorMessage)
        <div class="mx-6 mt-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start">
            <svg class="w-5 h-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-red-800">Error</h3>
                <p class="text-sm text-red-700 mt-1">{{ $errorMessage }}</p>
            </div>
            <button wire:click="clearOutput" class="ml-3 text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- Command Selection Form -->
    <div class="p-6 space-y-6">
        <!-- Predefined Commands -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-3">
                <svg class="w-4 h-4 inline mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                Select Command
            </label>
            <select 
                wire:model.live="selectedCommand"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
            >
                <option value="">-- Choose a command --</option>
                
                <optgroup label="Cache Commands">
                    <option value="cache:clear">cache:clear - Clear application cache</option>
                    <option value="config:clear">config:clear - Clear configuration cache</option>
                    <option value="route:clear">route:clear - Clear route cache</option>
                    <option value="view:clear">view:clear - Clear compiled views</option>
                    <option value="optimize:clear">optimize:clear - Clear all cached files</option>
                    <option value="optimize">optimize - Cache framework files</option>
                </optgroup>
                
                <optgroup label="Database Commands">
                    <option value="migrate:status">migrate:status - Show migration status</option>
                    <option value="db:show">db:show - Display database info</option>
                </optgroup>
                
                <optgroup label="Queue Commands">
                    <option value="queue:failed">queue:failed - List failed jobs</option>
                    <option value="queue:restart">queue:restart - Restart queue workers</option>
                </optgroup>
                
                <optgroup label="Storage Commands">
                    <option value="storage:link">storage:link - Create storage symlink</option>
                </optgroup>
                
                <optgroup label="Maintenance Commands">
                    <option value="down">down - Enable maintenance mode</option>
                    <option value="up">up - Disable maintenance mode</option>
                </optgroup>
                
                <optgroup label="Other Commands">
                    <option value="about">about - Application information</option>
                    <option value="env">env - Current environment</option>
                    <option value="list">list - List all commands</option>
                    <option value="inspire">inspire - Get inspired! 💡</option>
                </optgroup>
                
                <optgroup label="Advanced">
                    <option value="custom">✨ Custom Command (Advanced)</option>
                </optgroup>
            </select>
        </div>

        <!-- Custom Command Input (shown when 'custom' is selected) -->
        @if($selectedCommand === 'custom')
            <div x-data="{ showWarning: true }" class="space-y-4">
                <!-- Security Warning -->
                <div x-show="showWarning" x-transition class="p-4 bg-yellow-50 border-l-4 border-yellow-400">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-yellow-800">Advanced Feature - Use with Caution</h3>
                            <p class="text-sm text-yellow-700 mt-1">
                                Dangerous commands (migrate:fresh, db:wipe, key:generate, etc.) are blocked. 
                                Only enter commands if you understand their impact.
                            </p>
                        </div>
                        <button @click="showWarning = false" class="ml-3 text-yellow-600 hover:text-yellow-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Custom Artisan Command
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 font-mono text-sm">
                            php artisan
                        </span>
                        <input 
                            type="text" 
                            wire:model="customCommand"
                            placeholder="e.g., cache:clear --tags=views"
                            class="w-full pl-32 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent font-mono text-sm"
                        >
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        💡 Example: <code class="bg-gray-100 px-2 py-1 rounded">migrate:status</code>, 
                        <code class="bg-gray-100 px-2 py-1 rounded">queue:work --once</code>
                    </p>
                </div>
            </div>
        @endif

        <!-- Execute Button -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <div class="text-sm text-gray-600">
                <svg class="w-4 h-4 inline mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                All executions are logged for security
            </div>
            <div class="flex space-x-3">
                @if($commandOutput)
                    <button 
                        wire:click="clearOutput"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center space-x-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Clear Output</span>
                    </button>
                @endif
                
                <button 
                    wire:click="executeCommand"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="px-6 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg flex items-center space-x-2 disabled:opacity-50"
                >
                    <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove>Execute Command</span>
                    <span wire:loading>Executing...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Command Output -->
    @if($commandOutput)
        <div class="p-6 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <label class="text-sm font-semibold text-gray-900 flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Command Output
                </label>
                <button 
                    onclick="navigator.clipboard.writeText(this.closest('.p-6').querySelector('pre').textContent)"
                    class="text-xs px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors flex items-center space-x-1"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span>Copy</span>
                </button>
            </div>
            <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                <pre class="text-sm text-green-400 font-mono whitespace-pre-wrap">{{ $commandOutput }}</pre>
            </div>
        </div>
    @endif

    <!-- Info Footer -->
    <div class="p-4 bg-indigo-50 border-t border-indigo-100 rounded-b-xl">
        <div class="flex items-start space-x-2 text-xs text-indigo-900">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <p class="font-semibold">Security Notice:</p>
                <p class="mt-1">Only whitelisted commands can be executed. Destructive commands (migrate:fresh, db:wipe, etc.) are blocked. All command executions are logged with user info and IP address.</p>
            </div>
        </div>
    </div>
</div>
