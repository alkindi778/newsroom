<?php

namespace App\Repositories\Interfaces;

interface WriterRepositoryInterface
{
    public function getAllWithFilters($search = null, $status = null, $sortBy = 'created_at', $sortDirection = 'desc', $perPage = 10);
    
    public function getById($id);
    
    public function getBySlug($slug);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($id);
    
    public function toggleStatus($id);
    
    public function getActive();
    
    public function getBySpecialization($specialization);
    
    public function getTopWriters($limit = 10);
    
    public function getStatistics();
    
    public function search($query);
    
    public function updateOpinionsCount($writerId);
    
    public function getWritersWithOpinionsCount();
}
