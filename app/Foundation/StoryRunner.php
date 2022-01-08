<?php

namespace App\Foundation;

use App\Events\StepFailed;
use App\Events\StepOutput;
use App\Events\StepStarted;
use App\Events\StepSucceeded;
use App\Events\StoryFailed;
use App\Events\StoryRunning;
use App\Events\StorySucceeded;
use App\Foundation\Runners\Factory;
use App\Models\Outcome;
use App\Models\Step;
use App\Models\Story;
use App\Models\Session;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;

class StoryRunner
{
    protected $factory;

    protected $events;

    protected $outputEnv = [];

    public function __construct(Factory $factory, Dispatcher $events)
    {
        $this->factory = $factory;

        $this->events = $events;
    }

    public function getEvents(): Dispatcher
    {
        return $this->events;
    }

    public function run(Story $story)
    {
        $this->events->dispatch(new StoryRunning($story));

        $session = $story->sessions()->create(['name' => $story->name]);

        $exitCode = 0;

        foreach ($story->tasks as $task) {
            foreach ($task->steps as $step) {
                $exitCode =+ $this->runStep($step, $session);

                if ($exitCode !== 0) {
                    break;
                }
            }

            if ($exitCode !== 0) {
                break;
            }
        }

        $session->finished_at = now();
        $session->aggregate_exit_code = $exitCode;
        $session->save();

        if ($exitCode !== 0) {
            $this->events->dispatch(new StoryFailed($story, $session));
            return $exitCode;
        }

        $this->events->dispatch(new StorySucceeded($story, $session));


        return 0;
    }

    protected function getEnvironment(Session $session): array
    {
        $env = [
            'start_date' => $session->created_at,
            'output' => []
        ];

        if ($session->story->environment) {
            $env = array_merge($env, $this->outputEnv, $session->story->environment->values);
        }

        return $env;
    }

    protected function runStep(Step $step, Session $session): int
    {
        $this->events->dispatch(new StepStarted($step));

        $runner = $this->factory->make($step->runner);

        $env = $this->getEnvironment($session);

        $result = $runner->run($step, $env, function ($output) use ($step) {
            $this->events->dispatch(new StepOutput($step, $output));
        });

        $outcome = new Outcome([
            'name' => sprintf('%s: %s', $step->task->name, $step->name),
            'exit_code' => $result->getExitCode(),
            'output' => $result->getOutput(),
        ]);

        $taskSlug = Str::slug($step->task->name, '_');
        $stepSlug = Str::slug($step->name, '_');
        $this->outputEnv['output'][$taskSlug][$stepSlug] = trim($result->getOutput());

        $outcome->session()->associate($session);
        $outcome->step()->associate($step);
        $outcome->save();

        if ($result->wasSuccess()) {
            $this->events->dispatch(new StepSucceeded($step, $outcome));
            return 0;
        }

        $this->events->dispatch(new StepFailed($step, $outcome));

        return $result->getExitCode();
    }
}
