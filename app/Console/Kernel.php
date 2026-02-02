<?php

namespace App\Console;

use App\Models\Intern;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        \App\Console\Commands\MarkAbsentIntern::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // mark absent if no leave and check in 
        $schedule->command('attendance:mark-absent')
        ->dailyAt('23:59')
        ->name('mark-absent')
        ->withoutOverlapping();

        // Status intern (active or completed)
        $schedule->call(function () {
            Intern::where('end_date', '<', now())->update(['status' => 'completed']);
            Intern::where('end_date', '>=', now())->update(['status' => 'active']);
        })->daily()->name('update-intern-status')->withoutOverlapping();
    }


    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
