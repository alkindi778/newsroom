<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OpinionResource;
use App\Services\OpinionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OpinionController extends Controller
{
    protected OpinionService $opinionService;

    public function __construct(OpinionService $opinionService)
    {
        $this->opinionService = $opinionService;
    }

    // GET /api/v1/opinions
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status'); // published|draft
        $writerId = $request->query('writer');
        $featured = $request->boolean('featured', null);
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $perPage = (int) $request->query('per_page', 10);

        $opinions = $this->opinionService->getAllWithFilters(
            $search,
            $status,
            $writerId,
            $featured,
            $sortBy,
            $sortDir,
            $perPage
        );

        return OpinionResource::collection($opinions);
    }

    // GET /api/v1/opinions/featured
    public function featured(Request $request)
    {
        $limit = min($request->query('limit', 10), 20);
        
        $opinions = $this->opinionService->getAllWithFilters(
            null,           // search
            'published',    // status
            null,           // writerId
            null,           // featured
            'created_at',   // sortBy
            'desc',         // sortDir
            $limit          // perPage
        );

        return OpinionResource::collection($opinions);
    }

    // GET /api/v1/opinions/{slugOrId}
    public function show($slugOrId): JsonResponse|OpinionResource
    {
        // Check if it's a numeric ID or a slug
        if (is_numeric($slugOrId)) {
            $opinion = $this->opinionService->getById($slugOrId);
        } else {
            $opinion = $this->opinionService->getBySlug($slugOrId);
        }
        
        if (!$opinion) {
            return response()->json([
                'message' => 'المقال غير موجود',
            ], 404);
        }
        
        // Increment views
        $this->opinionService->incrementViews($opinion->id);
        
        return new OpinionResource($opinion);
    }

    // POST /api/v1/opinions/{id}/like
    public function like($id): JsonResponse
    {
        $opinion = $this->opinionService->getById($id);
        
        if (!$opinion) {
            return response()->json([
                'message' => 'المقال غير موجود',
            ], 404);
        }

        $this->opinionService->incrementLikes($id);

        return response()->json([
            'success' => true,
            'likes' => $opinion->fresh()->likes
        ]);
    }
}
