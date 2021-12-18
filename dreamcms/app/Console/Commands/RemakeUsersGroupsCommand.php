<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RemakeUsersGroupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:remake_groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete users donate groups on server and regive them';

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
        try{
            ini_set('max_execution_time', 0);
            set_time_limit(0);

            $time = time();

            echo "Load usergroups" . PHP_EOL;
            $usergroups = \App\Models\UserGroup::where([
                ['expire', '>=', $time],
                ['server_id', '!=', 30],
            ])->get();

            echo "Load servers". PHP_EOL;
            $servers = collect();
            foreach (\App\Models\Server::getActive() as $server){
                $servers->put($server->id, $server->getPermissionManager());
            }

            echo "Load donate groups". PHP_EOL;
            $dgroups = collect();
            foreach (\App\Models\DonateGroup::all() as $dg){
                $dgroups->put($dg->id, $dg->pexname);
            }

            echo "Start". PHP_EOL;
            /* @var \App\Models\UserGroup $ug*/
            foreach ($usergroups as $ug){
                try{
                    if ($ug){
                        /* @var \App\Models\Managers\PermissionEXManager $pm*/
                        $pm = $servers->get($ug->server_id);
                        if ($pm){
                            $dg = $dgroups->get($ug->group_id);
                            if ($dg){
                                $user = \App\Models\User::find($ug->user_id);
                                if ($user){
                                    $pm->removeUserFromGroup($user->uuid, $dg);
                                    $pm->addUserToGroup($user->uuid, $dg, $ug->expire);
                                    echo $user->login . " " . $ug->server_id . " " . $dg . " " . $ug->expire . PHP_EOL;
                                }
                            }
                        }
                    }
                }catch (\Throwable $exception){
                    echo $ug->server_id . ":" . ":" . $exception->getLine() . " " . $exception->getMessage() . PHP_EOL;
                }
            }
        }catch (\Throwable $exception){
            echo $exception->getMessage();
        }
    }
}
