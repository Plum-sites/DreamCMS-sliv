<?php

namespace App\Console\Commands;

use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckBansCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bans:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired bans';

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
        \DB::table('ban_list')->where([
            ['Temptime', '!=', '0'],
            ['Temptime', '<', Carbon::now()->getTimestamp()],
        ])->delete();
    }
}
