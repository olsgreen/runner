<?php

namespace App\Events;

use App\Models\Session;
use App\Models\Story;

class StorySucceeded
{
    public $story;

    public $session;

    public function __construct(Story $story, Session $session)
    {
        $this->story = $story;

        $this->session = $session;
    }
}
