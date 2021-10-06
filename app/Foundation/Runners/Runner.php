<?php

namespace App\Foundation\Runners;

use App\Models\Step;

interface Runner
{
    public function run(Step $step, array $vars, \Closure $closure): Result;
}
