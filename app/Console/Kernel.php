<?php

namespace App\Console;

use App\Models\AccessControlTimeSlot;
use App\Models\Company;
use App\Models\DeviceActivesettings;
use App\Models\PayrollSetting;
use App\Models\ReportNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $monthYear = date("M-Y");

        $schedule
            ->command('task:sync_attendance_logs')
            ->everyMinute()

            //->withoutOverlapping()
            ->appendOutputTo(storage_path("logs/" . date("d-M-y") . "-attendance-logs.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

        $schedule
            ->command('task:update_company_ids')
            // ->everyThirtyMinutes()
            ->everyMinute()
            //->withoutOverlapping()
            ->appendOutputTo(storage_path("logs/$monthYear-logs.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

        if (env("APP_ENV") == "production") {
            $schedule
                ->command('task:db_backup')
                ->dailyAt('6:00')
                ->appendOutputTo(storage_path("logs/db_backup.log"))
                ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            $schedule
                ->command('restart_sdk')
                // ->everyMinute()
                // ->everyThirtyMinutes()
                ->dailyAt('4:00')
                //->hourly()
                ->appendOutputTo(storage_path("logs/restart_sdk.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
