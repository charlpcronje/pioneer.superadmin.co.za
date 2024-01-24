<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DownloadDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pioneer:download_directory';

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
        $paths = [];
        \folders_tree($paths);
        file_put_contents(storage_path('app/public/directories.json'), json_encode($paths));
        //mail("philippe@fgx.co.za", "Cron Set up", "Cron has just ran");
        return 1;
    }
}
