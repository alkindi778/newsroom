<?php

namespace App\Events;

use App\Models\Opinion;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OpinionPublished
{
    use Dispatchable, SerializesModels;

    public Opinion $opinion;

    /**
     * Create a new event instance.
     */
    public function __construct(Opinion $opinion)
    {
        $this->opinion = $opinion;
    }
}
