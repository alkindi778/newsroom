<?php

namespace App\Services;

use App\Repositories\Interfaces\VideoRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VideoService
{
    protected $videoRepository;

    public function __construct(VideoRepositoryInterface $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    /**
     * Get all videos with filters
     */
    public function getAllVideos(array $filters = [])
    {
        try {
            return $this->videoRepository->all($filters);
        } catch (\Exception $e) {
            Log::error('Error fetching videos: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get published videos
     */
    public function getPublishedVideos(array $filters = [])
    {
        try {
            return $this->videoRepository->getPublished($filters);
        } catch (\Exception $e) {
            Log::error('Error fetching published videos: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get featured videos
     */
    public function getFeaturedVideos($limit = 4)
    {
        try {
            return $this->videoRepository->getFeatured($limit);
        } catch (\Exception $e) {
            Log::error('Error fetching featured videos: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get video by ID
     */
    public function getVideoById($id)
    {
        try {
            return $this->videoRepository->find($id);
        } catch (\Exception $e) {
            Log::error('Error fetching video: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get video by slug
     */
    public function getVideoBySlug($slug)
    {
        try {
            $video = $this->videoRepository->findBySlug($slug);
            // Increment views
            $this->videoRepository->incrementViews($video->id);
            return $video->fresh();
        } catch (\Exception $e) {
            Log::error('Error fetching video by slug: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create new video
     */
    public function createVideo(array $data)
    {
        try {
            // Handle thumbnail upload
            if (isset($data['thumbnail'])) {
                $data['thumbnail'] = $this->handleThumbnailUpload($data['thumbnail']);
            }

            // Extract video ID from URL
            if (isset($data['video_url'])) {
                $videoData = $this->extractVideoData($data['video_url']);
                $data['video_type'] = $videoData['type'];
                $data['video_id'] = $videoData['id'];
            }

            // Set published_at if not provided
            if (isset($data['is_published']) && $data['is_published'] && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            $video = $this->videoRepository->create($data);
            
            // إطلاق Event لإرسال Push Notifications عند النشر
            if ($video && $video->is_published) {
                event(new \App\Events\VideoPublished($video));
            }
            
            return $video;
        } catch (\Exception $e) {
            Log::error('Error creating video: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update video
     */
    public function updateVideo($id, array $data)
    {
        try {
            $video = $this->videoRepository->find($id);

            // Handle thumbnail upload
            if (isset($data['thumbnail']) && is_file($data['thumbnail'])) {
                // Delete old thumbnail
                if ($video->thumbnail) {
                    Storage::disk('public')->delete($video->thumbnail);
                }
                $data['thumbnail'] = $this->handleThumbnailUpload($data['thumbnail']);
            }

            // Extract video ID from URL if changed
            if (isset($data['video_url']) && $data['video_url'] !== $video->video_url) {
                $videoData = $this->extractVideoData($data['video_url']);
                $data['video_type'] = $videoData['type'];
                $data['video_id'] = $videoData['id'];
            }

            return $this->videoRepository->update($id, $data);
        } catch (\Exception $e) {
            Log::error('Error updating video: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete video
     */
    public function deleteVideo($id)
    {
        try {
            return $this->videoRepository->delete($id);
        } catch (\Exception $e) {
            Log::error('Error deleting video: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Publish video
     */
    public function publishVideo($id)
    {
        try {
            return $this->videoRepository->publish($id);
        } catch (\Exception $e) {
            Log::error('Error publishing video: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Unpublish video
     */
    public function unpublishVideo($id)
    {
        try {
            return $this->videoRepository->unpublish($id);
        } catch (\Exception $e) {
            Log::error('Error unpublishing video: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        try {
            return $this->videoRepository->toggleFeatured($id);
        } catch (\Exception $e) {
            Log::error('Error toggling featured status: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle thumbnail upload
     */
    protected function handleThumbnailUpload($thumbnail)
    {
        $path = $thumbnail->store('videos/thumbnails', 'public');
        return $path;
    }

    /**
     * Extract video type and ID from URL
     */
    protected function extractVideoData($url)
    {
        // YouTube patterns
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $match)) {
            return [
                'type' => 'youtube',
                'id' => $match[1]
            ];
        }

        // Vimeo pattern
        if (preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/i', $url, $match)) {
            return [
                'type' => 'vimeo',
                'id' => $match[1]
            ];
        }

        // Default to local
        return [
            'type' => 'local',
            'id' => null
        ];
    }

    /**
     * Bulk operations
     */
    public function bulkPublish(array $ids)
    {
        try {
            return $this->videoRepository->bulkPublish($ids);
        } catch (\Exception $e) {
            Log::error('Error bulk publishing videos: ' . $e->getMessage());
            throw $e;
        }
    }

    public function bulkUnpublish(array $ids)
    {
        try {
            return $this->videoRepository->bulkUnpublish($ids);
        } catch (\Exception $e) {
            Log::error('Error bulk unpublishing videos: ' . $e->getMessage());
            throw $e;
        }
    }

    public function bulkDelete(array $ids)
    {
        try {
            return $this->videoRepository->bulkDelete($ids);
        } catch (\Exception $e) {
            Log::error('Error bulk deleting videos: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get section title
     */
    public function getSectionTitle()
    {
        try {
            return $this->videoRepository->getSectionTitle();
        } catch (\Exception $e) {
            Log::error('Error getting section title: ' . $e->getMessage());
            return 'فيديو العربية'; // Default fallback
        }
    }

    /**
     * Update section title
     */
    public function updateSectionTitle(string $title)
    {
        try {
            return $this->videoRepository->updateSectionTitle($title);
        } catch (\Exception $e) {
            Log::error('Error updating section title: ' . $e->getMessage());
            throw $e;
        }
    }
}
