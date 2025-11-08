<?php

namespace App\Repositories\Interfaces;

use App\Models\HomepageSection;
use Illuminate\Database\Eloquent\Collection;

interface HomepageSectionRepositoryInterface
{
    /**
     * Get all sections ordered
     */
    public function getAllOrdered(): Collection;

    /**
     * Get active sections only
     */
    public function getActive(): Collection;

    /**
     * Get section by ID
     */
    public function findById(int $id): ?HomepageSection;

    /**
     * Get section by slug
     */
    public function findBySlug(string $slug): ?HomepageSection;

    /**
     * Create new section
     */
    public function create(array $data): HomepageSection;

    /**
     * Update section
     */
    public function update(HomepageSection $section, array $data): bool;

    /**
     * Delete section
     */
    public function delete(HomepageSection $section): bool;

    /**
     * Toggle section status
     */
    public function toggleStatus(HomepageSection $section): bool;

    /**
     * Update sections order
     */
    public function updateOrder(array $sections): bool;

    /**
     * Get sections by type
     */
    public function getByType(string $type): Collection;

    /**
     * Check if slug exists
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool;
}
