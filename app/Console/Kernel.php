<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\Project\ProjectController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        $schedule->call(function () {
            $msg = "MYLAN TEST CRON JOB";

            // send email
            mail("prajay@ajency.in","CRON JOB TEST",$msg);
            $myfile = fopen("/var/www/html/mylan.txt", "w") or die("Unable to open file!");

            })->everyMinute();    

        $schedule->call(function () {
            flushCacheMemory();

            $myfile = fopen("/var/www/html/newtest.txt", "w") or die("Unable to open file!");

            })->daily();         
    }
}
