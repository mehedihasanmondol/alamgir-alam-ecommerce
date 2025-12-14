<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SiteSetting;

class SentimentAnalysisService
{
    /**
     * Bangla positive keywords (comprehensive list)
     */
    private array $banglaPositiveKeywords = [
        // Common positive words
        'ভালো',
        'সুন্দর',
        'চমৎকার',
        'অসাধারণ',
        'দারুণ',
        'মজার',
        'পছন্দ',
        'ভালোবাসি',
        'প্রিয়',
        'উপকারী',
        'সহায়ক',
        'ধন্যবাদ',
        'শুভেচ্ছা',
        'অভিনন্দন',
        'সেরা',
        'চমৎকার',
        'মনোমুগ্ধকর',
        'অপূর্ব',
        'প্রশংসনীয়',
        'উত্তম',
        'মহান',

        // Appreciation & gratitude
        'কৃতজ্ঞ',
        'আনন্দিত',
        'খুশি',
        'সন্তুষ্ট',
        'গর্বিত',
        'আশাবাদী',
        'উৎসাহী',
        'উৎসাহিত',
        'অনুপ্রাণিত',
        'প্রেরণাদায়ক',

        // Quality descriptors
        'উন্নত',
        'উৎকৃষ্ট',
        'শ্রেষ্ঠ',
        'প্রথম শ্রেণী',
        'মানসম্পন্ন',
        'দক্ষ',
        'নিখুঁত',
        'পরিপূর্ণ',
        'সম্পূর্ণ',
        'যথার্থ',
        'নির্ভুল',

        // Emotional positive
        'আনন্দ',
        'সুখ',
        'শান্তি',
        'ভালোবাসা',
        'স্নেহ',
        'মমতা',
        'হাসি',
        'হাস্যকর',
        'মজা',
        'আমোদ',
        'বিনোদন',

        // Success & achievement
        'সফল',
        'সফলতা',
        'জয়',
        'বিজয়',
        'অর্জন',
        'সাফল্য',
        'উন্নতি',
        'প্রগতি',
        'বৃদ্ধি',
        'উত্থান',

        // Recommendation
        'সুপারিশ',
        'প্রস্তাবিত',
        'গ্রহণযোগ্য',
        'যোগ্য',
        'উপযুক্ত',

        // Emojis
        '❤️',
        '😍',
        '👍',
        '🙏',
        '👏',
        '💯',
        '✨',
        '⭐',
        '🌟',
        '💖',
        '😊',
        '😄',
        '😃',
        '🎉',
        '🎊',
        '👌',
        '💪',
        '🔥',
        '🏆',
        '🥇',
    ];

    /**
     * Bangla negative keywords
     */
    private array $banglaNegativeKeywords = [
        'খারাপ',
        'বাজে',
        'নিকৃষ্ট',
        'ভয়ানক',
        'অপছন্দ',
        'ঘৃণা',
        'বিরক্তিকর',
        'দুর্বল',
        'অসন্তুষ্ট',
        'হতাশ',
        'রাগ',
        'বিরক্ত',
        'অপমান',
        'দুঃখিত',
        'নিম্নমানের',
        'অকেজো',
        'বেকার',
        'অপ্রয়োজনীয়',
        'ক্ষতিকর',
        'বিপজ্জনক',
        'ভুল',
        'ত্রুটি',
        'সমস্যা',
        'কষ্ট',
        'যন্ত্রণা',
        'পীড়া',
        '👎',
        '😡',
        '😠',
        '💔',
        '😢',
        '😞',
        '😭',
        '😤',
        '🤬',
        '😰',
    ];

    /**
     * English positive keywords (comprehensive list)
     */
    private array $englishPositiveKeywords = [
        // Common positive
        'good',
        'great',
        'excellent',
        'amazing',
        'awesome',
        'wonderful',
        'fantastic',
        'love',
        'like',
        'best',
        'helpful',
        'useful',
        'thank',
        'thanks',
        'appreciate',
        'perfect',
        'nice',
        'beautiful',
        'brilliant',
        'outstanding',
        'superb',
        'fabulous',
        'incredible',
        'magnificent',
        'marvelous',
        'splendid',
        'terrific',
        'impressive',

        // Quality & excellence
        'superior',
        'exceptional',
        'remarkable',
        'extraordinary',
        'phenomenal',
        'stellar',
        'premium',
        'top',
        'finest',
        'supreme',
        'ultimate',
        'ideal',
        'flawless',
        'impeccable',
        'pristine',
        'exquisite',
        'elegant',

        // Appreciation
        'grateful',
        'thankful',
        'blessed',
        'fortunate',
        'lucky',
        'pleased',
        'delighted',
        'thrilled',
        'excited',
        'happy',
        'joyful',
        'cheerful',

        // Recommendation
        'recommend',
        'recommended',
        'must',
        'definitely',
        'absolutely',
        'highly',
        'strongly',
        'worth',
        'valuable',
        'worthwhile',

        // Success & achievement
        'success',
        'successful',
        'win',
        'winner',
        'victory',
        'achievement',
        'accomplish',
        'triumph',
        'excel',
        'exceed',
        'surpass',

        // Satisfaction
        'satisfied',
        'content',
        'pleased',
        'happy',
        'glad',
        'enjoy',
        'enjoyable',
        'pleasant',
        'delightful',
        'charming',
        'lovely',

        // Innovation & creativity
        'innovative',
        'creative',
        'unique',
        'original',
        'fresh',
        'new',
        'modern',
        'advanced',
        'cutting-edge',
        'state-of-the-art',

        // Reliability
        'reliable',
        'trustworthy',
        'dependable',
        'consistent',
        'stable',
        'solid',
        'strong',
        'robust',
        'durable',
        'lasting',

        // Emojis
        '❤️',
        '😍',
        '👍',
        '🙏',
        '👏',
        '💯',
        '✨',
        '⭐',
        '🌟',
        '💖',
        '😊',
        '😄',
        '😃',
        '🎉',
        '🎊',
        '👌',
        '💪',
        '🔥',
        '🏆',
        '🥇',
    ];

    /**
     * English negative keywords
     */
    private array $englishNegativeKeywords = [
        'bad',
        'worst',
        'terrible',
        'horrible',
        'awful',
        'poor',
        'hate',
        'dislike',
        'disappointing',
        'disappointed',
        'useless',
        'waste',
        'boring',
        'annoying',
        'angry',
        'sad',
        'upset',
        'frustrated',
        'pathetic',
        'disgusting',
        'fail',
        'failure',
        'broken',
        'wrong',
        'error',
        'problem',
        'issue',
        'weak',
        'inferior',
        'subpar',
        'mediocre',
        'inadequate',
        'insufficient',
        '👎',
        '😡',
        '😠',
        '💔',
        '😢',
        '😞',
        '😭',
        '😤',
        '🤬',
        '😰',
    ];

    /**
     * Get custom positive keywords from database
     */
    private function getCustomPositiveKeywords(string $language): array
    {
        $key = $language === 'bn' ? 'sentiment_custom_positive_bangla' : 'sentiment_custom_positive_english';
        $customKeywords = SiteSetting::get($key, '');

        if (empty($customKeywords)) {
            return [];
        }

        // Split by comma and trim
        return array_map('trim', explode(',', $customKeywords));
    }

    /**
     * Get custom negative keywords from database
     */
    private function getCustomNegativeKeywords(string $language): array
    {
        $key = $language === 'bn' ? 'sentiment_custom_negative_bangla' : 'sentiment_custom_negative_english';
        $customKeywords = SiteSetting::get($key, '');

        if (empty($customKeywords)) {
            return [];
        }

        // Split by comma and trim
        return array_map('trim', explode(',', $customKeywords));
    }

    /**
     * Get all positive keywords (default + custom)
     */
    private function getAllPositiveKeywords(string $language): array
    {
        $defaultKeywords = $language === 'bn' ? $this->banglaPositiveKeywords : $this->englishPositiveKeywords;
        $customKeywords = $this->getCustomPositiveKeywords($language);

        return array_merge($defaultKeywords, $customKeywords);
    }

    /**
     * Get all negative keywords (default + custom)
     */
    private function getAllNegativeKeywords(string $language): array
    {
        $defaultKeywords = $language === 'bn' ? $this->banglaNegativeKeywords : $this->englishNegativeKeywords;
        $customKeywords = $this->getCustomNegativeKeywords($language);

        return array_merge($defaultKeywords, $customKeywords);
    }

    /**
     * Analyze sentiment using keyword-based method
     */
    public function analyzeKeywordBased(string $text): array
    {
        $text = mb_strtolower($text);
        $language = $this->detectLanguage($text);

        // Use merged keywords (default + custom)
        $positiveKeywords = $this->getAllPositiveKeywords($language);
        $negativeKeywords = $this->getAllNegativeKeywords($language);

        $positiveCount = 0;
        $negativeCount = 0;

        // Count positive keywords
        foreach ($positiveKeywords as $keyword) {
            if (mb_strpos($text, mb_strtolower($keyword)) !== false) {
                $positiveCount++;
            }
        }

        // Count negative keywords
        foreach ($negativeKeywords as $keyword) {
            if (mb_strpos($text, mb_strtolower($keyword)) !== false) {
                $negativeCount++;
            }
        }

        // Calculate sentiment score (0 to 1)
        $totalKeywords = $positiveCount + $negativeCount;
        if ($totalKeywords === 0) {
            $score = 0.5; // Neutral if no keywords found
            $label = 'neutral';
        } else {
            $score = $positiveCount / $totalKeywords;
            if ($score >= 0.6) {
                $label = 'positive';
            } elseif ($score <= 0.4) {
                $label = 'negative';
            } else {
                $label = 'neutral';
            }
        }

        return [
            'score' => round($score, 2),
            'label' => $label,
            'language' => $language,
            'method' => 'keyword',
            'positive_keywords' => $positiveCount,
            'negative_keywords' => $negativeCount,
        ];
    }

    /**
     * Analyze sentiment using ML-based API (Google Cloud Natural Language)
     */
    public function analyzeMLBased(string $text): array
    {
        try {
            $apiKey = config('services.google.natural_language_api_key');

            if (empty($apiKey)) {
                Log::warning('Google Natural Language API key not configured');
                return $this->analyzeKeywordBased($text); // Fallback to keyword-based
            }

            $response = Http::post("https://language.googleapis.com/v1/documents:analyzeSentiment?key={$apiKey}", [
                'document' => [
                    'type' => 'PLAIN_TEXT',
                    'content' => $text,
                ],
                'encodingType' => 'UTF8',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $sentimentScore = $data['documentSentiment']['score'] ?? 0; // -1 to 1
                $magnitude = $data['documentSentiment']['magnitude'] ?? 0;

                // Convert score from -1..1 to 0..1
                $normalizedScore = ($sentimentScore + 1) / 2;

                // Determine label
                if ($sentimentScore >= 0.25) {
                    $label = 'positive';
                } elseif ($sentimentScore <= -0.25) {
                    $label = 'negative';
                } else {
                    $label = 'neutral';
                }

                $language = $data['language'] ?? $this->detectLanguage($text);

                return [
                    'score' => round($normalizedScore, 2),
                    'label' => $label,
                    'language' => $language,
                    'method' => 'ml',
                    'raw_score' => $sentimentScore,
                    'magnitude' => $magnitude,
                ];
            } else {
                Log::error('Google Natural Language API error: ' . $response->body());
                return $this->analyzeKeywordBased($text); // Fallback
            }
        } catch (\Exception $e) {
            Log::error('Sentiment analysis ML error: ' . $e->getMessage());
            return $this->analyzeKeywordBased($text); // Fallback
        }
    }

    /**
     * Analyze sentiment (uses configured method)
     */
    public function analyze(string $text, ?string $method = null): array
    {
        if (empty(trim($text))) {
            return [
                'score' => 0.5,
                'label' => 'neutral',
                'language' => 'unknown',
                'method' => 'none',
            ];
        }

        // Get method from config if not specified
        if ($method === null) {
            $method = config('services.sentiment.method', 'keyword');
        }

        if ($method === 'ml') {
            return $this->analyzeMLBased($text);
        } else {
            return $this->analyzeKeywordBased($text);
        }
    }

    /**
     * Detect language (Bangla or English)
     */
    private function detectLanguage(string $text): string
    {
        // Check for Bangla Unicode range (U+0980 to U+09FF)
        if (preg_match('/[\x{0980}-\x{09FF}]/u', $text)) {
            return 'bn'; // Bangla
        }

        return 'en'; // Default to English
    }

    /**
     * Check if comment is positive based on threshold
     */
    public function isPositive(string $text, float $threshold = 0.6, ?string $method = null): bool
    {
        $result = $this->analyze($text, $method);
        return $result['score'] >= $threshold;
    }

    /**
     * Batch analyze multiple texts
     */
    public function analyzeBatch(array $texts, ?string $method = null): array
    {
        $results = [];
        foreach ($texts as $text) {
            $results[] = $this->analyze($text, $method);
        }
        return $results;
    }
}
