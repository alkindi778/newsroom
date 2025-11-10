<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManifestController extends Controller
{
    /**
     * Get PWA manifest data
     */
    public function getManifest()
    {
        // جلب الإعدادات من Database
        $siteName = config('app.name');
        $siteDescription = setting('site_description');
        $themeColor = setting('theme_color');
        $backgroundColor = setting('background_color');
        
        $manifest = [
            'name' => $siteName,
            'short_name' => $siteName,
            'description' => $siteDescription,
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => $backgroundColor,
            'theme_color' => $themeColor,
            'orientation' => 'portrait',
            'lang' => 'ar',
            'dir' => 'rtl',
            'icons' => [
                [
                    'src' => '/favicon.ico',
                    'sizes' => '48x48',
                    'type' => 'image/x-icon'
                ],
                [
                    'src' => '/icon-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ],
                [
                    'src' => '/icon-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ],
                [
                    'src' => '/badge-72x72.png',
                    'sizes' => '72x72',
                    'type' => 'image/png'
                ]
            ],
            'categories' => ['news', 'media']
        ];

        return response()->json($manifest);
    }
}
