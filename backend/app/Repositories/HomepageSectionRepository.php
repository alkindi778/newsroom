<?php

namespace App\Repositories;

use App\Models\HomepageSection;
use App\Repositories\Interfaces\HomepageSectionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class HomepageSectionRepository implements HomepageSectionRepositoryInterface
{
    /**
     * Get all sections ordered
     */
    public function getAllOrdered(): Collection
    {
        return HomepageSection::with('category')
            ->ordered()
            ->get();
    }

    /**
     * Get active sections only
     */
    public function getActive(): Collection
    {
        return HomepageSection::with('category')
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Get section by ID
     */
    public function findById(int $id): ?HomepageSection
    {
        return HomepageSection::with('category')->find($id);
    }

    /**
     * Get section by slug
     */
    public function findBySlug(string $slug): ?HomepageSection
    {
        return HomepageSection::with('category')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Create new section
     */
    public function create(array $data): HomepageSection
    {
        return HomepageSection::create($data);
    }

    /**
     * Update section
     */
    public function update(HomepageSection $section, array $data): bool
    {
        return $section->update($data);
    }

    /**
     * Delete section
     */
    public function delete(HomepageSection $section): bool
    {
        try {
            return $section->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting homepage section: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle section status
     */
    public function toggleStatus(HomepageSection $section): bool
    {
        return $section->update([
            'is_active' => !$section->is_active
        ]);
    }

    /**
     * Update sections order
     */
    public function updateOrder(array $sections): bool
    {
        try {
            foreach ($sections as $sectionData) {
                HomepageSection::where('id', $sectionData['id'])
                    ->update(['order' => $sectionData['order']]);
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Error updating sections order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get sections by type
     */
    public function getByType(string $type): Collection
    {
        return HomepageSection::with('category')
            ->where('type', $type)
            ->ordered()
            ->get();
    }

    /**
     * Check if slug exists
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = HomepageSection::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}
