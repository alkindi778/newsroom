<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Get all users with pagination
     */
    public function getAllWithPagination(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['roles', 'permissions'])
                          ->withCount('articles')
                          ->latest()
                          ->paginate($perPage);
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?User
    {
        return $this->model->with(['roles', 'permissions', 'articles'])
                          ->find($id);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Create new user
     */
    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * Update user
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Delete user
     */
    public function delete(User $user): bool
    {
        // Don't allow deletion if user has articles
        if ($user->articles()->count() > 0) {
            return false;
        }
        
        return $user->delete();
    }

    /**
     * Get active users
     */
    public function getActive(): Collection
    {
        return $this->model->where('status', true)
                          ->with(['roles'])
                          ->get();
    }

    /**
     * Get inactive users  
     */
    public function getInactive(): Collection
    {
        return $this->model->where('status', false)
                          ->with(['roles'])
                          ->get();
    }

    /**
     * Search users
     */
    public function search(string $query): Collection
    {
        return $this->model->where(function ($q) use ($query) {
                                $q->where('name', 'LIKE', "%{$query}%")
                                  ->orWhere('email', 'LIKE', "%{$query}%")
                                  ->orWhere('phone', 'LIKE', "%{$query}%");
                            })
                            ->with(['roles'])
                            ->get();
    }

    /**
     * Get users with specific role
     */
    public function getUsersByRole(string $role): Collection
    {
        return $this->model->role($role)
                          ->with(['roles', 'permissions'])
                          ->get();
    }

    /**
     * Get users with specific permission
     */
    public function getUsersWithPermission(string $permission): Collection
    {
        return $this->model->permission($permission)
                          ->with(['roles', 'permissions'])
                          ->get();
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user): bool
    {
        return $user->update(['status' => !$user->status]);
    }

    /**
     * Get user statistics
     */
    public function getStatistics(): array
    {
        $total = $this->model->count();
        $active = $this->model->where('status', true)->count();
        $inactive = $this->model->where('status', false)->count();
        $recentlyRegistered = $this->model->where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $withArticles = $this->model->has('articles')->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'recently_registered' => $recentlyRegistered,
            'with_articles' => $withArticles,
            'percentage_active' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get recently registered users
     */
    public function getRecentUsers(int $limit = 10): Collection
    {
        return $this->model->with(['roles'])
                          ->latest('created_at')
                          ->limit($limit)
                          ->get();
    }

    /**
     * Get users with most articles
     */
    public function getUsersWithMostArticles(int $limit = 10): Collection
    {
        return $this->model->withCount('articles')
                          ->having('articles_count', '>', 0)
                          ->orderBy('articles_count', 'desc')
                          ->limit($limit)
                          ->get();
    }

    /**
     * Update last login time
     */
    public function updateLastLogin(User $user): bool
    {
        return $user->update(['last_login_at' => Carbon::now()]);
    }

    /**
     * Get online users (recently active)
     */
    public function getOnlineUsers(int $minutesThreshold = 15): Collection
    {
        return $this->model->where('last_login_at', '>=', Carbon::now()->subMinutes($minutesThreshold))
                          ->with(['roles'])
                          ->get();
    }

    /**
     * Bulk update users status
     */
    public function bulkUpdateStatus(array $userIds, bool $status): int
    {
        return $this->model->whereIn('id', $userIds)
                          ->update(['status' => $status]);
    }

    /**
     * Get users by filters
     */
    public function getByFilters(Request $request): LengthAwarePaginator
    {
        $query = $this->model->with(['roles', 'permissions'])->withCount('articles');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', (bool) $request->status);
        }

        // Filter by role
        if ($request->has('role') && $request->role !== '') {
            $query->role($request->role);
        }

        // Search by name or email
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($request->get('per_page', 10));
    }

    /**
     * Get users count by role
     */
    public function getCountByRole(): array
    {
        return DB::table('model_has_roles')
                 ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                 ->select('roles.name', DB::raw('count(*) as count'))
                 ->groupBy('roles.name')
                 ->pluck('count', 'name')
                 ->toArray();
    }
}
