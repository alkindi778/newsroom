<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000', 
        'http://127.0.0.1:3000',
        'http://192.168.1.101:3000',  // IP الحالي
        'http://192.168.1.106:3000',  // IP الجديد
        'http://192.168.1.107:3000'   // للوصول من الهاتف
    ],

    'allowed_origins_patterns' => [
        '/^http:\/\/localhost:\d+$/',      // أي منفذ على localhost
        '/^http:\/\/127\.0\.0\.1:\d+$/',   // أي منفذ على 127.0.0.1
        '/^http:\/\/192\.168\.1\.\d+:\d+$/' // أي IP محلي
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
