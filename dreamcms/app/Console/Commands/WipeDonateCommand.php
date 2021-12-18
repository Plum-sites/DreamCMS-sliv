<?php

namespace App\Console\Commands;

use App\Models\CartItem;
use App\Models\DonateGroup;
use App\Models\Server;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Console\Command;

class WipeDonateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donate:wipe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use it only on wipe! Delete all kits in cart, usergroups, donate groups on server and then return cash to user (percent of unused time) and return kits if user buy group no later than 7 days';

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

        /*$servers = collect();
        foreach (Server::getActive() as $server){
            $servers->put($server->id, $server);
        }*/

        $dgroups = collect();
        foreach (DonateGroup::all() as $dg){
            $dgroups->put($dg->id, $dg);
        }

        // CartItem::where([
        //     ['type', '=', 'ESSENTIALS_KIT']
        // ])->delete();

        /*$ug_toremove = UserGroup::where([
            ['time', '<=', $time - (60 * 60 * 24 * 7)],
        ])->get();

        foreach ($ug_toremove as $usergroup){
            $user = $usergroup->getUser();
            $server = $servers->get($usergroup->server_id);
            $dg = $dgroups->get($usergroup->group_id);

            if ($usergroup->server_id == 30 || $usergroup->server_id == 40 || $usergroup->server_id == 41) continue;

            $sum = round((($usergroup->expire - $time) / (60 * 60 * 24 * 30)) * $dg->price, 2);
            if ($sum < $dg->price){
                $usergroup->delete();

                CartItem::where([
                    ['type', '=', 'ESSENTIALS_KIT'],
                    ['uuid', '=', $user->uuid],
                ])->delete();

                try{
                    $server->getPermissionManager()->clearUser($user->uuid);
                }catch (\Throwable $e){}

                $user->addRealmoney($sum);

                echo 'CLEAR ' . $usergroup->id . ':' . $user->login . ':' . $server->name  . ':' . $dg->pexname . ': ' . $sum .  PHP_EOL;
            }
        }
 
        echo PHP_EOL;
        */

        $ug_checkkits = UserGroup::where([
            ['time', '>=', $time - (60 * 60 * 24 * 14)],
            ['time', '<=', $time - (60 * 60 * 12)],
        ])->get();

        $userkits = collect();

        foreach ($ug_checkkits as $usergroup){
            $user = $usergroup->getUser();
            if(!$userkits->has($user->id)){
                $userkits->put($user->id, CartItem::where([
                    ['type', '=', 'ESSENTIALS_KIT'],
                    ['uuid', '=', $user->uuid],
                ])->get());
            }
        }

        foreach ($ug_checkkits as $usergroup){
            $user = $usergroup->getUser();
            $dg = $dgroups->get($usergroup->group_id);
            $user_kits = $userkits->get($user->id);

            $needkits = collect();

            foreach ($dg->getKits() as $need_kit){
                $has = false;
                foreach ($user_kits as $has_kit){
                    if($has_kit->nbt == $need_kit){
                        $has = true;
                        break;
                    }
                }
                if (!$has){
                    $needkits->push($need_kit);
                }
            }

            foreach ($needkits as $kit){
                $citem = new CartItem;

                $citem->type = 'ESSENTIALS_KIT';
                $citem->damage = 0;
                $citem->count = 1;
                $citem->shop = 1;
                $citem->nbt = $kit;
                $citem->uuid = $user->uuid;

                $citem->save();
            }

            echo $user->login . ': ' . json_encode($needkits) . PHP_EOL;
        }



    }
}
