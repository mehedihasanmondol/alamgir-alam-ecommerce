<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\SiteSetting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * YouTube Comment Service
 * 
 * Handles importing YouTube comments as project feedback
 * Uses YouTube Data API v3
 * 
 * @category Services
 * @package  App\Services
 * @created  2025-12-14
 */
class YouTubeCommentService
{
    /**
     * HTTP Client instance
     */
    protected Client $client;

    /**
     * YouTube API base URL
     */
    protected string $apiBaseUrl = 'https://www.googleapis.com/youtube/v3';

    /**
     * YouTube API Key
     */
    protected ?string $apiKey = null;

    /**
     * YouTube Channel ID
     */
    protected ?string $channelId = null;

    /**
     * Sentiment Analysis Service
     */
    protected SentimentAnalysisService $sentimentService;

    /**
     * Constructor
     */
    public function __construct(SentimentAnalysisService $sentimentService = null)
    {
        $this->client = new Client();
        $this->sentimentService = $sentimentService ?? new SentimentAnalysisService();
        $this->loadSettings();
    }

    /**
     * Load YouTube settings from site settings
     */
    protected function loadSettings(): void
    {
        $this->apiKey = SiteSetting::get('youtube_api_key');
        $this->channelId = SiteSetting::get('youtube_channel_id');
    }

    /**
     * Test YouTube API connection
     * 
     * @return array
     */
    public function testConnection(): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'YouTube API Key is not configured'
            ];
        }

        if (empty($this->channelId)) {
            return [
                'success' => false,
                'message' => 'YouTube Channel ID is not configured'
            ];
        }

        try {
            $response = $this->client->get("{$this->apiBaseUrl}/channels", [
                'query' => [
                    'part' => 'snippet',
                    'id' => $this->channelId,
                    'key' => $this->apiKey
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['items']) && count($data['items']) > 0) {
                $channelTitle = $data['items'][0]['snippet']['title'] ?? 'Unknown';
                return [
                    'success' => true,
                    'message' => "Successfully connected to channel: {$channelTitle}",
                    'channel' => $data['items'][0]
                ];
            }

            return [
                'success' => false,
                'message' => 'Channel not found with the provided ID'
            ];
        } catch (RequestException $e) {
            $errorMessage = 'API Request failed';

            if ($e->hasResponse()) {
                $errorData = json_decode($e->getResponse()->getBody()->getContents(), true);
                $errorMessage = $errorData['error']['message'] ?? $errorMessage;
            }

            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }
    }

    /**
     * Fetch all video IDs from the channel
     * 
     * @param int $maxResults
     * @param string|null $publishedAfter
     * @param string|null $publishedBefore
     * @return array
     */
    public function getChannelVideoIds(int $maxResults = 50, ?string $publishedAfter = null, ?string $publishedBefore = null): array
    {
        try {
            $queryParams = [
                'part' => 'id',
                'channelId' => $this->channelId,
                'type' => 'video',
                'order' => 'date',
                'maxResults' => $maxResults,
                'key' => $this->apiKey
            ];

            // Add date filters if provided
            if ($publishedAfter) {
                $queryParams['publishedAfter'] = Carbon::parse($publishedAfter)->toIso8601String();
            }
            if ($publishedBefore) {
                $queryParams['publishedBefore'] = Carbon::parse($publishedBefore)->toIso8601String();
            }

            $response = $this->client->get("{$this->apiBaseUrl}/search", [
                'query' => $queryParams
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $videoIds = [];

            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    if (isset($item['id']['videoId'])) {
                        $videoIds[] = $item['id']['videoId'];
                    }
                }
            }

            return $videoIds;
        } catch (RequestException $e) {
            Log::error('YouTube API Error fetching videos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch comments from a specific video
     * 
     * @param string $videoId
     * @param int $maxResults
     * @return array
     */
    public function fetchVideoComments(string $videoId, int $maxResults = 100): array
    {
        try {
            $response = $this->client->get("{$this->apiBaseUrl}/commentThreads", [
                'query' => [
                    'part' => 'snippet',
                    'videoId' => $videoId,
                    'maxResults' => $maxResults,
                    'order' => 'time',
                    'textFormat' => 'plainText',
                    'key' => $this->apiKey
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $comments = [];

            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    $snippet = $item['snippet']['topLevelComment']['snippet'];
                    $comments[] = [
                        'comment_id' => $item['snippet']['topLevelComment']['id'],
                        'video_id' => $videoId,
                        'author_name' => $snippet['authorDisplayName'],
                        'author_channel_url' => $snippet['authorChannelUrl'] ?? null,
                        'comment_text' => $snippet['textDisplay'],
                        'like_count' => $snippet['likeCount'] ?? 0,
                        'published_at' => $snippet['publishedAt'],
                        'updated_at' => $snippet['updatedAt'] ?? $snippet['publishedAt'],
                    ];
                }
            }

            return $comments;
        } catch (RequestException $e) {
            Log::error("YouTube API Error fetching comments for video {$videoId}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get video details
     * 
     * @param string $videoId
     * @return array|null
     */
    public function getVideoDetails(string $videoId): ?array
    {
        try {
            $response = $this->client->get("{$this->apiBaseUrl}/videos", [
                'query' => [
                    'part' => 'snippet',
                    'id' => $videoId,
                    'key' => $this->apiKey
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['items']) && count($data['items']) > 0) {
                $snippet = $data['items'][0]['snippet'];
                return [
                    'video_id' => $videoId,
                    'title' => $snippet['title'],
                    'description' => $snippet['description'],
                    'channel_title' => $snippet['channelTitle'],
                    'published_at' => $snippet['publishedAt'],
                ];
            }

            return null;
        } catch (RequestException $e) {
            Log::error("YouTube API Error fetching video details for {$videoId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Import a YouTube comment as feedback
     * 
     * @param array $commentData
     * @param array $videoData
     * @return Feedback|null
     */
    public function importComment(array $commentData, array $videoData): ?Feedback
    {
        // Check if comment already exists
        $existing = Feedback::where('source_reference_id', $commentData['comment_id'])->first();

        if ($existing) {
            Log::info("Skipping duplicate comment: {$commentData['comment_id']}");
            return null;
        }

        $defaultRating = (int) SiteSetting::get('youtube_default_rating', 5);
        $autoApprove = SiteSetting::get('youtube_auto_approve', '0') === '1';

        try {
            $feedback = Feedback::create([
                'user_id' => null, // YouTube comments are from external users
                'customer_name' => $commentData['author_name'],
                'customer_email' => '', // YouTube API doesn't provide email
                'customer_mobile' => '', // YouTube API doesn't provide phone
                'customer_address' => null,
                'rating' => $defaultRating,
                'title' => 'Comment on: ' . $videoData['title'],
                'feedback' => $commentData['comment_text'],
                'source' => 'youtube',
                'source_reference_id' => $commentData['comment_id'],
                'source_metadata' => [
                    'video_id' => $videoData['video_id'],
                    'video_title' => $videoData['title'],
                    'video_url' => "https://www.youtube.com/watch?v={$videoData['video_id']}",
                    'author_channel_url' => $commentData['author_channel_url'],
                    'like_count' => $commentData['like_count'],
                    'published_at' => $commentData['published_at'],
                ],
                'images' => null,
                'status' => $autoApprove ? 'approved' : 'pending',
                'is_featured' => false,
                'helpful_count' => $commentData['like_count'],
                'not_helpful_count' => 0,
                'approved_at' => $autoApprove ? now() : null,
                'approved_by' => null,
            ]);

            Log::info("Imported YouTube comment: {$commentData['comment_id']}");
            return $feedback;
        } catch (\Exception $e) {
            Log::error("Error importing YouTube comment: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Sync comments from all channel videos
     * 
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array
     */
    public function syncComments(?string $dateFrom = null, ?string $dateTo = null, ?callable $progressCallback = null): array
    {
        $importEnabled = SiteSetting::get('youtube_import_enabled', '0') === '1';

        if (!$importEnabled) {
            return [
                'success' => false,
                'message' => 'YouTube import is not enabled'
            ];
        }

        if (empty($this->apiKey) || empty($this->channelId)) {
            return [
                'success' => false,
                'message' => 'YouTube API credentials not configured'
            ];
        }

        $maxResults = (int) SiteSetting::get('youtube_max_results', 100);
        $videoIds = $this->getChannelVideoIds(50, $dateFrom, $dateTo);
        $totalVideos = count($videoIds);

        $importedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        $filteredCount = 0; // Track sentiment-filtered comments

        // Get sentiment settings
        $sentimentEnabled = SiteSetting::get('youtube_sentiment_enabled', '0') === '1';
        $sentimentMethod = SiteSetting::get('youtube_sentiment_method', 'keyword');
        $sentimentThreshold = (float) SiteSetting::get('youtube_sentiment_threshold', '0.6');
        $importPositiveOnly = SiteSetting::get('youtube_import_positive_only', '0') === '1';

        // Report initial progress
        if ($progressCallback) {
            $progressCallback([
                'status' => 'fetching',
                'progress' => 5,
                'videos_processed' => 0,
                'total_videos' => $totalVideos,
                'comments_imported' => 0,
                'duplicates_skipped' => 0,
                'errors' => 0,
                'comments_filtered' => 0,
                'message' => "Found {$totalVideos} videos. Fetching comments...",
                'completed' => false,
            ]);
        }

        foreach ($videoIds as $index => $videoId) {
            $videoDetails = $this->getVideoDetails($videoId);

            if (!$videoDetails) {
                $errorCount++;
                continue;
            }

            $comments = $this->fetchVideoComments($videoId, $maxResults);

            foreach ($comments as $commentData) {
                // Apply sentiment filtering if enabled
                if ($sentimentEnabled && $importPositiveOnly) {
                    $commentText = $commentData['snippet']['topLevelComment']['snippet']['textDisplay'] ?? '';

                    // Analyze sentiment
                    $sentimentResult = $this->sentimentService->analyze($commentText, $sentimentMethod);

                    // Filter out non-positive comments
                    if ($sentimentResult['score'] < $sentimentThreshold) {
                        $filteredCount++;
                        continue; // Skip this comment
                    }

                    // Add sentiment data to comment metadata
                    $commentData['sentiment'] = $sentimentResult;
                }

                $feedback = $this->importComment($commentData, $videoDetails);

                if ($feedback) {
                    $importedCount++;
                } else {
                    $skippedCount++;
                }

                // Respect API quota - add small delay between requests
                usleep(100000); // 0.1 second delay
            }

            // Report progress after each video
            if ($progressCallback) {
                $videosProcessed = $index + 1;
                $progress = 5 + (($videosProcessed / $totalVideos) * 90); // 5-95%

                $progressCallback([
                    'status' => 'importing',
                    'progress' => round($progress),
                    'videos_processed' => $videosProcessed,
                    'total_videos' => $totalVideos,
                    'comments_imported' => $importedCount,
                    'duplicates_skipped' => $skippedCount,
                    'errors' => $errorCount,
                    'comments_filtered' => $filteredCount,
                    'message' => "Processing video {$videosProcessed} of {$totalVideos}...",
                    'completed' => false,
                ]);
            }
        }

        // Update last import timestamp and count
        SiteSetting::set('youtube_last_import', now()->toDateTimeString());
        SiteSetting::set('youtube_last_import_count', (string) $importedCount);
        SiteSetting::clearCache();

        return [
            'success' => true,
            'message' => "Import completed: {$importedCount} new, {$skippedCount} duplicates, {$filteredCount} filtered, {$errorCount} errors",
            'imported' => $importedCount,
            'skipped' => $skippedCount,
            'filtered' => $filteredCount,
            'errors' => $errorCount,
        ];
    }
}
