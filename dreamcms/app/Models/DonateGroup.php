<?php

namespace App\Models;

use Auth;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DonateGroup extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'groups';
    public $timestamps = false;

    public static $KITS = [
        "vip" => ["vip"],
        "premium" => ["vip", "premium"],
        "deluxe" => ["vip", "premium", "deluxe"],
        "legend" => ["vip", "premium", "deluxe", "legend"],
    ];

    protected $fillable = [
        'name', 'pexname', 'active', 'sort', 'price', 'css', 'time', 'discount', 'discount_start', 'discount_end', 'benefits'
    ];

    protected $casts = [
        'benefits' => 'array'
    ];

    public function getUserGroups($server = false){
        if($server){
            return UserGroup::where([
                ['server_id', '=', $server],
                ['group_id', '=', $this->id]
            ])->get();
        }else{
            return UserGroup::where([
                ['group_id', '=', $this->id]
            ])->get();
        }
    }

    public static function getActive($get = ['*']){
       return DonateGroup::where([
            ['active', '=', '1'],
            ['price', '>', '0']
        ])->get($get);
    }

    public function getKits(){
        return self::$KITS[$this->pexname];
    }

    public function giveKits(User $user){
        if (self::$KITS[$this->pexname]){
            foreach (self::$KITS[$this->pexname] as $kit){
                $this->giveKit($user, $kit);
            }
        }
    }

    public static function giveKit(User $user, $kit){
        $citem = new CartItem;

        $citem->type = 'ESSENTIALS_KIT';
        $citem->damage = 0;
        $citem->count = 1;
        $citem->shop = 1;
        $citem->nbt = $kit;
        $citem->uuid = $user->uuid;

        $citem->save();
    }

    public function createUsergroup(User $user, Server $server){
        $usergroup = new UserGroup;
        $usergroup->user_id = $user->id;
        $usergroup->server_id = $server->id;
        $usergroup->group_id = $this->id;
        $usergroup->time = time();
        $usergroup->expire = time() + $this->time;
        return $usergroup->save();
    }

    /* @var Server $server */
    public function buy(User $user, $server = false){
        /* @var Server $server */
        $server = Server::findOrFail($server);

        if ($user->withdrawRealmoney($this->price)){
            Activity::user_action(Auth::user(), 'buygroup', [
                'group' => $this->name,
                'server' => $server->id,
                'price' => $this->price
            ]);

            $this->giveKits($user);
            $this->giveOrRenew($user, $server);
        }else throw new \RuntimeException("Not enough money!", 403);
    }

    /* @var Server $server */
    public function giveOrRenew(User $user, $server = false){
        $renewal = false;
        $upgrade = false;

        $activegroups = $user->getActiveGroups($server->id);

        $activegroups->each(function ($item, $key) use (&$renewal, &$upgrade){
            /* @var UserGroup $item */
            if ($item->getDonateGroup()->id == $this->id) $renewal = true;
            if ($this->sort > $item->getDonateGroup()->sort) $upgrade = true;
        });

        if ($upgrade){
            UserGroup::where([
                ['user_id', '=', $user->id],
                ['server_id', '=', $server->id]
            ])->delete();

            $this->createUsergroup($user, $server);
            $server->getPermissionManager()->addUserToGroup($user->uuid, $this->pexname, time() + $this->time);
        }else{
            if ($renewal){
                $ug = UserGroup::where([
                    ['user_id', '=', $user->id],
                    ['server_id', '=', $server->id],
                    ['group_id', '=', $this->id],
                ])->first();

                $ug->expire = $ug->expire + $this->time;
                $ug->save();

                $server->getPermissionManager()->addUserToGroup($user->uuid, $this->pexname, $ug->expire);
            }else{
                $this->createUsergroup($user, $server);
                $server->getPermissionManager()->addUserToGroup($user->uuid, $this->pexname, time() + $this->time);
            }
        }

        $user->forgetCachedPermissions();
    }
}
