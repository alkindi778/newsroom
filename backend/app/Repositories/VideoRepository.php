<?php

namespace App\Repositories;

use App\Models\Video;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use Illuminate\Support\Facades\DB;

class VideoRepository implements VideoRepositoryInterface
{
    protected $model;

    public function __construct(Video $model)
    {
        $this->model = $model;
    }

    public function all(array $filters = [])
    {
        $query = $this->model->with('user')->orderBy('created_at', 'desc');

        // Search filter
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Status filter
        if (isset($filters['status'])) {
            if ($filters['status'] === 'published') {
                $query->where('is_published', true);
            } elseif ($filters['status'] === 'draft') {
                $query->where('is_published', false);
            }
        }

        // Featured filter
        if (isset($filters['featured'])) {
            $query->where('is_featured', $filters['featured']);
        }

        // Video type filter
        if (isset($filters['video_type'])) {
            $query->where('video_type', $filters['video_type']);
        }

        // Sort
        if (isset($filters['sort'])) {
            switch ($filters['sort']) {
                case 'views':
                    $query->orderBy('views', 'desc');
                    break;
                case 'likes':
                    $query->orderBy('likes', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    public function find($id)
    {
        return $this->model->with('user')->findOrFail($id);
    }

    public function findBySlug($slug)
    {
        return $this->model->with('user')->where('slug', $slug)->firstOrFail();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $video = $this->find($id);
        $video->update($data);
        return $video->fresh();
    }

    public function delete($id)
    {
        $video = $this->find($id);
        return $video->delete();
    }

    public function restore($id)
    {
        $video = $this->model->withTrashed()->findOrFail($id);
        return $video->restore();
    }

    public function forceDelete($id)
    {
        $video = $this->model->withTrashed()->findOrFail($id);
        return $video->forceDelete();
    }

    public function publish($id)
    {
        $video = $this->find($id);
        $video->update([
            'is_published' => true,
            'published_at' => $video->published_at ?? now(),
        ]);
        return $video->fresh();
    }

    public function unpublish($id)
    {
        $video = $this->find($id);
        $video->update(['is_published' => false]);
        return $video->fresh();
    }

    public function toggleFeatured($id)
    {
        $video = $this->find($id);
        $video->update(['is_featured' => !$video->is_featured]);
        return $video->fresh();
    }

    public function incrementViews($id)
    {
        return $this->model->where('id', $id)->increment('views');
    }

    public function incrementLikes($id)
    {
        return $this->model->where('id', $id)->increment('likes');
    }

    public function incrementShares($id)
    {
        return $this->model->where('id', $id)->increment('shares');
    }

    public function getPublished(array $filters = [])
    {
        $query = $this->model->published()->with('user');

        // Search filter
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Video type filter
        if (isset($filters['video_type'])) {
            $query->where('video_type', $filters['video_type']);
        }

        // Sort
        $sort = $filters['sort'] ?? 'recent';
        switch ($sort) {
            case 'views':
                $query->orderBy('views', 'desc');
                break;
            case 'likes':
                $query->orderBy('likes', 'desc');
                break;
            default:
                $query->orderBy('published_at', 'desc');
        }

        $perPage = $filters['per_page'] ?? 12;
        return $query->paginate($perPage);
    }

    public function getFeatured($limit = 4)
    {
        return $this->model->published()
            ->featured()
            ->recent()
            ->limit($limit)
            ->get();
    }

    public function getMostViewed($limit = 10)
    {
        return $this->model->published()
            ->mostViewed()
            ->limit($limit)
            ->get();
    }

    public function getRecent($limit = 10)
    {
        return $this->model->published()
            ->recent()
            ->limit($limit)
            ->get();
    }

    public function bulkPublish(array $ids)
    {
        return $this->model->whereIn('id', $ids)->update([
            'is_published' => true,
            'published_at' => DB::raw('COALESCE(published_at, NOW())'),
        ]);
    }

    public function bulkUnpublish(array $ids)
    {
        return $this->model->whereIn('id', $ids)->update(['is_published' => false]);
    }

    public function bulkDelete(array $ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    public function getSectionTitle()
    {
        $firstVideo = $this->model->first();
        return $firstVideo ? $firstVideo->section_title : 'فيديو العربية';
    }

    public function updateSectionTitle(string $title)
    {
        // Update all videos with the new section title using query builder
        return DB::table('videos')->update(['section_title' => $title]);
    }
}
