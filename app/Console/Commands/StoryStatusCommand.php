<?php

namespace App\Console\Commands;

use App\Models\Story;
use Illuminate\Console\Command;

class StoryStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'story:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Status of the stories.';

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
    public function handle()
    {
        $this->table([
            '#',
            'Name',
            'Last Outcome',
            'Last Run',
            'Last Error Step',
            'Last Error',
        ], Story::all()->map(function($story) {
            if ($session = $story->sessions()->latest()->first()) {
                $errors = $session->outcomes->filter(function ($outcome) {
                    return $outcome !== 0;
                });

                return [
                    $story->id,
                    $story->name,
                    $session->aggregate_exit_code === 0 ? 'Success' : 'Fail',
                    $session->created_at->format('d/m/Y H:i'),
                    $errors->count() > 0 ? trim($errors->first()->name) : '',
                    $errors->count() > 0 ? trim($errors->first()->output) : ''
                ];
            } else {
                return [
                    $story->id,
                    $story->name,
                    '-',
                    '-',
                    '-',
                    '-'
                ];
            }
        }));
    }
}
