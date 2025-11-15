<?php

return [
    /**
     * Social Media Platforms Configuration
     * التحكم الكامل في منصات التواصل الاجتماعي
     */

    'platforms' => [
        'facebook' => [
            'enabled' => env('FACEBOOK_ENABLED', false),
            'page_id' => env('FACEBOOK_PAGE_ID', ''),
            'access_token' => env('FACEBOOK_ACCESS_TOKEN', ''),
            'api_version' => 'v18.0',
            'auto_publish' => env('FACEBOOK_AUTO_PUBLISH', true),
            'include_image' => env('FACEBOOK_INCLUDE_IMAGE', true),
            'include_link' => env('FACEBOOK_INCLUDE_LINK', true),
        ],

        'twitter' => [
            'enabled' => env('TWITTER_ENABLED', false),
            'api_key' => env('TWITTER_API_KEY', ''),
            'api_secret' => env('TWITTER_API_SECRET', ''),
            'access_token' => env('TWITTER_ACCESS_TOKEN', ''),
            'access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET', ''),
            'bearer_token' => env('TWITTER_BEARER_TOKEN', ''),
            'auto_publish' => env('TWITTER_AUTO_PUBLISH', true),
            'include_image' => env('TWITTER_INCLUDE_IMAGE', true),
            'include_link' => env('TWITTER_INCLUDE_LINK', true),
        ],

        'telegram' => [
            'enabled' => env('TELEGRAM_ENABLED', false),
            'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),
            'channel_id' => env('TELEGRAM_CHANNEL_ID', ''),
            'auto_publish' => env('TELEGRAM_AUTO_PUBLISH', true),
            'include_image' => env('TELEGRAM_INCLUDE_IMAGE', true),
            'include_link' => env('TELEGRAM_INCLUDE_LINK', true),
        ],
    ],

    /**
     * Global Settings
     */
    'global' => [
        'auto_publish_on_create' => env('SOCIAL_AUTO_PUBLISH_ON_CREATE', true),
        'auto_publish_on_update' => env('SOCIAL_AUTO_PUBLISH_ON_UPDATE', false),
        'include_hashtags' => env('SOCIAL_INCLUDE_HASHTAGS', true),
        'include_category' => env('SOCIAL_INCLUDE_CATEGORY', true),
        'max_hashtags' => 5,
        'excerpt_length' => 280, // Twitter limit
    ],

    /**
     * Message Templates
     */
    'templates' => [
        'article' => '{title}

{link}',
        'video' => '🎥 {title}

{link}',
        'breaking_news' => '🔴 عاجل: {title}

{link}',
    ],
];
