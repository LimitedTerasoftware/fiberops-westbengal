<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CustomCommand::class,
        \App\Console\Commands\DbClearCommand::class,
        \App\Console\Commands\BharatNetStatusUpdate::class,
        \App\Console\Commands\AutoLogoutProviders::class,
        \App\Console\Commands\SyncEmployeeMaterialLedger::class,
        \App\Console\Commands\ClearLogsWeekly::class,


    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cronjob:rides')
                ->everyMinute();
           
        $schedule->command('cronjob:demodata')
                ->weeklyOn(1, '8:00');
                
        $schedule->command('attendance:autologout')->dailyAt('23:45');  
        $schedule->command('bharatnet:update')->cron('*/30 * * * *');
        $schedule->command('ledger:sync-employee-materials')
             ->everyTenMinutes()
             ->withoutOverlapping()
             ->runInBackground();
             
        $schedule->command('logs:clear-old')->weeklyOn(0, '02:00'); 
     
  
                         
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
