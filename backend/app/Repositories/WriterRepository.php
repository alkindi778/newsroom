<?php

namespace App\Repositories;

use App\Models\Writer;
use App\Repositories\Interfaces\WriterRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class WriterRepository implements WriterRepositoryInterface
{
    protected $model;

    public function __construct(Writer $model)
    {
        $this->model = $model;
    }

    public function getAllWithFilters($search = null, $status = null, $sortBy = 'created_at', $sortDirection = 'desc', $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->withCount('opinions');

        // البحث
        if ($search) {
            $query->search($search);
        }

        // فلتر الحالة
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        // الترتيب
        $allowedSorts = ['name', 'created_at', 'opinions_count', 'last_activity'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    public function getById($id): ?Writer
    {
        return $this->model->with(['opinions' => function ($query) {
            $query->latest()->take(10);
        }])->find($id);
    }

    public function getBySlug($slug): ?Writer
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function create(array $data): Writer
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): bool
    {
        $writer = $this->model->find($id);
        
        if (!$writer) {
            return false;
        }

        return $writer->update($data);
    }

    public function delete($id): bool
    {
        $writer = $this->model->find($id);
        
        if (!$writer) {
            return false;
        }

        return $writer->delete();
    }

    public function toggleStatus($id): bool
    {
        $writer = $this->model->find($id);
        
        if (!$writer) {
            return false;
        }

        return $writer->update([
            'is_active' => !$writer->is_active
        ]);
    }

    public function getActive(): Collection
    {
        return $this->model->active()->orderBy('name')->get();
    }

    public function getBySpecialization($specialization): Collection
    {
        return $this->model->where('specialization', $specialization)
                          ->active()
                          ->orderBy('name')
                          ->get();
    }

    public function getTopWriters($limit = 10): Collection
    {
        return $this->model->withCount('opinions')
                          ->active()
                          ->orderByDesc('opinions_count')
                          ->limit($limit)
                          ->get();
    }

    public function getStatistics(): array
    {
        return [
            'total_writers' => $this->model->count(),
            'active_writers' => $this->model->where('is_active', true)->count(),
            'inactive_writers' => $this->model->where('is_active', false)->count(),
            'writers_with_opinions' => $this->model->whereHas('opinions')->count(),
            'total_opinions' => $this->model->withSum('opinions', 'id')->sum('opinions_sum_id') ?? 0,
        ];
    }

    public function search($query): Collection
    {
        return $this->model->search($query)->get();
    }

    public function updateOpinionsCount($writerId): bool
    {
        $writer = $this->model->find($writerId);
        
        if (!$writer) {
            return false;
        }

        $writer->updateOpinionsCount();
        
        return true;
    }

    public function getWritersWithOpinionsCount(): Collection
    {
        return $this->model->withCount(['opinions', 'opinions as published_opinions_count' => function ($query) {
            $query->where('is_published', true);
        }])->orderBy('name')->get();
    }

    /**
     * البحث بناء على عدة معايير
     */
    public function searchAdvanced($search = null, $specialization = null, $hasOpinions = null): Collection
    {
        $query = $this->model->active();

        if ($search) {
            $query->search($search);
        }

        if ($specialization) {
            $query->where('specialization', $specialization);
        }

        if ($hasOpinions === true) {
            $query->whereHas('opinions');
        } elseif ($hasOpinions === false) {
            $query->whereDoesntHave('opinions');
        }

        return $query->withCount('opinions')->orderBy('name')->get();
    }

    /**
     * الحصول على قائمة التخصصات المتاحة
     */
    public function getSpecializations(): array
    {
        return $this->model->active()
                          ->whereNotNull('specialization')
                          ->distinct()
                          ->pluck('specialization')
                          ->toArray();
    }

    /**
     * الحصول على الكُتاب الأكثر نشاطاً
     */
    public function getMostActiveWriters($limit = 5): Collection
    {
        return $this->model->active()
                          ->whereNotNull('last_activity')
                          ->orderByDesc('last_activity')
                          ->limit($limit)
                          ->get();
    }
}
