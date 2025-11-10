<?php

namespace App\Events;

use App\Models\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoPublished
{
    use Dispatchable, SerializesModels;

    public Video $video;

    /**
     * Create a new event instance.
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }
}
