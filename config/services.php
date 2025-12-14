<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Login Services
    |--------------------------------------------------------------------------
    |
    | Configuration for social authentication providers
    |
    */

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL', env('APP_URL') . '/login/google/callback'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URL', env('APP_URL') . '/login/facebook/callback'),
    ],

    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect' => env('APPLE_REDIRECT_URL', env('APP_URL') . '/login/apple/callback'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Maps API
    |--------------------------------------------------------------------------
    |
    | API key for Google Maps integration on contact page
    |
    */

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Natural Language API
    |--------------------------------------------------------------------------
    |
    | API key for Google Cloud Natural Language API for sentiment analysis
    |
    */

    'google' => array_merge(config('services.google', []), [
        'natural_language_api_key' => env('GOOGLE_NATURAL_LANGUAGE_API_KEY'),
    ]),

    /*
    |--------------------------------------------------------------------------
    | Sentiment Analysis Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for sentiment analysis service
    | method: 'keyword' or 'ml' (machine learning)
    |
    */

    'sentiment' => [
        'method' => env('SENTIMENT_ANALYSIS_METHOD', 'keyword'), // 'keyword' or 'ml'
        'threshold' => env('SENTIMENT_POSITIVE_THRESHOLD', 0.6), // 0.0 to 1.0
    ],

];
