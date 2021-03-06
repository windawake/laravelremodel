<?php

namespace Laravel\Remote2Model\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CreateExampleModels extends Command
{
    /**
     * @var Filesystem $files
     */
    protected $files;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravelremodel:example-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to create laravel remote model example models';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $boolean = $this->files->copyDirectory(__DIR__.'/../../examples/Models', app_path('Models'));

        if(!$boolean) {
            $this->error('Failed to create Example models!');
        }

        $this->info('Example models created successfully!');
    }
}
