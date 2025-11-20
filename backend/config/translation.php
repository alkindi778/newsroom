<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Translation System Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all the configuration options for the automatic
    | translation system powered by Google Gemini AI.
    |
    */

    /**
     * Enable or disable automatic translation
     * Set to false to temporarily disable all automatic translations
     */
    'enabled' => env('TRANSLATION_ENABLED', true),

    /**
     * Auto-translate on article creation
     */
    'auto_translate_on_create' => env('TRANSLATION_AUTO_ON_CREATE', true),

    /**
     * Auto-translate when article content is updated
     */
    'auto_translate_on_update' => env('TRANSLATION_AUTO_ON_UPDATE', true),

    /**
     * Source language for translation
     */
    'source_language' => env('TRANSLATION_SOURCE_LANG', 'ar'),

    /**
     * Target language for translation
     */
    'target_language' => env('TRANSLATION_TARGET_LANG', 'en'),

    /**
     * Queue configuration for translation jobs
     */
    'queue' => [
        'connection' => env('TRANSLATION_QUEUE_CONNECTION', config('queue.default')),
        'name' => env('TRANSLATION_QUEUE_NAME', 'translations'),
        'tries' => env('TRANSLATION_QUEUE_TRIES', 3),
        'backoff' => env('TRANSLATION_QUEUE_BACKOFF', 60), // seconds
        'timeout' => env('TRANSLATION_QUEUE_TIMEOUT', 120), // seconds
    ],

    /**
     * Translation prompt configuration
     */
    'prompt' => [
        /**
         * Temperature for AI model (0.0 - 1.0)
         * Lower = more deterministic, Higher = more creative
         */
        'temperature' => env('TRANSLATION_TEMPERATURE', 0.3),

        /**
         * Top K sampling parameter
         */
        'top_k' => env('TRANSLATION_TOP_K', 40),

        /**
         * Top P (nucleus sampling) parameter
         */
        'top_p' => env('TRANSLATION_TOP_P', 0.95),

        /**
         * Maximum output tokens
         */
        'max_output_tokens' => env('TRANSLATION_MAX_TOKENS', 8192),
    ],

    /**
     * Logging configuration
     */
    'logging' => [
        /**
         * Enable detailed logging for translation processes
         */
        'enabled' => env('TRANSLATION_LOGGING', true),

        /**
         * Log level: debug, info, warning, error
         */
        'level' => env('TRANSLATION_LOG_LEVEL', 'info'),

        /**
         * Log successful translations
         */
        'log_success' => env('TRANSLATION_LOG_SUCCESS', true),

        /**
         * Log failed translations
         */
        'log_failures' => env('TRANSLATION_LOG_FAILURES', true),
    ],

    /**
     * Batch translation settings
     */
    'batch' => [
        /**
         * Number of articles to process in each chunk
         */
        'chunk_size' => env('TRANSLATION_BATCH_CHUNK_SIZE', 50),

        /**
         * Delay between processing chunks (milliseconds)
         */
        'delay_between_chunks' => env('TRANSLATION_BATCH_DELAY', 0),
    ],

    /**
     * Cache configuration for translations
     */
    'cache' => [
        /**
         * Enable caching of similar translations
         * (Future feature - not yet implemented)
         */
        'enabled' => env('TRANSLATION_CACHE_ENABLED', false),

        /**
         * Cache TTL in seconds
         */
        'ttl' => env('TRANSLATION_CACHE_TTL', 86400), // 24 hours
    ],

    /**
     * Validation rules
     */
    'validation' => [
        /**
         * Minimum content length to translate (characters)
         */
        'min_content_length' => env('TRANSLATION_MIN_LENGTH', 10),

        /**
         * Maximum content length to translate (characters)
         * Articles longer than this will still be translated, but might take longer
         */
        'max_content_length' => env('TRANSLATION_MAX_LENGTH', 50000),

        /**
         * Skip translation if content is already in target language
         * (Future feature - not yet implemented)
         */
        'detect_language' => env('TRANSLATION_DETECT_LANGUAGE', false),
    ],

    /**
     * Retry strategy
     */
    'retry' => [
        /**
         * Exponential backoff multiplier
         */
        'backoff_multiplier' => env('TRANSLATION_BACKOFF_MULTIPLIER', 1),

        /**
         * Maximum backoff time in seconds
         */
        'max_backoff' => env('TRANSLATION_MAX_BACKOFF', 300), // 5 minutes
    ],

    /**
     * Notification settings
     */
    'notifications' => [
        /**
         * Send notification when translation fails permanently
         */
        'notify_on_failure' => env('TRANSLATION_NOTIFY_FAILURE', false),

        /**
         * Email addresses to notify on failures (comma-separated)
         */
        'failure_recipients' => env('TRANSLATION_FAILURE_EMAILS', ''),
    ],

];
