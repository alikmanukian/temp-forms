<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class ImagePlaceholderGeneratedEvent
{
    use Dispatchable;

    public function __construct(public string $imagePath)
    {
        // may be we want to push placeholder image to s3 or do something else
    }
}
