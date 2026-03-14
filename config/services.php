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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    // Official Platform APIs (Recommended)
    'youtube' => [
        'api_key' => env('YOUTUBE_API_KEY'), // Google Official API
    ],

    'twitter' => [
        'bearer_token' => env('X_BEARER_TOKEN') ?: env('TWITTER_BEARER_TOKEN'), // X Official API
        'api_key' => env('X_API_KEY') ?: env('TWITTER_API_KEY'),
        'api_secret' => env('X_API_SECRET') ?: env('TWITTER_API_SECRET'),
        'callback_url' => env('X_CALLBACK_URL') ?: env('TWITTER_CALLBACK_URL'),
    ],

    'instagram' => [
        'access_token' => env('INSTAGRAM_ACCESS_TOKEN'), // Meta Official API
        'client_id' => env('INSTAGRAM_CLIENT_ID'),
        'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),
    ],

    'facebook' => [
        'access_token' => env('FACEBOOK_ACCESS_TOKEN'), // Meta Official API
        'app_id' => env('FACEBOOK_APP_ID'),
        'app_secret' => env('FACEBOOK_APP_SECRET'),
    ],

    'linkedin' => [
        'access_token' => env('LINKEDIN_ACCESS_TOKEN'), // LinkedIn Official API
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
    ],

    'tiktok' => [
        'access_token' => env('TIKTOK_ACCESS_TOKEN'), // TikTok Official API
        'client_key' => env('TIKTOK_CLIENT_KEY'),
        'client_secret' => env('TIKTOK_CLIENT_SECRET'),
    ],

    // WhatsApp API Configuration
    'whatsapp' => [
        'enabled' => env('WHATSAPP_ENABLED', false),
        'provider' => env('WHATSAPP_PROVIDER', 'fonnte'),
        'fonnte_api_url' => env('FONNTE_API_URL', 'https://api.fonnte.com'),
        'fonnte_token' => env('FONNTE_TOKEN'),
        'fonnte_device' => env('FONNTE_DEVICE'),
        'webhook_url' => env('WHATSAPP_WEBHOOK_URL'),
        'webhook_token' => env('WHATSAPP_WEBHOOK_TOKEN'),
    ],

    // Speech-to-Text Configuration
    'speech_to_text' => [
        'enabled' => env('SPEECH_TO_TEXT_ENABLED', false),
        'provider' => env('SPEECH_TO_TEXT_PROVIDER', 'google'),
        'google_api_key' => env('GOOGLE_SPEECH_API_KEY'),
        'openai_api_key' => env('OPENAI_WHISPER_API_KEY'),
        'assembly_ai_key' => env('ASSEMBLY_AI_API_KEY'),
    ],

];
