<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Schedule as ScheduleModel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $scheduler)
    {
        $schedules = ScheduleModel::all()->each(function($schedule) use ($scheduler) {
            $s = $scheduler->command('story:run ' . $schedule->id)
                ->{$schedule->definition}(...$schedule->args ?? []);

            if ($schedule->notify === ScheduleModel::NOTIFY_ALL) {
                $s->emailOutputTo($schedule->email);
            } elseif ($schedule->notify === ScheduleModel::NOTIFY_FAILURE) {
                $s->emailOutputOnFailure($schedule->email);
            }
        });
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
