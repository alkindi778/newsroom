<?php

namespace App\Services;

use App\Models\HomepageSection;
use App\Repositories\Interfaces\HomepageSectionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HomepageSectionService
{
    protected $repository;

    public function __construct(HomepageSectionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all sections
     */
    public function getAllSections(): Collection
    {
        return $this->repository->getAllOrdered();
    }

    /**
     * Get active sections
     */
    public function getActiveSections(): Collection
    {
        return $this->repository->getActive();
    }

    /**
     * Get section by ID
     */
    public function getSectionById(int $id): ?HomepageSection
    {
        return $this->repository->findById($id);
    }

    /**
     * Get section by slug
     */
    public function getSectionBySlug(string $slug): ?HomepageSection
    {
        return $this->repository->findBySlug($slug);
    }

    /**
     * Create new section
     */
    public function createSection(array $data): ?HomepageSection
    {
        try {
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Ensure slug is unique
            $originalSlug = $data['slug'];
            $counter = 1;
            while ($this->repository->slugExists($data['slug'])) {
                $data['slug'] = $originalSlug . '-' . $counter++;
            }

            // Handle is_active checkbox
            $data['is_active'] = $data['is_active'] ?? false;

            // Parse settings if it's JSON string
            if (isset($data['settings']) && is_string($data['settings'])) {
                $data['settings'] = json_decode($data['settings'], true);
            }

            return $this->repository->create($data);

        } catch (\Exception $e) {
            Log::error('Error creating homepage section: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update section
     */
    public function updateSection(HomepageSection $section, array $data): bool
    {
        try {
            // Ensure slug is unique (excluding current section)
            if (isset($data['slug']) && $data['slug'] !== $section->slug) {
                $originalSlug = $data['slug'];
                $counter = 1;
                while ($this->repository->slugExists($data['slug'], $section->id)) {
                    $data['slug'] = $originalSlug . '-' . $counter++;
                }
            }

            // Handle is_active checkbox
            $data['is_active'] = $data['is_active'] ?? false;

            // Parse settings if it's JSON string
            if (isset($data['settings']) && is_string($data['settings'])) {
                $data['settings'] = json_decode($data['settings'], true);
            }

            return $this->repository->update($section, $data);

        } catch (\Exception $e) {
            Log::error('Error updating homepage section: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete section
     */
    public function deleteSection(HomepageSection $section): bool
    {
        try {
            return $this->repository->delete($section);
        } catch (\Exception $e) {
            Log::error('Error deleting homepage section: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle section status
     */
    public function toggleSectionStatus(HomepageSection $section): bool
    {
        try {
            return $this->repository->toggleStatus($section);
        } catch (\Exception $e) {
            Log::error('Error toggling section status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update sections order
     */
    public function updateSectionsOrder(array $sections): bool
    {
        try {
            return $this->repository->updateOrder($sections);
        } catch (\Exception $e) {
            Log::error('Error updating sections order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get sections by type
     */
    public function getSectionsByType(string $type): Collection
    {
        return $this->repository->getByType($type);
    }

    /**
     * Get available section types
     */
    public function getAvailableTypes(): array
    {
        return HomepageSection::getAvailableTypes();
    }
}
