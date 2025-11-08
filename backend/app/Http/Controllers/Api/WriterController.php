<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WriterResource;
use App\Http\Resources\OpinionResource;
use App\Services\WriterService;
use App\Services\OpinionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WriterController extends Controller
{
    protected WriterService $writerService;
    protected OpinionService $opinionService;

    public function __construct(WriterService $writerService, OpinionService $opinionService)
    {
        $this->writerService = $writerService;
        $this->opinionService = $opinionService;
    }

    // GET /api/v1/writers
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status'); // active|inactive
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $perPage = (int) $request->query('per_page', 10);

        $writers = $this->writerService->getAllWithFilters($search, $status, $sortBy, $sortDir, $perPage);
        return WriterResource::collection($writers);
    }

    // GET /api/v1/writers/{slugOrId}
    public function show($slugOrId): JsonResponse|WriterResource
    {
        // Check if it's a numeric ID or a slug
        if (is_numeric($slugOrId)) {
            $writer = $this->writerService->getById($slugOrId);
        } else {
            $writer = $this->writerService->getBySlug($slugOrId);
        }
        
        if (!$writer) {
            return response()->json([
                'message' => 'الكاتب غير موجود',
            ], 404);
        }
        return new WriterResource($writer);
    }

    // GET /api/v1/writers/{slugOrId}/opinions
    public function opinions($slugOrId, Request $request)
    {
        // Check if it's a numeric ID or a slug
        if (is_numeric($slugOrId)) {
            $writer = $this->writerService->getById($slugOrId);
        } else {
            $writer = $this->writerService->getBySlug($slugOrId);
        }
        
        if (!$writer) {
            return response()->json([
                'message' => 'الكاتب غير موجود',
            ], 404);
        }

        $search = $request->query('search');
        $status = $request->query('status'); // published|draft
        $featured = $request->boolean('featured', null);
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $perPage = (int) $request->query('per_page', 10);

        $opinions = $this->opinionService->getAllWithFilters(
            $search,
            $status,
            $writer->id,
            $featured,
            $sortBy,
            $sortDir,
            $perPage
        );

        return OpinionResource::collection($opinions);
    }
}
