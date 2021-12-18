<?php

namespace App\Console\Commands;

use App\Models\VerificatedTask;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TimeoutTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:timeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove timeouted verificated tasks';

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
        VerificatedTask::where([
            ['status', '!=', 'executed'],
            ['created', '<', Carbon::now()->addHours(-1)->getTimestamp()],
        ])->delete();
    }
}
