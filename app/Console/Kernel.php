<?php

namespace App\Console;

use App\Models\Intern;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        // Update intern statuses daily
        $schedule->call(function () {
            // Mark interns as completed if their end_date has passed
            Intern::where('end_date', '<', now())->update(['status' => 'completed']);

            // Mark interns as active if their end_date is today or in the future
            Intern::where('end_date', '>=', now())->update(['status' => 'active']);
        })->daily()->name('update-intern-status')->withoutOverlapping();
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
