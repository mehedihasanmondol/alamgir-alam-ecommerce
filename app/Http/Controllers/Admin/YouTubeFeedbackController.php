<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\SiteSetting;
use App\Services\YouTubeCommentService;
use Illuminate\Http\Request;

/**
 * YouTube Feedback Controller
 * 
 * Handles YouTube comment import management
 * 
 * @category Controllers
 * @package  App\Http\Controllers\Admin
 * @created  2025-12-14
 */
class YouTubeFeedbackController extends Controller
{
    /**
     * YouTube Service instance
     */
    protected YouTubeCommentService $youtubeService;

    /**
     * Constructor
     */
    public function __construct(YouTubeCommentService $youtubeService)
    {
        $this->youtubeService = $youtubeService;
    }

    /**
     * Show YouTube management page
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
        ];

        // Get statistics
        $stats = [
            'total' => Feedback::fromYoutube()->count(),
            'pending' => Feedback::fromYoutube()->pending()->count(),
            'approved' => Feedback::fromYoutube()->approved()->count(),
            'rejected' => Feedback::fromYoutube()->rejected()->count(),
        ];

        return view('admin.feedback.youtube-management', compact('settings', 'stats'));
    }

    /**
     * Update YouTube settings
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'youtube_api_key' => 'nullable|string',
            'youtube_channel_id' => 'nullable|string',
            'youtube_import_enabled' => 'nullable|boolean',
            'youtube_auto_approve' => 'nullable|boolean',
            'youtube_default_rating' => 'nullable|integer|min:1|max:5',
            'youtube_max_results' => 'nullable|integer|min:1|max:500',

            // Sentiment Analysis Settings
            'youtube_sentiment_enabled' => 'nullable|boolean',
            'youtube_sentiment_method' => 'nullable|in:keyword,ml',
            'youtube_sentiment_threshold' => 'nullable|numeric|min:0|max:1',
            'youtube_import_positive_only' => 'nullable|boolean',
            'google_natural_language_api_key' => 'nullable|string',

            // Custom Keywords
            'sentiment_custom_positive_bangla' => 'nullable|string',
            'sentiment_custom_positive_english' => 'nullable|string',
            'sentiment_custom_negative_bangla' => 'nullable|string',
            'sentiment_custom_negative_english' => 'nullable|string',
        ]);

        // Handle text fields
        if (isset($validated['youtube_api_key'])) {
            SiteSetting::set('youtube_api_key', $validated['youtube_api_key']);
        }
        if (isset($validated['youtube_channel_id'])) {
            SiteSetting::set('youtube_channel_id', $validated['youtube_channel_id']);
        }
        if (isset($validated['youtube_default_rating'])) {
            SiteSetting::set('youtube_default_rating', (string) $validated['youtube_default_rating']);
        }
        if (isset($validated['youtube_max_results'])) {
            SiteSetting::set('youtube_max_results', (string) $validated['youtube_max_results']);
        }
        if (isset($validated['youtube_sentiment_method'])) {
            SiteSetting::set('youtube_sentiment_method', $validated['youtube_sentiment_method']);
        }
        if (isset($validated['youtube_sentiment_threshold'])) {
            SiteSetting::set('youtube_sentiment_threshold', (string) $validated['youtube_sentiment_threshold']);
        }
        if (isset($validated['google_natural_language_api_key'])) {
            SiteSetting::set('google_natural_language_api_key', $validated['google_natural_language_api_key']);
        }

        // Handle custom keywords
        if (isset($validated['sentiment_custom_positive_bangla'])) {
            SiteSetting::set('sentiment_custom_positive_bangla', $validated['sentiment_custom_positive_bangla']);
        }
        if (isset($validated['sentiment_custom_positive_english'])) {
            SiteSetting::set('sentiment_custom_positive_english', $validated['sentiment_custom_positive_english']);
        }
        if (isset($validated['sentiment_custom_negative_bangla'])) {
            SiteSetting::set('sentiment_custom_negative_bangla', $validated['sentiment_custom_negative_bangla']);
        }
        if (isset($validated['sentiment_custom_negative_english'])) {
            SiteSetting::set('sentiment_custom_negative_english', $validated['sentiment_custom_negative_english']);
        }

        // Handle checkboxes - they need special handling because unchecked = not sent
        SiteSetting::set('youtube_import_enabled', $request->has('youtube_import_enabled') ? '1' : '0');
        SiteSetting::set('youtube_auto_approve', $request->has('youtube_auto_approve') ? '1' : '0');
        SiteSetting::set('youtube_sentiment_enabled', $request->has('youtube_sentiment_enabled') ? '1' : '0');
        SiteSetting::set('youtube_import_positive_only', $request->has('youtube_import_positive_only') ? '1' : '0');

        SiteSetting::clearCache();

        return redirect()->back()->with('success', 'YouTube settings updated successfully!');
    }

    /**
     * Test YouTube API connection
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection(Request $request)
    {
        $result = $this->youtubeService->testConnection();

        return response()->json($result);
    }

    /**
     * Test Google Natural Language API connection
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testMLConnection(Request $request)
    {
        $apiKey = $request->input('api_key');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required'
            ]);
        }

        try {
            // Test with a sample text
            $testText = "This is a wonderful and amazing test!";

            $response = \Http::post("https://language.googleapis.com/v1/documents:analyzeSentiment?key={$apiKey}", [
                'document' => [
                    'type' => 'PLAIN_TEXT',
                    'content' => $testText,
                ],
                'encodingType' => 'UTF8',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $score = $data['documentSentiment']['score'] ?? 0;
                $magnitude = $data['documentSentiment']['magnitude'] ?? 0;

                return response()->json([
                    'success' => true,
                    'message' => "API is working! Test result:\n\nSentiment Score: {$score}\nMagnitude: {$magnitude}\n\nYour API key is valid and the Natural Language API is accessible."
                ]);
            } else {
                $error = $response->json();
                return response()->json([
                    'success' => false,
                    'message' => 'API Error: ' . ($error['error']['message'] ?? 'Unknown error')
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Trigger manual YouTube comment import
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        try {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');

            // Generate unique progress key
            $progressKey = 'youtube_import_' . auth()->id() . '_' . time();

            // Initialize progress
            cache()->put($progressKey, [
                'status' => 'starting',
                'progress' => 0,
                'videos_processed' => 0,
                'total_videos' => 0,
                'comments_imported' => 0,
                'duplicates_skipped' => 0,
                'errors' => 0,
                'message' => 'Initializing import...',
                'completed' => false,
            ], now()->addMinutes(30));

            // Start import with progress callback
            $result = $this->youtubeService->syncComments($dateFrom, $dateTo, function ($progress) use ($progressKey) {
                cache()->put($progressKey, $progress, now()->addMinutes(30));
            });

            // Mark as completed
            cache()->put($progressKey, array_merge(cache()->get($progressKey, []), [
                'status' => 'completed',
                'progress' => 100,
                'completed' => true,
                'message' => 'Import completed successfully!',
            ]), now()->addMinutes(30));

            return response()->json(array_merge($result, [
                'progress_key' => $progressKey,
            ]));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get import progress status
     */
    public function importProgress(Request $request)
    {
        $progressKey = $request->input('progress_key');

        if (!$progressKey) {
            return response()->json([
                'success' => false,
                'message' => 'Progress key required'
            ], 400);
        }

        $progress = cache()->get($progressKey, [
            'status' => 'unknown',
            'progress' => 0,
            'message' => 'No progress data',
            'completed' => true,
        ]);

        return response()->json([
            'success' => true,
            'progress' => $progress
        ]);
    }

    /**
     * Get import history/statistics
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function importHistory()
    {
        $lastImport = SiteSetting::get('youtube_last_import');
        $lastCount = SiteSetting::get('youtube_last_import_count', 0);

        return response()->json([
            'success' => true,
            'last_import' => $lastImport,
            'last_count' => $lastCount,
        ]);
    }
}
