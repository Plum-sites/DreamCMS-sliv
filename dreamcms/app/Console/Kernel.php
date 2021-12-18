<?php

namespace App\Console;

use App\Console\Commands\CheckBansCommand;
use App\Console\Commands\DigisellerCommand;
use App\Console\Commands\FillGreatKitsCommand;
use App\Console\Commands\FillKitsCommand;
use App\Console\Commands\ForumRecountCommand;
use App\Console\Commands\KillCommand;
use App\Console\Commands\MoveIntegrations;
use App\Console\Commands\PingServersCommand;
use App\Console\Commands\RecordCommand;
use App\Console\Commands\ReloadCommand;
use App\Console\Commands\RemakeUsersGroupsCommand;
use App\Console\Commands\ShopExport;
use App\Console\Commands\SyncPermCommand;
use App\Console\Commands\TestCommand;
use App\Console\Commands\TimeoutTasksCommand;
use App\Console\Commands\WipeDonateCommand;
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
        SyncPermCommand::class,
        PingServersCommand::class,
        RemakeUsersGroupsCommand::class,
        TimeoutTasksCommand::class,
        CheckBansCommand::class,
        WipeDonateCommand::class,
        ForumRecountCommand::class,
        TestCommand::class,
        ShopExport::class,
        RecordCommand::class,
        ReloadCommand::class,
        MoveIntegrations::class,
        FillKitsCommand::class,
        FillGreatKitsCommand::class,
        KillCommand::class,
        DigisellerCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('perm:sync')->everyMinute();
        $schedule->command('servers:ping')->everyMinute();
        $schedule->command('ban:delete-expired')->everyMinute();
        $schedule->command('bans:check')->everyMinute();
        $schedule->command('forum:recount')->everyMinute();
        $schedule->command('digiseller:check')->everyMinute();
        $schedule->command('tasks:timeout')->hourly();
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
