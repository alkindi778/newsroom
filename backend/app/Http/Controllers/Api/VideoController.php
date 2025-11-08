<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * Get all published videos (Frontend)
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'search' => $request->search,
                'video_type' => $request->video_type,
                'sort' => $request->sort ?? 'recent',
                'per_page' => $request->per_page ?? 12,
            ];

            $videos = $this->videoService->getPublishedVideos($filters);
            $sectionTitle = $this->videoService->getSectionTitle();

            return response()->json([
                'success' => true,
                'data' => VideoResource::collection($videos->items()),
                'section_title' => $sectionTitle,
                'pagination' => [
                    'total' => $videos->total(),
                    'per_page' => $videos->perPage(),
                    'current_page' => $videos->currentPage(),
                    'last_page' => $videos->lastPage(),
                    'from' => $videos->firstItem(),
                    'to' => $videos->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching videos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured videos (Frontend)
     */
    public function featured(Request $request)
    {
        try {
            $limit = $request->limit ?? 4;
            $videos = $this->videoService->getFeaturedVideos($limit);
            $sectionTitle = $this->videoService->getSectionTitle();

            return response()->json([
                'success' => true,
                'data' => VideoResource::collection($videos),
                'section_title' => $sectionTitle
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching featured videos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single video by slug (Frontend)
     */
    public function show($slug)
    {
        try {
            $video = $this->videoService->getVideoBySlug($slug);

            return response()->json([
                'success' => true,
                'data' => new VideoResource($video)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Create new video (Admin)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'nullable|string',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['user_id'] = auth()->id();

            $video = $this->videoService->createVideo($data);

            return response()->json([
                'success' => true,
                'message' => 'Video created successfully',
                'data' => new VideoResource($video)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating video',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update video (Admin)
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'nullable|string',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $video = $this->videoService->updateVideo($id, $validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Video updated successfully',
                'data' => new VideoResource($video)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating video',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete video (Admin)
     */
    public function destroy($id)
    {
        try {
            $this->videoService->deleteVideo($id);

            return response()->json([
                'success' => true,
                'message' => 'Video deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting video',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Publish video (Admin)
     */
    public function publish($id)
    {
        try {
            $video = $this->videoService->publishVideo($id);

            return response()->json([
                'success' => true,
                'message' => 'Video published successfully',
                'data' => new VideoResource($video)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error publishing video',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unpublish video (Admin)
     */
    public function unpublish($id)
    {
        try {
            $video = $this->videoService->unpublishVideo($id);

            return response()->json([
                'success' => true,
                'message' => 'Video unpublished successfully',
                'data' => new VideoResource($video)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error unpublishing video',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle featured status (Admin)
     */
    public function toggleFeatured($id)
    {
        try {
            $video = $this->videoService->toggleFeatured($id);

            return response()->json([
                'success' => true,
                'message' => 'Featured status toggled successfully',
                'data' => new VideoResource($video)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling featured status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Increment view count (Frontend)
     */
    public function incrementView($id)
    {
        try {
            $video = \App\Models\Video::findOrFail($id);
            $video->increment('views');

            return response()->json([
                'success' => true,
                'views' => $video->views
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error incrementing views'
            ], 500);
        }
    }

    /**
     * Like video (Frontend)
     */
    public function like($id)
    {
        try {
            $video = \App\Models\Video::findOrFail($id);
            $video->increment('likes');

            return response()->json([
                'success' => true,
                'likes' => $video->likes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error liking video'
            ], 500);
        }
    }
}
