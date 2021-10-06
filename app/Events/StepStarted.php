<?php

namespace App\Events;

use App\Models\Step;

class StepStarted
{
    public $step;

    public function __construct(Step $step)
    {
        $this->step = $step;
    }
}
