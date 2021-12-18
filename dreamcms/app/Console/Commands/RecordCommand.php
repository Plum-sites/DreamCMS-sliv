<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RecordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'servers:record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping all servers and count record';

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
        $curonline = 0;

        /* @var Server $server */
        foreach (Server::getActive() as $server){
            try{
                if($info = $server->getStatus()){
                    $curonline += $server->online;
                }
            }catch (\Throwable $e){}
        }

        $key = Carbon::now()->toDateString();
        $record = Cache::get('record_' . $key, 0);

        if ($record < $curonline)
            Cache::set('record_' . $key, $curonline);
    }
}
