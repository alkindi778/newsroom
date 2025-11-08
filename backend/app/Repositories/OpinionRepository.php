<?php

namespace App\Repositories;

use App\Models\Opinion;
use App\Repositories\Interfaces\OpinionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OpinionRepository implements OpinionRepositoryInterface
{
    protected $model;

    public function __construct(Opinion $model)
    {
        $this->model = $model;
    }

    public function getAllWithFilters($search = null, $status = null, $writerId = null, $featured = null, $sortBy = 'created_at', $sortDirection = 'desc', $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->with('writer');

        // البحث
        if ($search) {
            $query->search($search);
        }

        // فلتر الحالة
        if ($status === 'published') {
            $query->published();
        } elseif ($status === 'draft') {
            $query->draft();
        }

        // فلتر الكاتب
        if ($writerId) {
            $query->byWriter($writerId);
        }

        // فلتر التمييز
        if ($featured === '1') {
            $query->featured();
        }

        // الترتيب
        $allowedSorts = ['title', 'created_at', 'published_at', 'views', 'likes'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    public function getById($id): ?Opinion
    {
        return $this->model->with('writer')->find($id);
    }

    public function getBySlug($slug): ?Opinion
    {
        return $this->model->with('writer')->where('slug', $slug)->first();
    }

    public function create(array $data): Opinion
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): bool
    {
        $opinion = $this->model->find($id);
        
        if (!$opinion) {
            return false;
        }

        return $opinion->update($data);
    }

    public function delete($id): bool
    {
        $opinion = $this->model->find($id);
        
        if (!$opinion) {
            return false;
        }

        return $opinion->delete();
    }

    public function restore($id): bool
    {
        $opinion = $this->model->onlyTrashed()->find($id);
        
        if (!$opinion) {
            return false;
        }

        return $opinion->restore();
    }

    public function forceDelete($id): bool
    {
        $opinion = $this->model->withTrashed()->find($id);
        
        if (!$opinion) {
            return false;
        }

        return $opinion->forceDelete();
    }

    public function toggleStatus($id): bool
    {
        $opinion = $this->model->find($id);
        
        if (!$opinion) {
            return false;
        }

        $newStatus = !$opinion->is_published;
        
        return $opinion->update([
            'is_published' => $newStatus,
            'published_at' => $newStatus ? ($opinion->published_at ?: now()) : null
        ]);
    }

    public function toggleFeatured($id): bool
    {
        $opinion = $this->model->find($id);
        
        if (!$opinion) {
            return false;
        }

        return $opinion->update([
            'is_featured' => !$opinion->is_featured
        ]);
    }

    public function getPublished($limit = null): Collection
    {
        $query = $this->model->published()->with('writer')->byPublishDate();
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get();
    }

    public function getFeatured($limit = null): Collection
    {
        $query = $this->model->featured()->published()->with('writer')->byPublishDate();
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get();
    }

    public function getByWriter($writerId, $limit = null): Collection
    {
        $query = $this->model->byWriter($writerId)->latest();
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get();
    }

    public function getRecentPublished($limit = 10): Collection
    {
        return $this->model->published()
                          ->with('writer')
                          ->byPublishDate()
                          ->limit($limit)
                          ->get();
    }

    public function getPopular($limit = 10): Collection
    {
        return $this->model->published()
                          ->with('writer')
                          ->popular()
                          ->limit($limit)
                          ->get();
    }

    public function getStatistics(): array
    {
        return [
            'total_opinions' => $this->model->count(),
            'published_opinions' => $this->model->where('is_published', true)->count(),
            'draft_opinions' => $this->model->where('is_published', false)->count(),
            'featured_opinions' => $this->model->where('is_featured', true)->count(),
            'total_views' => $this->model->sum('views'),
            'total_likes' => $this->model->sum('likes'),
            'total_shares' => $this->model->sum('shares'),
            'trashed_opinions' => $this->model->onlyTrashed()->count(),
        ];
    }

    public function search($query): Collection
    {
        return $this->model->search($query)->with('writer')->get();
    }

    public function incrementViews($id): bool
    {
        $opinion = $this->model->find($id);
        
        if (!$opinion) {
            return false;
        }

        $opinion->incrementViews();
        
        return true;
    }

    public function incrementLikes($id): bool
    {
        $opinion = $this->model->find($id);
        
        if (!$opinion) {
            return false;
        }

        $opinion->incrementLikes();
        
        return true;
    }

    public function incrementShares($id): bool
    {
        $opinion = $this->model->find($id);
        
        if (!$opinion) {
            return false;
        }

        $opinion->incrementShares();
        
        return true;
    }

    public function getRelated($opinionId, $limit = 5): Collection
    {
        $opinion = $this->model->find($opinionId);
        
        if (!$opinion) {
            return collect();
        }

        return $this->model->published()
                          ->where('id', '!=', $opinionId)
                          ->where(function ($query) use ($opinion) {
                              // نفس الكاتب
                              $query->where('writer_id', $opinion->writer_id)
                                    // أو نفس الكلمات المفتاحية
                                    ->orWhere('keywords', 'LIKE', '%' . $opinion->keywords . '%');
                          })
                          ->with('writer')
                          ->byPublishDate()
                          ->limit($limit)
                          ->get();
    }

    public function getTrashed(): Collection
    {
        return $this->model->onlyTrashed()->with('writer')->latest()->get();
    }

    /**
     * البحث المتقدم
     */
    public function searchAdvanced($search = null, $writerId = null, $tags = null, $dateFrom = null, $dateTo = null): Collection
    {
        $query = $this->model->published()->with('writer');

        if ($search) {
            $query->search($search);
        }

        if ($writerId) {
            $query->byWriter($writerId);
        }

        if ($tags) {
            $tagsArray = is_array($tags) ? $tags : explode(',', $tags);
            $query->where(function ($q) use ($tagsArray) {
                foreach ($tagsArray as $tag) {
                    $q->orWhereJsonContains('tags', trim($tag));
                }
            });
        }

        if ($dateFrom) {
            $query->whereDate('published_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('published_at', '<=', $dateTo);
        }

        return $query->byPublishDate()->get();
    }

    /**
     * الحصول على أكثر المقالات مشاهدة
     */
    public function getMostViewed($limit = 10, $days = null): Collection
    {
        $query = $this->model->published()->with('writer')->orderByDesc('views');

        if ($days) {
            $query->where('published_at', '>=', now()->subDays($days));
        }

        return $query->limit($limit)->get();
    }

    /**
     * الحصول على الإحصائيات حسب الكاتب
     */
    public function getStatisticsByWriter($writerId): array
    {
        $query = $this->model->byWriter($writerId);

        return [
            'total' => $query->count(),
            'published' => $query->where('is_published', true)->count(),
            'draft' => $query->where('is_published', false)->count(),
            'featured' => $query->where('is_featured', true)->count(),
            'total_views' => $query->sum('views'),
            'total_likes' => $query->sum('likes'),
            'total_shares' => $query->sum('shares'),
        ];
    }
}
