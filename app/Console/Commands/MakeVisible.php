<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeVisible extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make-visible:products {model} {--all=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $argument = $this -> argument('model');
        $arguments = $this -> arguments();

        $option = $this -> option('all');
        $options = $this -> options();

        $makeVisibleAll = (int) $option;

        if($makeVisibleAll) $this -> info("Make all products visible");

        else $this -> info("Make special products visible");

        return 0;
    }
}
