<?php

namespace App\Repositories\Interfaces;

interface NewspaperIssueRepositoryInterface
{
    public function all(array $filters = []);
    public function find($id);
    public function findBySlug($slug);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    
    // Query methods
    public function getPublished(array $filters = []);
    public function getFeatured($limit = 6);
    public function getMostViewed($limit = 10);
    public function getRecent($limit = 10);
    
    // Statistics methods
    public function incrementViews($id);
    public function incrementDownloads($id);
    
    // Toggle methods
    public function toggleFeatured($id);
}
