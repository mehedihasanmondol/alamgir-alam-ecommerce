<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'We\'ll be back soon!' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Maintenance Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Image Section -->
            @if($image)
                <div class="w-full h-64 overflow-hidden bg-gray-100">
                    <img src="{{ asset('storage/' . $image) }}" alt="Maintenance" class="w-full h-full object-cover">
                </div>
            @else
                <div class="w-full h-64 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                    <svg class="w-32 h-32 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            @endif

            <!-- Content Section -->
            <div class="p-8 md:p-12">
                <!-- Warning Icon -->
                <div class="flex justify-center mb-6">
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 text-center mb-4">
                    {{ $title ?? 'We\'ll be back soon!' }}
                </h1>

                <!-- Message -->
                <p class="text-lg text-gray-600 text-center mb-8 leading-relaxed">
                    {{ $message ?? 'Sorry for the inconvenience. We\'re performing some maintenance at the moment. We\'ll be back online shortly!' }}
                </p>

                <!-- Animated Loading Dots -->
                <div class="flex justify-center space-x-2 mb-8">
                    <div class="w-3 h-3 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                    <div class="w-3 h-3 bg-purple-600 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                    <div class="w-3 h-3 bg-pink-600 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-blue-900 mb-1">What's happening?</h3>
                            <p class="text-blue-700 text-sm">
                                We're currently performing scheduled maintenance to improve our services. 
                                This should only take a few moments. Thank you for your patience!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Info (Optional) -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        Need urgent assistance? 
                        <a href="mailto:support@example.com" class="text-indigo-600 hover:text-indigo-700 font-medium">
                            Contact Support
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-gray-600 text-sm">
                &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Auto-refresh Script (check every 30 seconds) -->
    <script>
        setTimeout(function() {
            location.reload();
        }, 30000); // Refresh every 30 seconds to check if maintenance is over
    </script>
</body>
</html>
