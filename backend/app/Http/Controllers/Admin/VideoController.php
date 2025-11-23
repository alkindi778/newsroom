<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * Display a listing of videos
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->search,
            'status' => $request->status,
            'featured' => $request->featured,
            'video_type' => $request->video_type,
            'sort' => $request->sort,
            'per_page' => $request->per_page ?? 15,
        ];

        $videos = $this->videoService->getAllVideos($filters);
        $sectionTitle = $this->videoService->getSectionTitle();

        return view('admin.videos.index', compact('videos', 'sectionTitle'));
    }

    /**
     * Show the form for creating a new video
     */
    public function create()
    {
        return view('admin.videos.create');
    }

    /**
     * Store a newly created video
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        try {
            $data = $request->except('_token');
            $data['user_id'] = auth()->id();
            
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $request->file('thumbnail');
            }

            $this->videoService->createVideo($data);

            return redirect()->route('admin.videos.index')
                ->with('success', 'تم إضافة الفيديو بنجاح');
        } catch (\Exception $e) {
            Log::error('Error creating video: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الفيديو: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified video
     */
    public function show($id)
    {
        try {
            $video = $this->videoService->getVideoById($id);
            return view('admin.videos.show', compact('video'));
        } catch (\Exception $e) {
            return redirect()->route('admin.videos.index')
                ->with('error', 'الفيديو غير موجود');
        }
    }

    /**
     * Show the form for editing the specified video
     */
    public function edit($id)
    {
        try {
            $video = $this->videoService->getVideoById($id);
            return view('admin.videos.edit', compact('video'));
        } catch (\Exception $e) {
            return redirect()->route('admin.videos.index')
                ->with('error', 'الفيديو غير موجود');
        }
    }

    /**
     * Update the specified video
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        try {
            $data = $request->except('_token', '_method');
            
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $request->file('thumbnail');
            }

            $this->videoService->updateVideo($id, $data);

            return redirect()->route('admin.videos.index')
                ->with('success', 'تم تحديث الفيديو بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating video: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الفيديو: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified video
     */
    public function destroy($id)
    {
        try {
            $this->videoService->deleteVideo($id);
            return redirect()->route('admin.videos.index')
                ->with('success', 'تم حذف الفيديو بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف الفيديو');
        }
    }

    /**
     * Publish video
     */
    public function publish($id)
    {
        try {
            $this->videoService->publishVideo($id);
            return back()->with('success', 'تم نشر الفيديو بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء نشر الفيديو');
        }
    }

    /**
     * Unpublish video
     */
    public function unpublish($id)
    {
        try {
            $this->videoService->unpublishVideo($id);
            return back()->with('success', 'تم إلغاء نشر الفيديو بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إلغاء نشر الفيديو');
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        try {
            $this->videoService->toggleFeatured($id);
            return back()->with('success', 'تم تحديث حالة التمييز بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث حالة التمييز');
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:publish,unpublish,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:videos,id'
        ]);

        try {
            switch ($request->action) {
                case 'publish':
                    $this->videoService->bulkPublish($request->ids);
                    $message = 'تم نشر الفيديوهات المحددة بنجاح';
                    break;
                case 'unpublish':
                    $this->videoService->bulkUnpublish($request->ids);
                    $message = 'تم إلغاء نشر الفيديوهات المحددة بنجاح';
                    break;
                case 'delete':
                    $this->videoService->bulkDelete($request->ids);
                    $message = 'تم حذف الفيديوهات المحددة بنجاح';
                    break;
            }

            return redirect()->route('admin.videos.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تنفيذ العملية');
        }
    }

    /**
     * Fetch video information from URL (YouTube/Vimeo)
     */
    public function fetchVideoInfo(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        try {
            $url = $request->url;
            $info = [];

            // Check if YouTube
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $match)) {
                $videoId = $match[1];
                
                // Try YouTube Data API v3 if API key is available
                $apiKey = config('services.youtube.api_key');
                
                if ($apiKey) {
                    $apiUrl = "https://www.googleapis.com/youtube/v3/videos?id={$videoId}&key={$apiKey}&part=snippet,contentDetails";
                    $response = @file_get_contents($apiUrl);
                    
                    if ($response) {
                        $data = json_decode($response, true);
                        
                        if (isset($data['items'][0])) {
                            $video = $data['items'][0];
                            $snippet = $video['snippet'];
                            $contentDetails = $video['contentDetails'];
                            
                            // Parse ISO 8601 duration (PT15M33S -> 15:33)
                            $duration = '';
                            if (isset($contentDetails['duration'])) {
                                preg_match('/PT(\d+H)?(\d+M)?(\d+S)?/', $contentDetails['duration'], $durationMatch);
                                $hours = isset($durationMatch[1]) ? rtrim($durationMatch[1], 'H') : 0;
                                $minutes = isset($durationMatch[2]) ? rtrim($durationMatch[2], 'M') : 0;
                                $seconds = isset($durationMatch[3]) ? rtrim($durationMatch[3], 'S') : 0;
                                
                                if ($hours > 0) {
                                    $duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                } else {
                                    $duration = sprintf('%02d:%02d', $minutes, $seconds);
                                }
                            }
                            
                            $info = [
                                'success' => true,
                                'title' => $snippet['title'] ?? '',
                                'description' => $snippet['description'] ?? '',
                                'duration' => $duration,
                                'thumbnail' => $snippet['thumbnails']['maxres']['url'] ?? $snippet['thumbnails']['high']['url'] ?? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg",
                                'type' => 'youtube'
                            ];
                        }
                    }
                } else {
                    // Fallback to oEmbed API (no API key needed)
                    $oembedUrl = "https://www.youtube.com/oembed?url=" . urlencode($url) . "&format=json";
                    $response = @file_get_contents($oembedUrl);
                    
                    if ($response) {
                        $data = json_decode($response, true);
                        $info = [
                            'success' => true,
                            'title' => $data['title'] ?? '',
                            'description' => '', // YouTube oEmbed doesn't provide description
                            'duration' => '', // Would need YouTube Data API for this
                            'thumbnail' => "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg",
                            'type' => 'youtube'
                        ];
                    }
                }
            }
            // Check if Vimeo
            elseif (preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/i', $url, $match)) {
                $videoId = $match[1];
                
                // Use Vimeo oEmbed API
                $oembedUrl = "https://vimeo.com/api/oembed.json?url=" . urlencode($url);
                $response = @file_get_contents($oembedUrl);
                
                if ($response) {
                    $data = json_decode($response, true);
                    
                    // Convert duration from seconds to HH:MM:SS
                    $duration = '';
                    if (isset($data['duration'])) {
                        $seconds = $data['duration'];
                        $hours = floor($seconds / 3600);
                        $minutes = floor(($seconds % 3600) / 60);
                        $secs = $seconds % 60;
                        
                        if ($hours > 0) {
                            $duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
                        } else {
                            $duration = sprintf('%02d:%02d', $minutes, $secs);
                        }
                    }
                    
                    $info = [
                        'success' => true,
                        'title' => $data['title'] ?? '',
                        'description' => $data['description'] ?? '',
                        'duration' => $duration,
                        'thumbnail' => $data['thumbnail_url'] ?? '',
                        'type' => 'vimeo'
                    ];
                }
            }
            
            // Check if Facebook
            elseif (preg_match('/(?:facebook\.com\/(?:[^\/]+\/videos\/|video\.php\?v=|watch\/\?v=|share\/v\/|share\/r\/|reel\/)|fb\.watch\/)([^"&?\/\s]+)/i', $url, $match)) {
                $videoId = $match[1];
                
                // Use basic scraping to get Open Graph data
                // Note: This works for public videos. Facebook might block scraping eventually.
                // Set User-Agent to mimic a browser
                $context = stream_context_create([
                    'http' => [
                        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
                    ]
                ]);
                
                // For fb.watch short links, follow redirect first to get full URL
                if (strpos($url, 'fb.watch') !== false) {
                    $headers = get_headers($url, 1);
                    if (isset($headers['Location'])) {
                        $url = is_array($headers['Location']) ? end($headers['Location']) : $headers['Location'];
                    }
                }
                
                $html = @file_get_contents($url, false, $context);
                
                if ($html) {
                    // Extract OG tags
                    preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $titleMatch);
                    preg_match('/<meta property="og:description" content="([^"]+)"/i', $html, $descMatch);
                    preg_match('/<meta property="og:image" content="([^"]+)"/i', $html, $imageMatch);
                    
                    $title = isset($titleMatch[1]) ? html_entity_decode($titleMatch[1]) : 'Facebook Video';
                    // Clean up title (remove " | Facebook")
                    $title = str_replace(' | Facebook', '', $title);
                    
                    $info = [
                        'success' => true,
                        'title' => $title,
                        'description' => isset($descMatch[1]) ? html_entity_decode($descMatch[1]) : '',
                        'duration' => '', // Hard to scrape duration reliably without API
                        'thumbnail' => isset($imageMatch[1]) ? html_entity_decode($imageMatch[1]) : '',
                        'type' => 'facebook'
                    ];
                } else {
                    // Fallback if scraping fails (e.g. blocked)
                    $info = [
                        'success' => true,
                        'title' => 'Facebook Video',
                        'description' => '',
                        'duration' => '',
                        'thumbnail' => '',
                        'type' => 'facebook'
                    ];
                }
            }

            if (empty($info)) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم التعرف على رابط الفيديو'
                ]);
            }

            return response()->json($info);

        } catch (\Exception $e) {
            Log::error('Error fetching video info: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب معلومات الفيديو'
            ], 500);
        }
    }

    /**
     * Update section title
     */
    public function updateSectionTitle(Request $request)
    {
        $request->validate([
            'section_title' => 'required|string|max:255',
        ]);

        try {
            $this->videoService->updateSectionTitle($request->section_title);

            return redirect()->route('admin.videos.index')
                ->with('success', 'تم تحديث عنوان القسم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating section title: ' . $e->getMessage());
            return back()
                ->with('error', 'حدث خطأ أثناء تحديث العنوان: ' . $e->getMessage());
        }
    }
}
