@extends('layouts.admin')

@section('title', 'YouTube Setup & Configuration')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-5xl">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-7 h-7 mr-2 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                    </svg>
                    YouTube Setup & Configuration
                </h1>
                <p class="text-gray-600 text-sm mt-1">Configure YouTube comment imports with sentiment analysis</p>
            </div>
            <a href="{{ route('admin.feedback.youtube.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">
                ← Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Settings Form --}}
        <div class="col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 mr-2">⚙️</span>
                Settings & Configuration
            </h2>

            <form action="{{ route('admin.feedback.youtube.update-settings') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Basic Settings --}}
                <div class="space-y-3 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">YouTube API Key</label>
                            <input type="password" name="youtube_api_key" value="{{ $settings['youtube_api_key'] ?? '' }}"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Your YouTube Data API v3 Key">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">YouTube Channel ID</label>
                            <input type="text" name="youtube_channel_id" value="{{ $settings['youtube_channel_id'] ?? '' }}"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Your YouTube Channel ID">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="flex items-center p-2 bg-gray-50 rounded-lg">
                            <input type="checkbox" name="youtube_import_enabled" value="1" id="import_enabled"
                                {{ ($settings['youtube_import_enabled'] ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 rounded">
                            <label for="import_enabled" class="ml-2 text-xs font-medium text-gray-700">Enable Import</label>
                        </div>

                        <div class="flex items-center p-2 bg-gray-50 rounded-lg">
                            <input type="checkbox" name="youtube_auto_approve" value="1" id="auto_approve"
                                {{ ($settings['youtube_auto_approve'] ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-600 rounded">
                            <label for="auto_approve" class="ml-2 text-xs font-medium text-gray-700">Auto-Approve</label>
                        </div>

                        <div>
                            <select name="youtube_default_rating" class="w-full px-2 py-2 text-xs border border-gray-300 rounded-lg">
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ ($settings['youtube_default_rating'] ?? 5) == $i ? 'selected' : '' }}>
                                    {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                    </option>
                                    @endfor
                            </select>
                        </div>

                        <div>
                            <input type="number" name="youtube_max_results" value="{{ $settings['youtube_max_results'] ?? 100 }}"
                                min="1" max="500" placeholder="Max: 100"
                                class="w-full px-2 py-2 text-xs border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                {{-- Sentiment Analysis Collapsible --}}
                <div class="border border-purple-200 rounded-lg mb-4">
                    <button type="button" onclick="toggleSection('sentiment')" class="w-full flex items-center justify-between p-3 bg-purple-50 hover:bg-purple-100 transition rounded-t-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-semibold text-purple-900">Sentiment Analysis (Bangla & English)</span>
                        </div>
                        <svg id="sentiment-icon" class="w-5 h-5 text-purple-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="sentiment-section" class="p-4 space-y-3 hidden">
                        <div class="flex items-center p-2 bg-gray-50 rounded-lg">
                            <input type="checkbox" name="youtube_sentiment_enabled" value="1" id="sentiment_enabled"
                                {{ ($settings['youtube_sentiment_enabled'] ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 text-purple-600 rounded"
                                onchange="toggleSentimentFields(this.checked)">
                            <label for="sentiment_enabled" class="ml-2 text-xs font-medium text-gray-700">Enable Sentiment Filtering</label>
                        </div>

                        <div id="sentimentFields" class="{{ ($settings['youtube_sentiment_enabled'] ?? false) ? '' : 'hidden' }} space-y-3">
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="youtube_sentiment_method" value="keyword"
                                        {{ ($settings['youtube_sentiment_method'] ?? 'keyword') === 'keyword' ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600" onchange="toggleMLFields(false)">
                                    <span class="ml-2 text-xs font-medium">Keyword-Based</span>
                                </label>
                                <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="youtube_sentiment_method" value="ml"
                                        {{ ($settings['youtube_sentiment_method'] ?? 'keyword') === 'ml' ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600" onchange="toggleMLFields(true)">
                                    <span class="ml-2 text-xs font-medium">ML-Based</span>
                                </label>
                            </div>

                            <div id="mlApiKeyField" class="{{ ($settings['youtube_sentiment_method'] ?? 'keyword') === 'ml' ? '' : 'hidden' }}">
                                <input type="password" name="google_natural_language_api_key"
                                    value="{{ $settings['google_natural_language_api_key'] ?? '' }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg"
                                    placeholder="Google Natural Language API Key">
                            </div>

                            <div>
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>Threshold</span>
                                    <span id="thresholdValue" class="font-medium">{{ ($settings['youtube_sentiment_threshold'] ?? 0.6) * 100 }}%</span>
                                </div>
                                <input type="range" name="youtube_sentiment_threshold"
                                    value="{{ $settings['youtube_sentiment_threshold'] ?? 0.6 }}"
                                    min="0" max="1" step="0.05"
                                    class="w-full h-2 bg-gray-200 rounded-lg cursor-pointer"
                                    oninput="document.getElementById('thresholdValue').textContent = Math.round(this.value * 100) + '%'">
                            </div>

                            <div class="flex items-center p-2 bg-green-50 rounded-lg border border-green-200">
                                <input type="checkbox" name="youtube_import_positive_only" value="1" id="positive_only"
                                    {{ ($settings['youtube_import_positive_only'] ?? false) ? 'checked' : '' }}
                                    class="w-4 h-4 text-green-600 rounded">
                                <label for="positive_only" class="ml-2 text-xs font-medium text-green-900">Import Positive Only</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Custom Keywords Collapsible --}}
                <div class="border border-blue-200 rounded-lg mb-4">
                    <button type="button" onclick="toggleSection('keywords')" class="w-full flex items-center justify-between p-3 bg-blue-50 hover:bg-blue-100 transition rounded-t-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="text-sm font-semibold text-blue-900">Custom Keywords</span>
                        </div>
                        <svg id="keywords-icon" class="w-5 h-5 text-blue-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="keywords-section" class="p-4 space-y-3 hidden">
                        {{-- View Default Keywords Button --}}
                        <button type="button" onclick="toggleDefaultKeywords()" class="w-full px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition">
                            📚 View Default Keywords
                        </button>

                        <div id="defaultKeywords" class="hidden p-3 bg-gray-50 rounded-lg text-xs space-y-2">
                            <div class="border-b pb-2">
                                <p class="font-semibold text-green-700">✅ Bangla Positive (60+):</p>
                                <p class="text-gray-600 mt-1">ভালো, সুন্দর, চমৎকার, অসাধারণ, দারুণ, মজার, পছন্দ, ভালোবাসি, প্রিয়, উপকারী, সহায়ক, ধন্যবাদ, শুভেচ্ছা, অভিনন্দন, সেরা, মনোমুগ্ধকর, অপূর্ব, প্রশংসনীয়, উত্তম, মহান, কৃতজ্ঞ, আনন্দিত, খুশি, সন্তুষ্ট, গর্বিত, আশাবাদী, উৎসাহী, উৎসাহিত, অনুপ্রাণিত, প্রেরণাদায়ক, উন্নত, উৎকৃষ্ট, শ্রেষ্ঠ, প্রথম শ্রেণী, মানসম্পন্ন, দক্ষ, নিখুঁত, পরিপূর্ণ, সম্পূর্ণ, যথার্থ, নির্ভুল, আনন্দ, সুখ, শান্তি, ভালোবাসা, স্নেহ, মমতা, হাসি, হাস্যকর, মজা, আমোদ, বিনোদন, সফল, সফলতা, জয়, বিজয়, অর্জন, সাফল্য, উন্নতি, প্রগতি, বৃদ্ধি, উত্থান, সুপারিশ, প্রস্তাবিত, গ্রহণযোগ্য, যোগ্য, উপযুক্ত</p>
                            </div>
                            <div class="border-b pb-2">
                                <p class="font-semibold text-green-700">✅ English Positive (100+):</p>
                                <p class="text-gray-600 mt-1">good, great, excellent, amazing, awesome, wonderful, fantastic, love, like, best, helpful, useful, thank, thanks, appreciate, perfect, nice, beautiful, brilliant, outstanding, superb, fabulous, incredible, magnificent, marvelous, splendid, terrific, impressive, superior, exceptional, remarkable, extraordinary, phenomenal, stellar, premium, top, finest, supreme, ultimate, ideal, flawless, impeccable, pristine, exquisite, elegant, grateful, thankful, blessed, fortunate, lucky, pleased, delighted, thrilled, excited, happy, joyful, cheerful, recommend, recommended, must, definitely, absolutely, highly, strongly, worth, valuable, worthwhile, success, successful, win, winner, victory, achievement, accomplish, triumph, excel, exceed, surpass, satisfied, content, glad, enjoy, enjoyable, pleasant, delightful, charming, lovely, innovative, creative, unique, original, fresh, new, modern, advanced, cutting-edge, state-of-the-art, reliable, trustworthy, dependable, consistent, stable, solid, strong, robust, durable, lasting</p>
                            </div>
                            <div class="border-b pb-2">
                                <p class="font-semibold text-red-700">❌ Bangla Negative (30+):</p>
                                <p class="text-gray-600 mt-1">খারাপ, বাজে, নিকৃষ্ট, ভয়ানক, অপছন্দ, ঘৃণা, বিরক্তিকর, দুর্বল, অসন্তুষ্ট, হতাশ, রাগ, বিরক্ত, অপমান, দুঃখিত, নিম্নমানের, অকেজো, বেকার, অপ্রয়োজনীয়, ক্ষতিকর, বিপজ্জনক, ভুল, ত্রুটি, সমস্যা, কষ্ট, যন্ত্রণা, পীড়া</p>
                            </div>
                            <div>
                                <p class="font-semibold text-red-700">❌ English Negative (40+):</p>
                                <p class="text-gray-600 mt-1">bad, worst, terrible, horrible, awful, poor, hate, dislike, disappointing, disappointed, useless, waste, boring, annoying, angry, sad, upset, frustrated, pathetic, disgusting, fail, failure, broken, wrong, error, problem, issue, weak, inferior, subpar, mediocre, inadequate, insufficient</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-1 gap-3">
                            <div class="border border-green-200 rounded-lg p-3 bg-green-50">
                                <p class="text-xs font-semibold text-green-900 mb-2">✅ Positive Keywords</p>
                                <textarea name="sentiment_custom_positive_bangla" rows="2"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded mb-2"
                                    placeholder="Bangla: চমৎকার, দারুণ">{{ $settings['sentiment_custom_positive_bangla'] ?? '' }}</textarea>
                                <textarea name="sentiment_custom_positive_english" rows="2"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded"
                                    placeholder="English: fantastic, superb">{{ $settings['sentiment_custom_positive_english'] ?? '' }}</textarea>
                            </div>

                            <div class="border border-red-200 rounded-lg p-3 bg-red-50">
                                <p class="text-xs font-semibold text-red-900 mb-2">❌ Negative Keywords</p>
                                <textarea name="sentiment_custom_negative_bangla" rows="2"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded mb-2"
                                    placeholder="Bangla: খারাপ, বাজে">{{ $settings['sentiment_custom_negative_bangla'] ?? '' }}</textarea>
                                <textarea name="sentiment_custom_negative_english" rows="2"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded"
                                    placeholder="English: terrible, awful">{{ $settings['sentiment_custom_negative_english'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    💾 Save Settings
                </button>
            </form>
        </div>

        {{-- Setup Guides Collapsible --}}
        <div class="space-y-2">
            {{-- YouTube API Guide --}}
            <div class="border border-gray-200 rounded-lg">
                <button type="button" onclick="toggleSection('youtube-api')" class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition rounded-t-lg">
                    <span class="text-sm font-semibold text-gray-900">🔑 How to Get YouTube API Key</span>
                    <svg id="youtube-api-icon" class="w-5 h-5 text-gray-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="youtube-api-section" class="p-4 text-sm text-gray-700 hidden">
                    <ol class="list-decimal list-inside space-y-2">
                        <li>Go to <a href="https://console.cloud.google.com/" target="_blank" class="text-blue-600 hover:underline">Google Cloud Console</a></li>
                        <li>Create a new project or select existing</li>
                        <li>Enable <strong>YouTube Data API v3</strong></li>
                        <li>Go to <strong>Credentials</strong> → <strong>Create Credentials</strong> → <strong>API Key</strong></li>
                        <li>Copy and paste the API key above</li>
                    </ol>
                </div>
            </div>

            {{-- ML API Guide --}}
            <div class="border border-gray-200 rounded-lg">
                <button type="button" onclick="toggleSection('ml-api')" class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition rounded-t-lg">
                    <span class="text-sm font-semibold text-gray-900">🤖 How to Get Google Natural Language API Key</span>
                    <svg id="ml-api-icon" class="w-5 h-5 text-gray-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="ml-api-section" class="p-4 text-sm text-gray-700 hidden">
                    <ol class="list-decimal list-inside space-y-2">
                        <li>Go to <a href="https://console.cloud.google.com/" target="_blank" class="text-blue-600 hover:underline">Google Cloud Console</a></li>
                        <li>Create a new project or select existing (can use same as YouTube)</li>
                        <li>Enable <strong>Cloud Natural Language API</strong></li>
                        <li>Go to <strong>Credentials</strong> → <strong>Create Credentials</strong> → <strong>API Key</strong></li>
                        <li>Copy and paste the API key in sentiment settings</li>
                    </ol>
                    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs text-blue-800">
                            <strong>💡 Tip:</strong> ML-based analysis is more accurate but requires API costs. Keyword-based is free and works offline.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Channel ID Guide --}}
            <div class="border border-gray-200 rounded-lg">
                <button type="button" onclick="toggleSection('channel-id')" class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition rounded-t-lg">
                    <span class="text-sm font-semibold text-gray-900">📺 How to Get YouTube Channel ID</span>
                    <svg id="channel-id-icon" class="w-5 h-5 text-gray-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="channel-id-section" class="p-4 text-sm text-gray-700 hidden">
                    <ol class="list-decimal list-inside space-y-2">
                        <li>Go to <a href="https://www.youtube.com/" target="_blank" class="text-blue-600 hover:underline">YouTube</a></li>
                        <li>Click your profile → <strong>Your Channel</strong></li>
                        <li>Copy Channel ID from URL (starts with "UC")</li>
                        <li>Paste it in the settings above</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function toggleSection(section) {
        const content = document.getElementById(section + '-section');
        const icon = document.getElementById(section + '-icon');

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }

    function toggleDefaultKeywords() {
        const keywords = document.getElementById('defaultKeywords');
        keywords.classList.toggle('hidden');
    }

    function toggleSentimentFields(enabled) {
        const fields = document.getElementById('sentimentFields');
        if (enabled) {
            fields.classList.remove('hidden');
        } else {
            fields.classList.add('hidden');
        }
    }

    function toggleMLFields(showML) {
        const mlField = document.getElementById('mlApiKeyField');
        if (showML) {
            mlField.classList.remove('hidden');
        } else {
            mlField.classList.add('hidden');
        }
    }
</script>
@endsection