<?php

namespace App\Repositories\Interfaces;

interface OpinionRepositoryInterface
{
    public function getAllWithFilters($search = null, $status = null, $writerId = null, $featured = null, $sortBy = 'created_at', $sortDirection = 'desc', $perPage = 10);
    
    public function getById($id);
    
    public function getBySlug($slug);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($id);
    
    public function restore($id);
    
    public function forceDelete($id);
    
    public function toggleStatus($id);
    
    public function toggleFeatured($id);
    
    public function getPublished($limit = null);
    
    public function getFeatured($limit = null);
    
    public function getByWriter($writerId, $limit = null);
    
    public function getRecentPublished($limit = 10);
    
    public function getPopular($limit = 10);
    
    public function getStatistics();
    
    public function search($query);
    
    public function incrementViews($id);
    
    public function incrementLikes($id);
    
    public function incrementShares($id);
    
    public function getRelated($opinionId, $limit = 5);
    
    public function getTrashed();
}
