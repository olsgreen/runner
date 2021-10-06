<?php

namespace App\Foundation\Runners;

use App\Models\Step;
use Illuminate\Support\Arr;
use Symfony\Component\Process\Process;

class Local implements Runner
{
    public function run(Step $step, array $vars, \Closure $closure): Result
    {
        $env = [];

        $script = $this->getCompiledScript($step->script, $vars);

        $process = Process::fromShellCommandline($script, null, $env);

        $process->setTimeout(null);

        $out = '';

        $process->run(function ($type, $processOutput) use ($step, $closure, &$out) {
            $out .= $processOutput;

            $closure($processOutput);
        });

        return new Result($process->getExitCode(), $out);
    }

    protected function getCompiledScript(string $script, array $vars): string
    {
        $compiled = $this->compileVariables($script, $vars);

        // Compile dates.
        $compiled = $this->compileDates($vars['start_date'], $compiled);

        return $compiled;
    }

    protected function compileDates(\DateTime $date, string $compiled)
    {
        preg_match_all("/@date\(([a-z]+)\)/i", $compiled, $matches);

        foreach ($matches[0] as $k => $match) {
            $compiled = str_replace('{{ ' . $match . ' }}', $date->format($matches[1][$k]), $compiled);
        }

        return $compiled;
    }

    protected function compileVariables(string $script, array $vars): string
    {
        $vars = array_filter($vars);

        $vars = Arr::dot($vars);

        $tokens = collect($vars)
            ->keys()
            ->map(function($item) {
                return '{{ ' . $item . ' }}';
            })
            ->toArray();

        $replacements = array_values($vars);

        $compiled = str_replace($tokens, $replacements, $script);

        //dump($tokens, $replacements, $script, $compiled);

        return $compiled;
    }

    /*protected function getStepVariableTokens(Step $step): array
    {
        $vars = collect([
            $step->task->story->environment,
            $step->task->environment,
            $step->environment
        ])->filter()->map(function($item) {
            return $item->values;
        })->toArray();

        $vars = array_merge(...$vars);

        $tokens = [];

        foreach ($vars as $key => $value) {
            $token = sprintf('{{ %s }}', $key);
            $tokens[$token] = $value;
        }

        return [array_keys($tokens), array_values($tokens)];
    }*/
}
