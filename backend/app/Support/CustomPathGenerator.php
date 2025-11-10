<?php

namespace App\Support;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    /**
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    /**
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    /**
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive/';
    }

    /**
     * Get a (unique) base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
        // Get the model that owns this media
        $model = $media->model;
        
        // Determine the folder based on collection name
        $folder = match ($media->collection_name) {
            'articles' => 'articles',
            'opinions' => 'opinions',
            'videos' => 'videos',
            default => 'media',
        };

        // Get creation date
        $date = $model->created_at ?? now();
        
        // Build path: media/{type}/{year}/{month}/{model_id}
        return sprintf(
            'media/%s/%s/%s',
            $folder,
            $date->format('Y/m'),
            $model->id
        );
    }
}
