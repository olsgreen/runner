<?php

namespace App\Events;

use App\Models\Story;

class StoryRunning
{
    public $story;

    public function __construct(Story $story)
    {
        $this->story = $story;
    }
}
