<?php

namespace App\Foundation;

use App\Foundation\Runners\Factory;
use App\Models\Environment;
use App\Models\Step;
use App\Models\Story;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Yaml\Yaml;

class StoryImporter
{
    public function import(string $pathname)
    {
        $pathname = $this->validatePathname($pathname);

        $data = file_get_contents($pathname);

        $storyData = Yaml::parse($data);

        // Create the story.
        $story = Story::create([
            'name' => $storyData['name']
        ]);

        // Create the schedule.
        if (isset($storyData['schedule'])) {
            $story->schedule()->create([
                'definition' =>  $storyData['schedule']['definition'],
                'args' => $storyData['schedule']['args'] ?? [],
                'notify' => $storyData['schedule']['notify'] ?? \App\Models\Schedule::NOTIFY_NONE,
                'email' => $storyData['schedule']['email'] ?? null
            ]);
        }

        // Add the story's environment data.
        if (isset($storyData['environment'])) {
            $this->createEnvironment(
                $story, $storyData['environment']
            );
        }

        foreach ($storyData['tasks'] as $taskName => $taskData) {
            // Add the task.
            $task = $story->tasks()->create([
                'name' => $taskName,
                'order' => $stepData['order'] ?? 500,
            ]);

            // Add the environment.
            if (isset($taskData['environment'])) {
                $this->createEnvironment(
                    $task, $taskData['environment']
                );
            }

            // Add the steps.
            foreach ($taskData['steps'] as $stepName => $stepData) {
                $this->validateStepData($stepData);

                $step = $task->steps()->create([
                    'name' => $stepName,
                    'script' => $stepData['script'],
                    'runner' => $stepData['runner'] ?? 'local',
                    'order' => $stepData['order'] ?? 500,
                ]);

                if (isset($stepData['environment'])) {
                    $this->createEnvironment(
                        $step, $stepData['environment']
                    );
                }
            }
        }

        dd('Data', $pathname, $storyData, $story->id);
    }

    protected function createEnvironment(Model $model, array $values): Environment
    {
        $environment = new Environment();
        $environment->values = $values;
        $environment->save();

        $model->environment()->associate($environment);
        $model->save();

        return $environment;
    }

    protected function validateStepData(array $data): bool
    {
        if (empty($data['script'])) {
            throw new InvalidArgumentException('Steps cannot have empty scripts.');
        }

        if (isset($data['type']) && !in_array($data['type'], [Step::TYPE_COMMAND, Step::TYPE_SCRIPT])) {
            throw new InvalidArgumentException('Invalid step type.');
        }

        $runnerTypes = array_keys(Factory::getRunnerTypeMap());

        if (isset($data['runner']) && !in_array($data['runner'], $runnerTypes)) {
            throw new InvalidArgumentException('Invalid step runner type.');
        }

        return true;
    }

    protected function validatePathname(string $pathname): string
    {
        $realPath = realpath($pathname);

        if (!file_exists($realPath) || !is_readable($realPath)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The pathname provided could not be found or is not readable. [%s]',
                    $pathname
                )
            );
        }

        return $realPath;
    }
}
