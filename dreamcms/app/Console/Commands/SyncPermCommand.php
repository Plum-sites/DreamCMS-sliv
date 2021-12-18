<?php

namespace App\Console\Commands;

use App\Models\DonateGroup;
use App\Models\Managers\MiniGamesPermissionManager;
use App\Models\Server;
use App\Models\UserGroup;
use Illuminate\Console\Command;

class SyncPermCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perm:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired donate groups and remove prefixes';

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
    public static function handle()
    {
        $time = time();
        $expired = UserGroup::where([
            ['expire', '<=', $time]
        ])->get();

        $servers = collect();
        foreach (Server::getActive() as $server){
            $servers->put($server->id, $server->getPermissionManager());
        }

        $dgroups = collect();
        foreach (DonateGroup::all() as $dg){
            $dgroups->put($dg->id, $dg->pexname);
        }

        echo $expired->count();

        /* @var UserGroup $ug*/
        foreach ($expired as $ug){
            try{
                $pm = $servers->get($ug->server_id);
                if ($pm){
                    $dg = $dgroups->get($ug->group_id);
                    if ($dg){
                        $user = $ug->getUser();

                        $pm->removeUserFromGroup($user->uuid, $dg);
                        $pm->removeUserPrefix($user->uuid);
                        $pm->removeUserSuffix($user->uuid);
                    }
                }
            }catch (\Exception $exception){}
        }

        UserGroup::where([
            ['expire', '<=', $time]
        ])->delete();
 
        $give = UserGroup::where([
            ['time', '>=', $time - 60],
            ['time', '<=', $time + 60],
        ])->get();

        foreach ($give as $ug){
            try{
                $pm = $servers->get($ug->server_id);
                //if ($pm instanceof MiniGamesPermissionManager) continue;
                
                $dg = $dgroups->get($ug->group_id);
                $user = $ug->getUser();

                $pm->removeUserFromGroup($user->uuid, $dg);
                $pm->addUserToGroup($user->uuid, $dg, $ug->expire);
            }catch (\Exception $exception){}
        }
    }
}
