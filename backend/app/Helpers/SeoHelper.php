<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SeoHelper
{
    protected array $meta = [];
    protected array $og = [];
    protected array $twitter = [];
    protected array $schema = [];

    /**
     * تعيين عنوان الصفحة
     */
    public function setTitle(string $title, ?string $siteName = null): self
    {
        $siteName = $siteName ?? config('app.name');
        $fullTitle = $title . ' | ' . $siteName;

        $this->meta['title'] = $fullTitle;
        $this->og['og:title'] = $title;
        $this->twitter['twitter:title'] = $title;

        return $this;
    }

    /**
     * تعيين الوصف
     */
    public function setDescription(string $description, int $maxLength = 160): self
    {
        $description = Str::limit(strip_tags($description), $maxLength, '...');

        $this->meta['description'] = $description;
        $this->og['og:description'] = $description;
        $this->twitter['twitter:description'] = $description;

        return $this;
    }

    /**
     * تعيين الصورة المميزة
     */
    public function setImage(string $imageUrl, ?int $width = null, ?int $height = null): self
    {
        $fullUrl = Str::startsWith($imageUrl, 'http') 
            ? $imageUrl 
            : url($imageUrl);

        $this->og['og:image'] = $fullUrl;
        $this->twitter['twitter:image'] = $fullUrl;

        if ($width && $height) {
            $this->og['og:image:width'] = $width;
            $this->og['og:image:height'] = $height;
        }

        return $this;
    }

    /**
     * تعيين الكلمات المفتاحية
     */
    public function setKeywords(array|string $keywords): self
    {
        if (is_array($keywords)) {
            $keywords = implode(', ', $keywords);
        }

        $this->meta['keywords'] = $keywords;

        return $this;
    }

    /**
     * تعيين نوع المحتوى (مقال، موقع، إلخ)
     */
    public function setType(string $type = 'website'): self
    {
        $this->og['og:type'] = $type;

        return $this;
    }

    /**
     * تعيين URL الصفحة
     */
    public function setUrl(?string $url = null): self
    {
        $url = $url ?? url()->current();

        $this->og['og:url'] = $url;
        $this->meta['canonical'] = $url;

        return $this;
    }

    /**
     * تعيين اسم الموقع
     */
    public function setSiteName(string $siteName): self
    {
        $this->og['og:site_name'] = $siteName;

        return $this;
    }

    /**
     * تعيين المؤلف
     */
    public function setAuthor(string $author): self
    {
        $this->meta['author'] = $author;

        return $this;
    }

    /**
     * تعيين تاريخ النشر (للمقالات)
     */
    public function setPublishedTime(string|\DateTime $publishedTime): self
    {
        if ($publishedTime instanceof \DateTime) {
            $publishedTime = $publishedTime->format('c');
        }

        $this->og['article:published_time'] = $publishedTime;

        return $this;
    }

    /**
     * تعيين تاريخ التعديل (للمقالات)
     */
    public function setModifiedTime(string|\DateTime $modifiedTime): self
    {
        if ($modifiedTime instanceof \DateTime) {
            $modifiedTime = $modifiedTime->format('c');
        }

        $this->og['article:modified_time'] = $modifiedTime;

        return $this;
    }

    /**
     * تعيين اللغة
     */
    public function setLocale(string $locale = 'ar_SA'): self
    {
        $this->og['og:locale'] = $locale;

        return $this;
    }

    /**
     * إضافة تاج Twitter Card
     */
    public function setTwitterCard(string $cardType = 'summary_large_image'): self
    {
        $this->twitter['twitter:card'] = $cardType;

        return $this;
    }

    /**
     * إضافة حساب Twitter
     */
    public function setTwitterSite(string $handle): self
    {
        if (!Str::startsWith($handle, '@')) {
            $handle = '@' . $handle;
        }

        $this->twitter['twitter:site'] = $handle;

        return $this;
    }

    /**
     * إضافة Schema.org JSON-LD
     */
    public function addSchema(array $schema): self
    {
        $this->schema[] = $schema;

        return $this;
    }

    /**
     * Schema للمقال
     */
    public function articleSchema(
        string $headline,
        string $description,
        string $imageUrl,
        string $authorName,
        string $publishedDate,
        ?string $modifiedDate = null
    ): self {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $headline,
            'description' => $description,
            'image' => $imageUrl,
            'datePublished' => $publishedDate,
            'dateModified' => $modifiedDate ?? $publishedDate,
            'author' => [
                '@type' => 'Person',
                'name' => $authorName
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => url('/logo.png')
                ]
            ]
        ];

        $this->addSchema($schema);

        return $this;
    }

    /**
     * إنشاء كود HTML للـ Meta Tags
     */
    public function render(): string
    {
        $html = '';

        // Meta Tags العادية
        foreach ($this->meta as $name => $content) {
            if ($name === 'title') {
                $html .= "<title>{$content}</title>\n";
            } elseif ($name === 'canonical') {
                $html .= "<link rel=\"canonical\" href=\"{$content}\">\n";
            } else {
                $html .= "<meta name=\"{$name}\" content=\"{$content}\">\n";
            }
        }

        // Open Graph Tags
        foreach ($this->og as $property => $content) {
            $html .= "<meta property=\"{$property}\" content=\"{$content}\">\n";
        }

        // Twitter Tags
        foreach ($this->twitter as $name => $content) {
            $html .= "<meta name=\"{$name}\" content=\"{$content}\">\n";
        }

        // Schema.org JSON-LD
        foreach ($this->schema as $schema) {
            $json = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $html .= "<script type=\"application/ld+json\">{$json}</script>\n";
        }

        return $html;
    }

    /**
     * الحصول على بيانات SEO كمصفوفة
     */
    public function toArray(): array
    {
        return [
            'meta' => $this->meta,
            'og' => $this->og,
            'twitter' => $this->twitter,
            'schema' => $this->schema,
        ];
    }

    /**
     * إعداد سريع للمقال
     */
    public static function forArticle(
        string $title,
        string $description,
        string $imageUrl,
        string $authorName,
        \DateTime $publishedDate,
        ?\DateTime $modifiedDate = null,
        ?string $url = null
    ): self {
        $seo = new self();

        return $seo
            ->setTitle($title)
            ->setDescription($description)
            ->setImage($imageUrl, 1200, 630)
            ->setAuthor($authorName)
            ->setType('article')
            ->setPublishedTime($publishedDate)
            ->setModifiedTime($modifiedDate ?? $publishedDate)
            ->setUrl($url)
            ->setLocale('ar_SA')
            ->setTwitterCard('summary_large_image')
            ->articleSchema(
                $title,
                $description,
                $imageUrl,
                $authorName,
                $publishedDate->format('c'),
                $modifiedDate?->format('c')
            );
    }
}
