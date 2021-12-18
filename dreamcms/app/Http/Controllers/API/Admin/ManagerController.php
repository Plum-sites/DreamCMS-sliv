<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\DonateGroup;
use App\Models\Interfaces\IDBPermissionManager;
use App\Models\Managers\MiniGamesPermissionManager;
use App\Models\Server;
use App\Models\ShopItem;
use App\Models\User;
use App\Models\UserGroup;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Gate;
use Illuminate\Http\Request;

class ManagerController extends Controller{

    public function __construct()
    {
        $this->middleware(function ($request, $next)
        {
            if (Gate::denies('ext', ['admin', 'manager.access'])){
                abort(403, 'Недостаточно прав!');
            }

            return $next($request);
        });
    }

    public function loadUser(Request $request){
        /* @var User $user */
        $user = User::findOrFail($request->get('id'));
        
        $user->head_url = '/head/' . $user->login . '/100';
        
        $status = [];
        /* @var UserGroup $ug */
        foreach ($user->getActiveGroups() as $ug){
            $ug->server = $ug->getServer()->only('id', 'name');
            $ug->group = $ug->getDonateGroup()->only('id', 'name');

            $status[] = $ug;
        }
        
        $cart = CartItem::where('uuid', '=', $user->uuid)->get();
        
        $bans = $user->getServerBans();
        
        return \Response::json([
            'user' => $user,
            'status' => $status,
            'cart' => $cart,
            'bans' => [
                'game' => $bans,
                'forum' => $user->bans()->get()
            ]
        ]);
    }
    
    public function findUser(Request $request){
        $q = $request->get('q');
        
        $data = User::where('login', 'LIKE', '%' . $q . '%')->limit(100)->get(['id', 'uuid', 'login', 'email', 'realmoney', 'money', 'last_play', 'reputation']);
        
        $user = User::where('login', '=', $q)->get(['id', 'uuid', 'login', 'email', 'realmoney', 'money', 'last_play', 'reputation'])->first();

        if($user) $data->prepend($user);

        $data = $data->map(function ($item, $key) {
            $item->head_url = '/head/' . $item->login . '/100';
            return $item;
        });
        
        return \Response::json([
            'data' => $data
        ]);
    }

    public function remove2FA(Request $request){
        /* @var $user User */
        $user = User::findOrFail($request->get('user'));

        if (Gate::denies('ext', ['manager', '2fa.delete'])){
            abort(403, 'Недостаточно прав!');
        }

        if (!\Auth::user()->hasRole('admin')){
            return \Response::json([
                'success' => false,
                'message' => 'У вас нет прав отключать 2FA!'
            ]);
        }

        $user->otp_secret = null;
        $user->save();

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно удалили 2FA пользователя!'
        ]);
    }

    public function clearServerPerms(Request $request){
        if (Gate::denies('ext', ['manager', 'permissions.revoke'])){
            abort(403, 'Недостаточно прав!');
        }

        $user = User::findOrFail($request->get('user'));

        $failed = collect();

        foreach (Server::getActive() as $server){
            try{
                /* @var $pm IDBPermissionManager */
                $pm = $server->getPermissionManager();
                $pm->clearUser($user);
            }catch (\Throwable $e){
                $failed->push($server);
            }
        }

        if ($failed->count() > 0){
            return \Response::json([
                'success' => true,
                'message' => 'Не удалось очистить права на ' . $failed->implode('name', ', ') .'! На остальных успешно очищено!'
            ]);
        }else{
            return \Response::json([
                'success' => true,
                'message' => 'Вы успешно очистили права на всех активных серверах!'
            ]);
        }
    }

    public function clearSitePerms(Request $request){
        if (!\Auth::user()->isSuperAdmin() && !\Auth::user()->hasRole('admin')){
            return \Response::json([
                'success' => false,
                'message' => 'Вы не можете очищать права пользователя!'
            ]);
        }

        /* @var $user User */
        $user = User::findOrFail($request->get('user'));

        $user->roles()->detach();
        $user->permissions()->detach();

        $user->forgetCachedPermissions();

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно очистили права пользователя!'
        ]);
    }
    
    public function loadUserServerGroups(Request $request){
        $user = User::findOrFail($request->get('id'));
        
        $status = [];
        /* @var Server $server */
        foreach (Server::getActive() as $server){
            $pm = $server->getPermissionManager();
            
            if($pm instanceof MiniGamesPermissionManager){
                try{
                    $groups = $pm->playerGroups($user->uuid);
                    if ($groups){
                        $status[] = ['server' => $server->id, 'name' => $groups[0]];
                    }
                }catch (\Exception $e){}
            }
            
            try{
                $groups = $pm->playerGroups($user->uuid);
                foreach ($groups as $group){
                    $status[] = ['server' => $server->id, 'name' => $group->group];
                }
            }catch (\Exception $e){}
        }
        
        return \Response::json([
            'groups' => $status
        ]);
    }


    public function getTime(Request $request): CarbonPeriod
    {
        $range = $request->get('time');

        if ($range){
            $ex = explode(' — ', $range);
            if (count($ex) === 2){
                return CarbonPeriod::create($ex[0], $ex[1]);
            }
        }

        abort(500, 'Неверно указано время!');
    }

    public function giveDonateGroup(Request $request){
        if (Gate::denies('ext', ['manager', 'donate.give'])){
            abort(403, 'Недостаточно прав!');
        }

        $user = User::findOrFail($request->get('user'));
        /* @var DonateGroup $group */
        $group = DonateGroup::findOrFail($request->get('group'));
        $server = Server::findOrFail($request->get('server'));
        $period = $this->getTime($request);
    
        if (Gate::denies('ext', [$group, 'give'])){
            abort(403, 'Недостаточно прав!');
        }
        
        UserGroup::create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'server_id' => $server->id,
            'time' => $period->getStartDate()->getTimestamp(),
            'expire' => $period->getEndDate()->getTimestamp()
        ]);
        $server->getPermissionManager()->addUserToGroup($user->uuid, $group->pexname, $period->getEndDate()->getTimestamp());
        
        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно выдали группу!'
        ]);
    }
    
    public function removeDonateGroup(Request $request){
        if (Gate::denies('ext', ['manager', 'donate.revoke'])){
            abort(403, 'Недостаточно прав!');
        }

        /* @var UserGroup $ug */
        $ug = UserGroup::findOrFail($request->get('id'));
    
        if (Gate::denies('ext', [$ug->getDonateGroup(), 'revoke'])){
            abort(403, 'Недостаточно прав!');
        }
        
        $ug->getServer()->getPermissionManager()->removeUserFromGroup($ug->getUser()->uuid, $ug->getDonateGroup()->pexname);
        
        $ug->delete();
        
        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно сняли группу!'
        ]);
    }

    public function findItem(Request $request){
        $q = $request->get('q');
        
        $data = ShopItem::where('name', 'LIKE', '%' . $q . '%')->limit(100)->get(['id', 'name', 'type', 'damage']);

        $data = $data->map(function ($item, $key) {
            $item->icon = '/items/' . $item->type . ($item->damage ? '@' . $item->damage : '') . '.png';
            return $item;
        });
        
        return \Response::json([
            'data' => $data
        ]);
    }

    public function gameBan(Request $request){
        if (Gate::denies('ext', ['manager', 'ban_game.give'])){
            abort(403, 'Недостаточно прав!');
        }
        
        /* @var User $user */
        $user = User::findOrFail($request->get('user'));
        $server = Server::findOrFail($request->get('server'));

        $reason = $request->get('reason');
        $period = $this->getTime($request);

        \DB::table('ban_list')->insert([
            'UUID' => $user->uuid,
            'Reason' => $reason,
            'Time' => $period->getStartDate()->getTimestamp(),
            'Temptime' => $period->getEndDate()->getTimestamp(),
            'Type' => 0,
            'UUIDadmin' => \Auth::user()->uuid
        ]);

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно выдали бан в игре!'
        ]);
    }
    
    public function gameUnban(Request $request){
        if (Gate::denies('ext', ['manager', 'ban_game.delete'])){
            abort(403, 'Недостаточно прав!');
        }
        
        \DB::table('ban_list')->where('id', '=', $request->get('id'))->delete();
        
        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно сняли бан!'
        ]);
    }
    
    public function forumUnban(Request $request){
        if (Gate::denies('ext', ['manager', 'ban_site.delete'])){
            abort(403, 'Недостаточно прав!');
        }
        
        /* @var User $user */
        $user = User::findOrFail($request->get('user'));
        $user->unban();
        
        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно сняли ВСЕ баны с форума!'
        ]);
    }
    
    public function forumBan(Request $request){
        if (Gate::denies('ext', ['manager', 'ban_site.give'])){
            abort(403, 'Недостаточно прав!');
        }
        
        /* @var User $user */
        $user = User::findOrFail($request->get('user'));
        
        $reason = $request->get('reason');
        $period = $this->getTime($request);
        
        $user->ban([
            'comment' => $reason,
            'expired_at' => $period->getEndDate()
        ]);
        
        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно выдали бан на форуме!'
        ]);
    }

    public function giveCartItem(Request $request){
        if (Gate::denies('ext', ['manager', 'cart.give'])){
            abort(403, 'Недостаточно прав!');
        }
        
        /* @var User $user */
        $user = User::findOrFail($request->get('user'));
        $item = ShopItem::findOrFail($request->get('item'));

        $citem = $item->getCartItem();
        $citem->count = intval($request->get('count'));
        $citem->shop = 1;
        $citem->uuid = $user->uuid;

        $citem->save();

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно выдали предмет в корзину!'
        ]);
    }
    
    public function removeCartItem(Request $request){
        if (Gate::denies('ext', ['manager', 'cart.revoke'])){
            abort(403, 'Недостаточно прав!');
        }
        
        /* @var User $user */
        $item = CartItem::findOrFail($request->get('id'));
        $item->delete();
        
        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно удалили предмет!'
        ]);
    }

    public function giveKit(Request $request){
        if (Gate::denies('ext', ['manager', 'cart_kit.give'])){
            abort(403, 'Недостаточно прав!');
        }
        
        /* @var User $user */
        $user = User::findOrFail($request->get('user'));

        $citem = new CartItem;

        $citem->type = 'ESSENTIALS_KIT';
        $citem->damage = 0;
        $citem->count = 1;
        $citem->shop = 1;
        $citem->nbt = $request->get('name');
        $citem->uuid = $user->uuid;

        $citem->save();

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно кит в корзину!'
        ]);
    }
    
    public function updateBalance(Request $request){
        /* @var User $user */
        $user = User::findOrFail($request->get('user'));
        $realmoney = floatval($request->get('realmoney'));
        $money = floatval($request->get('money'));
        $rep = intval($request->get('reputation'));

        if ($realmoney != 0) {
            if ($realmoney > 0){
                if (Gate::denies('ext', ['manager', 'balance_real.give'])) abort(403, 'Недостаточно прав!');
            }else{
                if (Gate::denies('ext', ['manager', 'balance_real.revoke'])) abort(403, 'Недостаточно прав!');
            }

            if ($realmoney > 0) $user->addRealmoney($realmoney);
            else $user->withdrawRealmoney(-$realmoney);
        }
    
        if ($money != 0) {
            if ($money > 0){
                if (Gate::denies('ext', ['manager', 'balance_money.give'])) abort(403, 'Недостаточно прав!');
            }else{
                if (Gate::denies('ext', ['manager', 'balance_money.revoke'])) abort(403, 'Недостаточно прав!');
            }

            if ($money > 0) $user->addMoney($money);
            else $user->withdrawMoney(-$money);
        }
    
        if ($rep != 0){
            if ($rep > 0){
                if (Gate::denies('ext', ['manager', 'reputation.give'])) abort(403, 'Недостаточно прав!');
            }else{
                if (Gate::denies('ext', ['manager', 'reputation.revoke'])) abort(403, 'Недостаточно прав!');
            }

            $user->reputation += $rep;
            $user->save();
        }
        
        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно обновили игрока!'
        ]);
    }

    

    public function giveServerGroup(Request $request){
        if (Gate::denies('ext', ['manager', 'permissions.give'])){
            abort(403, 'Недостаточно прав!');
        }
        
        /* @var User $user */
        $user = User::findOrFail($request->get('user'));
        $group = $request->get('group');
        $period = $this->getTime($request);

        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));
        $server->getPermissionManager()->addUserToGroup($user->uuid, $group, $period->getEndDate()->getTimestamp());

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно выдали группу!'
        ]);
    }

    public function removeServerGroup(Request $request){
        if (Gate::denies('ext', ['manager', 'permissions.revoke'])){
            abort(403, 'Недостаточно прав!');
        }
        
        /* @var User $user */
        $user = User::findOrFail($request->get('user'));
        $group = $request->get('group');

        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));
        $server->getPermissionManager()->removeUserFromGroup($user->uuid, $group);

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно сняли группу!'
        ]);
    }
}