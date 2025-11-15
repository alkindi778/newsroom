<?php

namespace App\Services;

use App\Repositories\Interfaces\NewspaperIssueRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class NewspaperIssueService
{
    protected $repository;

    public function __construct(NewspaperIssueRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all newspaper issues with filters
     */
    public function getAllIssues(array $filters = [])
    {
        try {
            return $this->repository->all($filters);
        } catch (\Exception $e) {
            Log::error('Error fetching newspaper issues: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get published newspaper issues
     */
    public function getPublishedIssues(array $filters = [])
    {
        try {
            return $this->repository->getPublished($filters);
        } catch (\Exception $e) {
            Log::error('Error fetching published newspaper issues: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get featured newspaper issues
     */
    public function getFeaturedIssues($limit = 6)
    {
        try {
            return $this->repository->getFeatured($limit);
        } catch (\Exception $e) {
            Log::error('Error fetching featured newspaper issues: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get newspaper issue by ID
     */
    public function getIssueById($id)
    {
        try {
            return $this->repository->find($id);
        } catch (\Exception $e) {
            Log::error('Error fetching newspaper issue: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get newspaper issue by slug
     */
    public function getIssueBySlug($slug)
    {
        try {
            $issue = $this->repository->findBySlug($slug);
            // Increment views
            $this->repository->incrementViews($issue->id);
            return $issue->fresh();
        } catch (\Exception $e) {
            Log::error('Error fetching newspaper issue by slug: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create new newspaper issue
     */
    public function createIssue(array $data)
    {
        try {
            // Handle cover image upload
            if (isset($data['cover_image'])) {
                $data['cover_image'] = $this->handleCoverImageUpload($data['cover_image']);
            }

            $issue = $this->repository->create($data);
            
            // النشر على السوشيال ميديا إذا كان منشوراً
            if ($issue && $issue->is_published) {
                \App\Jobs\ShareToAllPlatforms::dispatch('newspaper_issue', $issue->id);
                Log::info('Newspaper issue published to social media', ['issue_id' => $issue->id]);
            }
            
            return $issue;
        } catch (\Exception $e) {
            Log::error('Error creating newspaper issue: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update newspaper issue
     */
    public function updateIssue($id, array $data)
    {
        try {
            $issue = $this->repository->find($id);
            $wasUnpublished = !$issue->is_published;

            // Handle cover image upload
            if (isset($data['cover_image']) && is_file($data['cover_image'])) {
                // Delete old cover image
                if ($issue->cover_image) {
                    Storage::disk('public')->delete($issue->cover_image);
                }
                $data['cover_image'] = $this->handleCoverImageUpload($data['cover_image']);
            }

            $result = $this->repository->update($id, $data);
            
            // النشر على السوشيال ميديا عند النشر لأول مرة
            if ($result) {
                $issue->refresh();
                if ($wasUnpublished && $issue->is_published) {
                    \App\Jobs\ShareToAllPlatforms::dispatch('newspaper_issue', $issue->id);
                    Log::info('Newspaper issue published to social media', ['issue_id' => $issue->id]);
                }
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Error updating newspaper issue: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete newspaper issue
     */
    public function deleteIssue($id)
    {
        try {
            $issue = $this->repository->find($id);
            
            // Delete cover image
            if ($issue->cover_image) {
                Storage::disk('public')->delete($issue->cover_image);
            }

            return $this->repository->delete($id);
        } catch (\Exception $e) {
            Log::error('Error deleting newspaper issue: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        try {
            return $this->repository->toggleFeatured($id);
        } catch (\Exception $e) {
            Log::error('Error toggling featured status: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Increment downloads
     */
    public function incrementDownloads($id)
    {
        try {
            return $this->repository->incrementDownloads($id);
        } catch (\Exception $e) {
            Log::error('Error incrementing downloads: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle cover image upload
     */
    protected function handleCoverImageUpload($coverImage)
    {
        $path = $coverImage->store('newspapers/covers', 'public');
        return $path;
    }
}
