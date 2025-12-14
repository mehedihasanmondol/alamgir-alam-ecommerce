<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SiteSetting;

class YouTubeSetupController extends Controller
{
    /**
     * Show YouTube setup and configuration guide
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get settings
        $settings = [
            'youtube_api_key' => SiteSetting::get('youtube_api_key'),
            'youtube_channel_id' => SiteSetting::get('youtube_channel_id'),
            'youtube_import_enabled' => SiteSetting::get('youtube_import_enabled', '0') === '1',
            'youtube_auto_approve' => SiteSetting::get('youtube_auto_approve', '0') === '1',
            'youtube_default_rating' => SiteSetting::get('youtube_default_rating', '5'),
            'youtube_max_results' => SiteSetting::get('youtube_max_results', '100'),
            'youtube_last_import' => SiteSetting::get('youtube_last_import'),
            'youtube_last_import_count' => SiteSetting::get('youtube_last_import_count', '0'),

            // Sentiment Analysis Settings
            'youtube_sentiment_enabled' => SiteSetting::get('youtube_sentiment_enabled', '0') === '1',
            'youtube_sentiment_method' => SiteSetting::get('youtube_sentiment_method', 'keyword'),
            'youtube_sentiment_threshold' => SiteSetting::get('youtube_sentiment_threshold', '0.6'),
            'youtube_import_positive_only' => SiteSetting::get('youtube_import_positive_only', '0') === '1',
            'google_natural_language_api_key' => SiteSetting::get('google_natural_language_api_key', ''),

            // Custom Keywords
            'sentiment_custom_positive_bangla' => SiteSetting::get('sentiment_custom_positive_bangla', ''),
            'sentiment_custom_positive_english' => SiteSetting::get('sentiment_custom_positive_english', ''),
            'sentiment_custom_negative_bangla' => SiteSetting::get('sentiment_custom_negative_bangla', ''),
            'sentiment_custom_negative_english' => SiteSetting::get('sentiment_custom_negative_english', ''),
        ];

        return view('admin.feedback.youtube-setup', compact('settings'));
    }
}
