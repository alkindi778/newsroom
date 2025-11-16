<?php

namespace App\Http\Controllers;

use App\Models\RssFeed;
use App\Models\Article;
use Illuminate\Http\Response;

class RssController extends Controller
{
    public function show($slug)
    {
        $feed = RssFeed::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get articles based on category filter
        $query = Article::where('is_published', true)
            ->where('approval_status', 'approved')
            ->with(['category', 'user'])
            ->latest('published_at');

        if ($feed->category_id) {
            $query->where('category_id', $feed->category_id);
        }

        $articles = $query->take($feed->items_count)->get();

        // Update last generated time
        $feed->update(['last_generated_at' => now()]);

        // Generate RSS XML
        $xml = $this->generateRssXml($feed, $articles);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    protected function generateRssXml($feed, $articles)
    {
        $siteUrl = config('app.url');
        $feedUrl = route('rss.show', $feed->slug);
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">' . "\n";
        $xml .= '  <channel>' . "\n";
        $xml .= '    <title>' . $this->xmlEncode($feed->title) . '</title>' . "\n";
        $xml .= '    <link>' . $siteUrl . '</link>' . "\n";
        $xml .= '    <description>' . $this->xmlEncode($feed->description ?? 'أحدث الأخبار') . '</description>' . "\n";
        $xml .= '    <language>' . $feed->language . '</language>' . "\n";
        $xml .= '    <lastBuildDate>' . now()->toRssString() . '</lastBuildDate>' . "\n";
        $xml .= '    <ttl>' . $feed->ttl . '</ttl>' . "\n";
        
        if ($feed->copyright) {
            $xml .= '    <copyright>' . $this->xmlEncode($feed->copyright) . '</copyright>' . "\n";
        }
        
        if ($feed->image_url) {
            $xml .= '    <image>' . "\n";
            $xml .= '      <url>' . $feed->image_url . '</url>' . "\n";
            $xml .= '      <title>' . $this->xmlEncode($feed->title) . '</title>' . "\n";
            $xml .= '      <link>' . $siteUrl . '</link>' . "\n";
            $xml .= '    </image>' . "\n";
        }
        
        $xml .= '    <atom:link href="' . $feedUrl . '" rel="self" type="application/rss+xml" />' . "\n";

        foreach ($articles as $article) {
            $xml .= '    <item>' . "\n";
            $xml .= '      <title>' . $this->xmlEncode($article->title) . '</title>' . "\n";
            $xml .= '      <link>' . $siteUrl . '/articles/' . $article->slug . '</link>' . "\n";
            $xml .= '      <guid isPermaLink="true">' . $siteUrl . '/articles/' . $article->slug . '</guid>' . "\n";
            $xml .= '      <description>' . $this->xmlEncode($article->excerpt ?? strip_tags(substr($article->content, 0, 200))) . '</description>' . "\n";
            
            if ($article->published_at) {
                $xml .= '      <pubDate>' . $article->published_at->toRssString() . '</pubDate>' . "\n";
            }
            
            if ($article->category) {
                $xml .= '      <category>' . $this->xmlEncode($article->category->name) . '</category>' . "\n";
            }
            
            if ($article->user) {
                $xml .= '      <dc:creator>' . $this->xmlEncode($article->user->name) . '</dc:creator>' . "\n";
            }
            
            if ($article->image) {
                $imageUrl = asset('storage/' . $article->image);
                $xml .= '      <enclosure url="' . $imageUrl . '" type="image/jpeg" />' . "\n";
            }
            
            $xml .= '    </item>' . "\n";
        }

        $xml .= '  </channel>' . "\n";
        $xml .= '</rss>';

        return $xml;
    }

    protected function xmlEncode($string)
    {
        return htmlspecialchars($string, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
