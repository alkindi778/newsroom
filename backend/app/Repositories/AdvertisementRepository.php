<?php

namespace App\Repositories;

use App\Models\Advertisement;
use App\Repositories\Interfaces\AdvertisementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class AdvertisementRepository implements AdvertisementRepositoryInterface
{
    protected $model;

    public function __construct(Advertisement $model)
    {
        $this->model = $model;
    }

    /**
     * Get all advertisements with filters
     */
    public function getAllWithFilters(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query();

        // البحث
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // فلتر النوع
        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        // فلتر الموقع
        if (!empty($filters['position'])) {
            $query->byPosition($filters['position']);
        }

        // فلتر الحالة
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->active();
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            } elseif ($filters['status'] === 'expired') {
                $query->where('end_date', '<', now());
            } elseif ($filters['status'] === 'scheduled') {
                $query->where('start_date', '>', now());
            }
        }

        // فلتر التاريخ
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // الترتيب
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // العدد لكل صفحة
        $perPage = $filters['per_page'] ?? 10;

        // Manual pagination as workaround for forPage() issue
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $total = $query->count();
        $results = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    }

    /**
     * Get advertisement by ID
     */
    public function findById(int $id): ?Advertisement
    {
        return $this->model->with(['media'])->find($id);
    }

    /**
     * Get advertisement by slug
     */
    public function findBySlug(string $slug): ?Advertisement
    {
        return $this->model->with(['media'])->where('slug', $slug)->first();
    }

    /**
     * Create new advertisement
     */
    public function create(array $data): Advertisement
    {
        return $this->model->create($data);
    }

    /**
     * Update advertisement
     */
    public function update(int $id, array $data): bool
    {
        $advertisement = $this->findById($id);
        if (!$advertisement) {
            return false;
        }

        return $advertisement->update($data);
    }

    /**
     * Delete advertisement
     */
    public function delete(int $id): bool
    {
        $advertisement = $this->findById($id);
        if (!$advertisement) {
            return false;
        }

        // حذف الصور
        $advertisement->clearMediaCollection('advertisements');
        
        return $advertisement->delete();
    }

    /**
     * Get active advertisements
     */
    public function getActive(): Collection
    {
        return $this->model->active()
            ->with(['media'])
            ->orderByPriority()
            ->get();
    }

    /**
     * Get currently active advertisements
     */
    public function getCurrentlyActive(): Collection
    {
        return Cache::remember('currently_active_ads', 300, function () {
            return $this->model->currentlyActive()
                ->with(['media'])
                ->orderByPriority()
                ->get();
        });
    }

    /**
     * Get advertisements by type
     */
    public function getByType(string $type): Collection
    {
        return $this->model->byType($type)
            ->currentlyActive()
            ->with(['media'])
            ->orderByPriority()
            ->get();
    }

    /**
     * Get advertisements by position
     */
    public function getByPosition(string $position, bool $activeOnly = true): Collection
    {
        $query = $this->model->byPosition($position);

        if ($activeOnly) {
            $query->currentlyActive();
        }

        return $query->with(['media'])
            ->orderByPriority()
            ->get();
    }

    /**
     * Get advertisements for specific section
     */
    public function getAfterSection(int $sectionId, string $page = 'home'): Collection
    {
        return $this->model->where('after_section_id', $sectionId)
            ->currentlyActive()
            ->forPage($page)
            ->with(['media'])
            ->orderByPriority()
            ->get();
    }

    /**
     * Get advertisements for specific page
     */
    public function getForPage(string $page, string $device = null): Collection
    {
        $cacheKey = "ads_page_{$page}" . ($device ? "_{$device}" : '');

        return Cache::remember($cacheKey, 300, function () use ($page, $device) {
            $query = $this->model->currentlyActive()
                ->forPage($page);

            if ($device) {
                $query->forDevice($device);
            }

            return $query->with(['media'])
                ->orderByPriority()
                ->get();
        });
    }

    /**
     * Get advertisements for specific category
     */
    public function getForCategory(int $categoryId, string $device = null): Collection
    {
        $cacheKey = "ads_category_{$categoryId}" . ($device ? "_{$device}" : '');

        return Cache::remember($cacheKey, 300, function () use ($categoryId, $device) {
            $query = $this->model->currentlyActive()
                ->forCategory($categoryId);

            if ($device) {
                $query->forDevice($device);
            }

            return $query->with(['media'])
                ->orderByPriority()
                ->get();
        });
    }

    /**
     * Toggle advertisement status
     */
    public function toggleStatus(int $id): bool
    {
        $advertisement = $this->findById($id);
        if (!$advertisement) {
            return false;
        }

        $advertisement->is_active = !$advertisement->is_active;
        $result = $advertisement->save();

        // Clear cache
        Cache::forget('currently_active_ads');

        return $result;
    }

    /**
     * Update advertisement priority
     */
    public function updatePriority(int $id, int $priority): bool
    {
        $advertisement = $this->findById($id);
        if (!$advertisement) {
            return false;
        }

        $advertisement->priority = $priority;
        $result = $advertisement->save();

        // Clear cache
        Cache::forget('currently_active_ads');

        return $result;
    }

    /**
     * Increment views
     */
    public function incrementViews(int $id): bool
    {
        $advertisement = $this->findById($id);
        if (!$advertisement) {
            return false;
        }

        $advertisement->incrementViews();
        return true;
    }

    /**
     * Increment clicks
     */
    public function incrementClicks(int $id): bool
    {
        $advertisement = $this->findById($id);
        if (!$advertisement) {
            return false;
        }

        $advertisement->incrementClicks();
        return true;
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->active()->count(),
            'inactive' => $this->model->where('is_active', false)->count(),
            'expired' => $this->model->where('end_date', '<', now())->count(),
            'scheduled' => $this->model->where('start_date', '>', now())->count(),
            'total_views' => $this->model->sum('views'),
            'total_clicks' => $this->model->sum('clicks'),
            'by_type' => $this->model->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'by_position' => $this->model->selectRaw('position, count(*) as count')
                ->groupBy('position')
                ->pluck('count', 'position')
                ->toArray(),
        ];
    }

    /**
     * Get expired advertisements
     */
    public function getExpired(): Collection
    {
        return $this->model->where('end_date', '<', now())
            ->with(['media'])
            ->orderBy('end_date', 'desc')
            ->get();
    }

    /**
     * Get scheduled advertisements
     */
    public function getScheduled(): Collection
    {
        return $this->model->where('start_date', '>', now())
            ->with(['media'])
            ->orderBy('start_date', 'asc')
            ->get();
    }

    /**
     * Search advertisements
     */
    public function search(string $query): Collection
    {
        return $this->model->search($query)
            ->with(['media'])
            ->orderByPriority()
            ->get();
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(array $ids, bool $status): int
    {
        $result = $this->model->whereIn('id', $ids)->update(['is_active' => $status]);
        
        // Clear cache
        Cache::forget('currently_active_ads');
        
        return $result;
    }

    /**
     * Bulk delete
     */
    public function bulkDelete(array $ids): int
    {
        // حذف الصور
        $advertisements = $this->model->whereIn('id', $ids)->get();
        foreach ($advertisements as $advertisement) {
            $advertisement->clearMediaCollection('advertisements');
        }

        return $this->model->whereIn('id', $ids)->delete();
    }
}
