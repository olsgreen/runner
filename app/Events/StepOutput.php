<?php

namespace App\Events;

use App\Models\Step;

class StepOutput
{
    public $step;

    public $output;

    public function __construct(Step $step, string $output)
    {
        $this->step = $step;

        $this->output = $output;
    }
}
