<?php

namespace App\Console\Commands;

use App\Foundation\StoryImporter;
use Illuminate\Console\Command;

class ImportStoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'story:import {pathname}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports a story from a YAML file.';

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
    public function handle(StoryImporter $importer)
    {
        $importer->import($this->argument('pathname'));

        return 0;
    }
}
