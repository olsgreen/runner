<?php

namespace App\Console\Commands;

use App\Events\StepFailed;
use App\Events\StepOutput;
use App\Events\StepStarted;
use App\Events\StepSucceeded;
use App\Foundation\StoryRunner;
use App\Models\Step;
use App\Models\Story;
use Illuminate\Console\Command;

class RunStoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'story:run {story_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs a story.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(StoryRunner $runner)
    {
        $story = Story::findOrFail($this->argument('story_id'));

        $this->info('Running story \'' . $story->name . '\'...');

        $runner->getEvents()
            ->listen(StepStarted::class, function(StepStarted $ev) {
                $this->getOutput()->write($ev->step->name . '... ');
            });

        $runner->getEvents()
            ->listen(StepFailed::class, function(StepFailed $ev) {
                $this->getOutput()->write('❌' . PHP_EOL);
                $this->error(trim($ev->outcome->output));
            });

        $runner->getEvents()
            ->listen(StepSucceeded::class, function(StepSucceeded $ev) {
                $this->getOutput()->write('✅' . PHP_EOL);
            });

        $runner->getEvents()
            ->listen(StepOutput::class, function(StepOutput $event) {
                //$this->warn('Output');
                //$this->line($event->output);
            });

        $exitCode = $runner->run($story);

        if ($exitCode === 0) {
            $this->info('Command complete.');
        } else {
            $this->error('Command failed.');
        }

        return $exitCode;
    }
}
