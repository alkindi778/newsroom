<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    protected $sitemapService;

    public function __construct(SitemapService $sitemapService)
    {
        $this->sitemapService = $sitemapService;
    }

    /**
     * عرض Sitemap الرئيسي
     */
    public function index(): Response
    {
        $sitemap = $this->sitemapService->generate();

        return response($sitemap, 200)
            ->header('Content-Type', 'text/xml; charset=UTF-8');
    }

    /**
     * مسح Cache الـ Sitemap
     */
    public function refresh(): Response
    {
        $this->sitemapService->clearCache();
        $sitemap = $this->sitemapService->generate();

        return response($sitemap, 200)
            ->header('Content-Type', 'text/xml; charset=UTF-8');
    }
}
