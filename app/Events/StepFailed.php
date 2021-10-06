<?php

namespace App\Events;

use App\Models\Outcome;
use App\Models\Step;

class StepFailed
{
    public $step;

    public $outcome;

    public function __construct(Step $step, Outcome $outcome)
    {
        $this->step = $step;

        $this->outcome = $outcome;
    }
}
