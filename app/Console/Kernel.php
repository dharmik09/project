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
        //
        'App\Console\Commands\SendNotification',
        'App\Console\Commands\RemoveNotificationData',
        'App\Console\Commands\InactiveUserNotification',
        'App\Console\Commands\expiredProCoinsForTeenager',
        'App\Console\Commands\resetAdminGiftedProCoins',
        'App\Console\Commands\CreateSlug',
        'App\Console\Commands\CalculateHMLScore',
        'App\Console\Commands\ImportInstituteSpeciality'
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
        $schedule->command('sendNotification')
                 ->everyTenMinutes();

        $schedule->command('RemoveNotificationData')
                 ->weekly();

        $schedule->command('InactiveUserNotification')
                 ->weekly();

        $schedule->command('expiredProCoinsForTeenager')
                 ->weekly();

        $schedule->command('resetAdminGiftedProCoins')
                 ->weekly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
