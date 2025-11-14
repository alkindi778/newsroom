<?php

namespace App\Repositories;

use App\Models\NewspaperIssue;
use App\Repositories\Interfaces\NewspaperIssueRepositoryInterface;

class NewspaperIssueRepository implements NewspaperIssueRepositoryInterface
{
    protected $model;

    public function __construct(NewspaperIssue $model)
    {
        $this->model = $model;
    }

    public function all(array $filters = [])
    {
        $query = $this->model->with('user')->orderBy('publication_date', 'desc');

        // Search filter
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('newspaper_name', 'like', '%' . $filters['search'] . '%')
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

        // Sort
        if (isset($filters['sort'])) {
            switch ($filters['sort']) {
                case 'views':
                    $query->orderBy('views', 'desc');
                    break;
                case 'downloads':
                    $query->orderBy('downloads', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('publication_date', 'asc');
                    break;
                default:
                    $query->orderBy('publication_date', 'desc');
            }
        }

        $perPage = $filters['per_page'] ?? 12;
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
        $issue = $this->find($id);
        $issue->update($data);
        return $issue->fresh();
    }

    public function delete($id)
    {
        $issue = $this->find($id);
        return $issue->delete();
    }

    public function getPublished(array $filters = [])
    {
        $query = $this->model->published()->with('user');

        // Search filter
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('newspaper_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Sort
        $sort = $filters['sort'] ?? 'recent';
        switch ($sort) {
            case 'views':
                $query->orderBy('views', 'desc');
                break;
            case 'downloads':
                $query->orderBy('downloads', 'desc');
                break;
            default:
                $query->orderBy('publication_date', 'desc');
        }

        $perPage = $filters['per_page'] ?? 12;
        return $query->paginate($perPage);
    }

    public function getFeatured($limit = 6)
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

    public function incrementViews($id)
    {
        return $this->model->where('id', $id)->increment('views');
    }

    public function incrementDownloads($id)
    {
        return $this->model->where('id', $id)->increment('downloads');
    }

    public function toggleFeatured($id)
    {
        $issue = $this->find($id);
        $issue->update(['is_featured' => !$issue->is_featured]);
        return $issue->fresh();
    }
}
