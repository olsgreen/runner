<?php

namespace App\Foundation\Runners;

class Result
{
    protected $exitCode;

    protected $output;

    public function __construct(int $exitCode, string $output)
    {
        $this->exitCode = $exitCode;

        $this->output = $output;
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function wasSuccess(): bool
    {
        return $this->exitCode === 0;
    }
}
