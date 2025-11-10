<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\Opinion;
use App\Models\Video;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class SitemapService
{
    protected $baseUrl;
    protected $urls = [];

    public function __construct()
    {
        // استخدام رابط الـ Frontend بدلاً من الـ Backend
        $this->baseUrl = env('FRONTEND_URL', config('app.url'));
    }

    /**
     * إنشاء Sitemap كامل
     */
    public function generate(): string
    {
        // استخدام Cache للـ Sitemap (يتم تحديثه كل ساعة)
        return Cache::remember('sitemap', 3600, function () {
            $this->addStaticPages();
            $this->addArticles();
            $this->addCategories();
            $this->addOpinions();
            $this->addVideos();

            return $this->buildXml();
        });
    }

    /**
     * إضافة الصفحات الثابتة
     */
    protected function addStaticPages(): void
    {
        $pages = [
            ['url' => '', 'priority' => '1.0', 'changefreq' => 'hourly'],
            ['url' => 'news', 'priority' => '0.9', 'changefreq' => 'hourly'],
            ['url' => 'opinions', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => 'writers', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['url' => 'about', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => 'contact', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ];

        foreach ($pages as $page) {
            $this->addUrl(
                url: $this->baseUrl . '/' . $page['url'],
                lastmod: now()->toIso8601String(),
                changefreq: $page['changefreq'],
                priority: $page['priority']
            );
        }
    }

    /**
     * إضافة المقالات
     */
    protected function addArticles(): void
    {
        Article::where('is_published', true)
            ->with(['category'])
            ->orderBy('published_at', 'desc')
            ->chunk(100, function ($articles) {
                foreach ($articles as $article) {
                    $this->addUrl(
                        url: $this->baseUrl . '/news/' . $article->slug,
                        lastmod: $article->updated_at->toIso8601String(),
                        changefreq: 'weekly',
                        priority: '0.7',
                        images: $this->getArticleImages($article),
                        title: $article->title,
                        isNews: true
                    );
                }
            });
    }

    /**
     * إضافة الآراء
     */
    protected function addOpinions(): void
    {
        Opinion::where('is_published', true)
            ->with(['writer'])
            ->orderBy('published_at', 'desc')
            ->chunk(100, function ($opinions) {
                foreach ($opinions as $opinion) {
                    $images = [];
                    if ($opinion->featured_image || $opinion->image) {
                        $imageUrl = $opinion->featured_image ?? $opinion->image;
                        $images[] = [
                            'loc' => $this->getImageUrl($imageUrl),
                            'title' => $opinion->title,
                        ];
                    }

                    $this->addUrl(
                        url: $this->baseUrl . '/opinions/' . $opinion->slug,
                        lastmod: $opinion->updated_at->toIso8601String(),
                        changefreq: 'weekly',
                        priority: '0.6',
                        images: $images,
                        title: $opinion->title
                    );
                }
            });
    }

    /**
     * إضافة الفيديوهات
     */
    protected function addVideos(): void
    {
        Video::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->chunk(100, function ($videos) {
                foreach ($videos as $video) {
                    $images = [];
                    if ($video->thumbnail || $video->image) {
                        $imageUrl = $video->thumbnail ?? $video->image;
                        $images[] = [
                            'loc' => $this->getImageUrl($imageUrl),
                            'title' => $video->title,
                        ];
                    }

                    $this->addUrl(
                        url: $this->baseUrl . '/videos/' . $video->slug,
                        lastmod: $video->updated_at->toIso8601String(),
                        changefreq: 'weekly',
                        priority: '0.6',
                        images: $images,
                        title: $video->title
                    );
                }
            });
    }

    /**
     * إضافة التصنيفات
     */
    protected function addCategories(): void
    {
        Category::all()->each(function ($category) {
            $this->addUrl(
                url: $this->baseUrl . '/category/' . $category->slug,
                lastmod: $category->updated_at->toIso8601String(),
                changefreq: 'daily',
                priority: '0.8'
            );
        });
    }

    /**
     * إضافة URL إلى Sitemap
     */
    protected function addUrl(
        string $url,
        string $lastmod,
        string $changefreq = 'weekly',
        string $priority = '0.5',
        array $images = [],
        ?string $title = null,
        bool $isNews = false
    ): void {
        $this->urls[] = [
            'loc' => htmlspecialchars($url, ENT_XML1, 'UTF-8'),
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority,
            'images' => $images,
            'title' => $title ? htmlspecialchars($title, ENT_XML1, 'UTF-8') : null,
            'isNews' => $isNews,
        ];
    }

    /**
     * الحصول على صور المقال
     */
    protected function getArticleImages($article): array
    {
        $images = [];

        // الصورة المميزة أو الصورة العادية
        $imageUrl = $article->featured_image ?? $article->image;
        
        if ($imageUrl) {
            $images[] = [
                'loc' => $this->getImageUrl($imageUrl),
                'title' => $article->title,
                'caption' => $article->subtitle ?? $article->excerpt ?? null,
            ];
        }

        return $images;
    }

    /**
     * الحصول على URL كامل للصورة
     */
    protected function getImageUrl(string $path): string
    {
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return url('storage/' . $path);
    }

    /**
     * بناء XML
     */
    protected function buildXml(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"' . "\n";
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($this->urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url['loc']}</loc>\n";
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";

            // إضافة News tags للمقالات الإخبارية
            if ($url['isNews'] && $url['title']) {
                $xml .= "    <news:news>\n";
                $xml .= "      <news:publication>\n";
                $xml .= "        <news:name>" . config('app.name') . "</news:name>\n";
                $xml .= "        <news:language>ar</news:language>\n";
                $xml .= "      </news:publication>\n";
                $xml .= "      <news:publication_date>{$url['lastmod']}</news:publication_date>\n";
                $xml .= "      <news:title>{$url['title']}</news:title>\n";
                $xml .= "    </news:news>\n";
            }

            // إضافة الصور إن وجدت
            foreach ($url['images'] as $image) {
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>{$image['loc']}</image:loc>\n";
                if (isset($image['title'])) {
                    $xml .= "      <image:title>" . htmlspecialchars($image['title'], ENT_XML1, 'UTF-8') . "</image:title>\n";
                }
                if (isset($image['caption'])) {
                    $xml .= "      <image:caption>" . htmlspecialchars($image['caption'], ENT_XML1, 'UTF-8') . "</image:caption>\n";
                }
                $xml .= "    </image:image>\n";
            }

            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * مسح Cache الـ Sitemap
     */
    public function clearCache(): void
    {
        Cache::forget('sitemap');
    }

    /**
     * إنشاء Sitemap Index (للمواقع الكبيرة)
     */
    public function generateIndex(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $sitemaps = [
            ['url' => '/sitemap-articles.xml', 'lastmod' => now()],
            ['url' => '/sitemap-opinions.xml', 'lastmod' => now()],
            ['url' => '/sitemap-videos.xml', 'lastmod' => now()],
            ['url' => '/sitemap-pages.xml', 'lastmod' => now()],
        ];

        foreach ($sitemaps as $sitemap) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>" . $this->baseUrl . $sitemap['url'] . "</loc>\n";
            $xml .= "    <lastmod>" . $sitemap['lastmod']->toIso8601String() . "</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';

        return $xml;
    }
}
