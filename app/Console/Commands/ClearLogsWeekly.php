<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class ClearLogsWeekly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Laravel logs older than 2 days';

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
     * @return mixed
     */

public function handle()
{
    $path = storage_path('logs');
    $days = 2;

    foreach (glob($path . '/*.log') as $file) {
        if (filemtime($file) < Carbon::now()->subDays($days)->timestamp) {
            @unlink($file);
        }
    }

    $this->info("Old logs cleared successfully.");
}

}
