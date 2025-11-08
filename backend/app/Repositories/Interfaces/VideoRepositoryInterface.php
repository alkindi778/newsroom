<?php

namespace App\Repositories\Interfaces;

interface VideoRepositoryInterface
{
    public function all(array $filters = []);
    public function find($id);
    public function findBySlug($slug);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
    public function forceDelete($id);
    
    // Publishing methods
    public function publish($id);
    public function unpublish($id);
    public function toggleFeatured($id);
    
    // Statistics methods
    public function incrementViews($id);
    public function incrementLikes($id);
    public function incrementShares($id);
    
    // Query methods
    public function getPublished(array $filters = []);
    public function getFeatured($limit = 4);
    public function getMostViewed($limit = 10);
    public function getRecent($limit = 10);
    
    // Bulk operations
    public function bulkPublish(array $ids);
    public function bulkUnpublish(array $ids);
    public function bulkDelete(array $ids);
}
