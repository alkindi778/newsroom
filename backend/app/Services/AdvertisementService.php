<?php

namespace App\Services;

use App\Helpers\MediaHelper;
use App\Models\Advertisement;
use App\Repositories\Interfaces\AdvertisementRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Exception;

class AdvertisementService
{
    protected $advertisementRepository;

    public function __construct(AdvertisementRepositoryInterface $advertisementRepository)
    {
        $this->advertisementRepository = $advertisementRepository;
    }

    /**
     * Get all advertisements with filters
     */
    public function getAllAdvertisements(array $filters = [])
    {
        try {
            return $this->advertisementRepository->getAllWithFilters($filters);
        } catch (Exception $e) {
            Log::error('Error fetching advertisements: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get advertisement by ID
     */
    public function getAdvertisementById(int $id)
    {
        try {
            $advertisement = $this->advertisementRepository->findById($id);
            
            if (!$advertisement) {
                Log::warning("Advertisement not found: {$id}");
                return null;
            }

            return $advertisement;
        } catch (Exception $e) {
            Log::error("Error fetching advertisement {$id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get advertisement by slug
     */
    public function getAdvertisementBySlug(string $slug)
    {
        try {
            return $this->advertisementRepository->findBySlug($slug);
        } catch (Exception $e) {
            Log::error("Error fetching advertisement by slug {$slug}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create new advertisement
     */
    public function createAdvertisement(array $data)
    {
        try {
            // Remove slug from data - let the Model handle it with generateUniqueSlug
            // This ensures uniqueness is handled properly
            unset($data['slug']);
            
            // Handle target arrays
            $data['target_pages'] = $data['target_pages'] ?? null;
            $data['target_categories'] = $data['target_categories'] ?? null;
            $data['target_devices'] = $data['target_devices'] ?? null;

            // Create advertisement
            $advertisement = $this->advertisementRepository->create($data);

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
                MediaHelper::addImage($advertisement, $data['image'], MediaHelper::COLLECTION_ADVERTISEMENTS);
            }

            // Clear cache
            $this->clearCache();

            Log::info("Advertisement created successfully: {$advertisement->id}", [
                'user_id' => Auth::id(),
                'title' => $advertisement->title
            ]);

            return $advertisement;
        } catch (Exception $e) {
            Log::error('Error creating advertisement: ' . $e->getMessage(), [
                'data' => $data,
                'user_id' => Auth::id()
            ]);
            throw $e;
        }
    }

    /**
     * Update advertisement
     */
    public function updateAdvertisement(int $id, array $data)
    {
        try {
            $advertisement = $this->advertisementRepository->findById($id);
            
            if (!$advertisement) {
                Log::warning("Advertisement not found for update: {$id}");
                return null;
            }

            // Don't set slug here - let the Model handle it with generateUniqueSlug
            // The Model's updating event will handle slug generation when title changes
            
            // Handle target arrays
            if (isset($data['target_pages'])) {
                $data['target_pages'] = $data['target_pages'] ?: null;
            }
            if (isset($data['target_categories'])) {
                $data['target_categories'] = $data['target_categories'] ?: null;
            }
            if (isset($data['target_devices'])) {
                $data['target_devices'] = $data['target_devices'] ?: null;
            }

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
                MediaHelper::updateImage($advertisement, $data['image'], MediaHelper::COLLECTION_ADVERTISEMENTS);
                unset($data['image']);
            }

            // Update advertisement
            $this->advertisementRepository->update($id, $data);

            // Clear cache
            $this->clearCache();

            Log::info("Advertisement updated successfully: {$id}", [
                'user_id' => Auth::id()
            ]);

            return $this->advertisementRepository->findById($id);
        } catch (Exception $e) {
            Log::error("Error updating advertisement {$id}: " . $e->getMessage(), [
                'data' => $data,
                'user_id' => Auth::id()
            ]);
            throw $e;
        }
    }

    /**
     * Delete advertisement
     */
    public function deleteAdvertisement(int $id): bool
    {
        try {
            $advertisement = $this->advertisementRepository->findById($id);
            
            if (!$advertisement) {
                Log::warning("Advertisement not found for deletion: {$id}");
                return false;
            }

            $result = $this->advertisementRepository->delete($id);

            if ($result) {
                // Clear cache
                $this->clearCache();

                Log::info("Advertisement deleted successfully: {$id}", [
                    'user_id' => Auth::id(),
                    'title' => $advertisement->title
                ]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error("Error deleting advertisement {$id}: " . $e->getMessage(), [
                'user_id' => Auth::id()
            ]);
            throw $e;
        }
    }

    /**
     * Toggle advertisement status
     */
    public function toggleStatus(int $id): bool
    {
        try {
            $advertisement = $this->advertisementRepository->findById($id);
            
            if (!$advertisement) {
                Log::warning("Advertisement not found for status toggle: {$id}");
                return false;
            }

            $result = $this->advertisementRepository->toggleStatus($id);

            if ($result) {
                // Clear cache
                $this->clearCache();

                Log::info("Advertisement status toggled: {$id}", [
                    'user_id' => Auth::id(),
                    'new_status' => !$advertisement->is_active
                ]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error("Error toggling advertisement status {$id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update priority
     */
    public function updatePriority(int $id, int $priority): bool
    {
        try {
            $result = $this->advertisementRepository->updatePriority($id, $priority);

            if ($result) {
                // Clear cache
                $this->clearCache();

                Log::info("Advertisement priority updated: {$id}", [
                    'user_id' => Auth::id(),
                    'priority' => $priority
                ]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error("Error updating advertisement priority {$id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get advertisements for specific page
     */
    public function getForPage(string $page, string $device = null)
    {
        try {
            return $this->advertisementRepository->getForPage($page, $device);
        } catch (Exception $e) {
            Log::error("Error fetching advertisements for page {$page}: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get advertisements by position
     */
    public function getByPosition(string $position, bool $activeOnly = true)
    {
        try {
            return $this->advertisementRepository->getByPosition($position, $activeOnly);
        } catch (Exception $e) {
            Log::error("Error fetching advertisements for position {$position}: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get advertisements after specific section
     */
    public function getAfterSection(int $sectionId, string $page = 'home')
    {
        try {
            return $this->advertisementRepository->getAfterSection($sectionId, $page);
        } catch (Exception $e) {
            Log::error("Error fetching advertisements for section {$sectionId}: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Track view
     */
    public function trackView(int $id): bool
    {
        try {
            return $this->advertisementRepository->incrementViews($id);
        } catch (Exception $e) {
            Log::error("Error tracking view for advertisement {$id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Track click
     */
    public function trackClick(int $id): bool
    {
        try {
            return $this->advertisementRepository->incrementClicks($id);
        } catch (Exception $e) {
            Log::error("Error tracking click for advertisement {$id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        try {
            return $this->advertisementRepository->getStatistics();
        } catch (Exception $e) {
            Log::error('Error fetching advertisement statistics: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(string $action, array $ids): int
    {
        try {
            $result = 0;

            switch ($action) {
                case 'activate':
                    $result = $this->advertisementRepository->bulkUpdateStatus($ids, true);
                    break;

                case 'deactivate':
                    $result = $this->advertisementRepository->bulkUpdateStatus($ids, false);
                    break;

                case 'delete':
                    $result = $this->advertisementRepository->bulkDelete($ids);
                    break;
            }

            if ($result > 0) {
                // Clear cache
                $this->clearCache();

                Log::info("Bulk action {$action} completed", [
                    'user_id' => Auth::id(),
                    'count' => $result
                ]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error("Error performing bulk action {$action}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get expired advertisements
     */
    public function getExpired()
    {
        try {
            return $this->advertisementRepository->getExpired();
        } catch (Exception $e) {
            Log::error('Error fetching expired advertisements: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get scheduled advertisements
     */
    public function getScheduled()
    {
        try {
            return $this->advertisementRepository->getScheduled();
        } catch (Exception $e) {
            Log::error('Error fetching scheduled advertisements: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Clear cache
     */
    protected function clearCache(): void
    {
        Cache::forget('currently_active_ads');
        
        // Clear page-specific caches
        $pages = ['home', 'articles', 'categories', 'article'];
        foreach ($pages as $page) {
            Cache::forget("ads_page_{$page}");
            Cache::forget("ads_page_{$page}_desktop");
            Cache::forget("ads_page_{$page}_mobile");
            Cache::forget("ads_page_{$page}_tablet");
        }
    }
}
