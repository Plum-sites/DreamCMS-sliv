<?php

namespace App\Http\Controllers\API;

use App\Events\BuyGroupEvent;
use App\Events\BuyKitEvent;
use App\Events\ExchangeCoinsEvent;
use App\Events\SendCoinsToServerEvent;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\CaseChest;
use App\Models\DonateGroup;
use App\Models\Forum\Post;
use App\Models\Kit;
use App\Models\Server;
use App\Models\SpecialOffer;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\VerificatedTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function loadKits(){
        return ['success' => true, 'kits' => []];
    }

    public function index(Request $request){
        $groups = DonateGroup::getActive();

        /* @var $user User */
        $user = Auth::user();

        foreach ($groups as $group){
            /*$offer = SpecialOffer::getOffer($user, 'GROUP', ['group' => $group->id]);

            if ($offer){
                $group->oldprice = $group->price;
                $group->price = $group->price * ((100 - $offer->discount)/100);
                continue;
            }*/

            if ($group->discount > 0 && in_datarange($group->discount_start, $group->discount_end)){
                $group->oldprice = $group->price;
                $group->price = $group->price * ((100 - $group->discount)/100);
            }
        }

        $active = UserGroup::with(['group', 'server'])->where([
            ['user_id', '=', $user->id],
            ['time', '<', time()],
            ['expire', '>', time()]
        ])->get();

        return [
            'success' => true,
            'groups'  => $groups,
            'active_groups' => $active
        ];
    }

    public function refer(Request $request){
        $refers = User::where('refer', Auth::id())->get();
        return view('profile.refer', ['refers' => $refers]);
    }

    public function buyKit(Request $request){
        $kit_name = $request->get('kit');

        $kit = Kit::where('name', '=', $kit_name)->first()->get();

        $price = $kit->price;

        if (Auth::user()->withdrawRealmoney($price)){
            DonateGroup::giveKit(Auth::user(), $kit->server_name);

            Activity::user_action(Auth::user(), 'buy_kit',[
                'kit' => $kit->only(['id', 'name', 'price'])
            ]);

            event(new BuyKitEvent(Auth::user(), $kit_name));

            return Response::json([
                'success' => true,
                'message' => 'Кит выдан в вашу корзину (/cart)!'
            ]);
        }else{
            return Response::json([
                'success' => false,
                'message' => 'Недостаточно средств!'
            ]);
        }
    }

    public function view(Request $request, $login){
        $user = User::fromLogin($login);

        if ($user){
            $offset = 0;
            if ($page = $request->get('page')){
                $offset = ($page - 1) * 10;
                if ($offset < 0) $offset = 0;
                else return Response::json([
                    'profile' => [
                        'actions' => \DB::select("SELECT chatter_discussion.title, chatter_discussion.slug, chatter_post.body, chatter_post.created_at FROM chatter_post JOIN chatter_discussion ON chatter_discussion.id = chatter_post.chatter_discussion_id WHERE chatter_post.user_id = ? ORDER BY chatter_post.id DESC LIMIT 10 OFFSET ?;", [$user->id, $offset])
                    ]
                ]);
            }

            $chars = \Cache::get('profile_chars_' . $user->id, 0);

            if (!$chars){
                $chars = intval(\DB::selectOne('SELECT SUM(LENGTH(body)) as count FROM chatter_post WHERE user_id = ?', [$user->id])->count);
                \Cache::set('profile_chars_' . $user->id, $chars, 60 * 60 * 12);
            }

            if (\Cache::has('user_post_count_' . $user->id)){
                $posts_count = \Cache::get('user_post_count_' . $user->id);
            }else{
                $posts_count = \DB::select('SELECT COUNT(*) as count FROM chatter_post WHERE chatter_post.user_id = ? AND chatter_post.chatter_discussion_id NOT IN (SELECT chatter_discussion.id FROM chatter_discussion WHERE chatter_discussion.chatter_category_id IN (SELECT chatter_categories.id FROM chatter_categories WHERE chatter_categories.not_count = 1))', [$user->id])[0]->count;
                \Cache::set('user_post_count_' . $user->id, $posts_count, 60);
            }

            return Response::json([
                'profile' => [
                    'user' => $user->only(['id', 'login', 'uuid', 'reg_time', 'reputation', 'last_play']),
                    'role' => $user->getSiteRole(),
                    'banned' => $user->isBanned(),
                    'game_banned' => $user->getServerBans()->count(),
                    'hide_friends' => $user->hide_friends,
                    'friends' => $user->hide_friends && !(Auth::user() && Auth::user()->id === $user->id)  ? [] : $user->getFriends()->map->only('id', 'login', 'uuid'),
                    'has_friend_request' => Auth::user() ? $user->hasFriendRequestFrom(Auth::user()) : false,
                    'has_sent_friend_request' => Auth::user() ? $user->hasSentFriendRequestTo(Auth::user()) : false,
                    'posts' => $posts_count,
                    'chars' => $chars,
                    'discussions' => intval(\DB::select('SELECT COUNT(DISTINCT chatter_discussion_id) as count FROM chatter_post WHERE user_id = ?', [$user->id])[0]->count),
                    'actions' => \DB::select("SELECT chatter_discussion.title, chatter_discussion.slug, chatter_post.body, chatter_post.created_at FROM chatter_post JOIN chatter_discussion ON chatter_discussion.id = chatter_post.chatter_discussion_id WHERE chatter_post.user_id = ? ORDER BY chatter_post.id DESC LIMIT 10;", [$user->id])
                ]
            ]);
        }
    }

    public function pay(Request $request){
        $user = User::where('login', $request->get('username'))->get()->first();
        $server = $request->get('server');
        $sum = $request->get('sum');
        $url = 'https://unitpay.ru/pay/' . config('settings.unitpay_publickey', 'demo') . '?account=' . $user->uuid . '&sum=' . $sum . '&desc=Пополнение баланса игрока ' . $user->login;
        return redirect($url);
    }

    public function loadBans(Request $request){
        $user = Auth::user();

        $bans = $user->getServerBans();

        $logs = \DB::table('punish_logs')
            ->where('UUID', '=', $user->uuid)
            ->whereIn('type', ['ban', 'permban', 'mute', 'kick'])
            ->orderByDesc('id')
            ->get()->map(function ($item){
            $admin = User::fromUUID($item->UUIDAdmin);
            $item->admin = $admin ? $admin->only(['id', 'login']) : ['id' => 0, 'login' => 'Server'];

            if ($item->TempTime){
                $item->TempTime = $item->TempTime > 1500000000 ? $item->TempTime : ($item->Time + $item->TempTime);
            }
            return $item;
        });

        return Response::json([
            'success' => true,
            'bans' => $bans,
            'punish_logs' => $logs
        ]);
    }

    public function unban(Request $request){
        $user = Auth::user();

        $ban_id = $request->get('ban');
        if ($ban_id){
            $ban = $user->getServerBans()->filter(function ($ban) use ($ban_id){
                return $ban->id == $ban_id;
            })->first();
        }else{
            $ban = $user->getServerBans()->first();
        }

        if ($ban->price && $user->withdrawRealmoney($ban->price)){
            Activity::user_action($user, 'buy_unban',[
                'ban' => $ban
            ]);

            $user->clearCache();

            \DB::table('ban_list')->where('id', $ban->id)->delete();

            return Response::json([
                'success' => true,
                'message' => 'Вы успешно разбанены!'
            ]);
        }else{
            return Response::json([
                'success' => false,
                'message' => 'Недостаточно средств!'
            ]);
        }
    }

    public function banlist(Request $request){
        $search = $request->get('search');
        $subject = $request->get('subject');

        if ($search){
            $list = false;

            switch ($subject){
                case 'user':
                    $user = User::fromLogin($search);
                    if ($user)
                        $list = \DB::table(\DB::raw('ban_list as banlist'))
                            ->select(\DB::raw('users.uuid, users.login, usersAdmin.login as admin, usersAdmin.uuid as adminUUID, banlist.Reason, banlist.Time, banlist.Temptime'))

                            ->join(\DB::raw('users as users'), \DB::raw('users.uuid'), '=', \DB::raw('banlist.UUID'))
                            ->join(\DB::raw('users as usersAdmin'), \DB::raw('usersAdmin.uuid'), '=', \DB::raw('banlist.UUIDadmin'))

                            ->where('banlist.UUID', $user->uuid)

                            ->orderBy('banlist.Time', 'desc')
                            ->paginate(10);
                    break;
                case 'moder':
                    $user = User::fromLogin($search);
                    if ($user)
                        $list = \DB::table(\DB::raw('ban_list as banlist'))
                            ->select(\DB::raw('users.uuid, users.login, usersAdmin.login as admin, usersAdmin.uuid as adminUUID, banlist.Reason, banlist.Time, banlist.Temptime'))

                            ->join(\DB::raw('users as users'), \DB::raw('users.uuid'), '=', \DB::raw('banlist.UUID'))
                            ->join(\DB::raw('users as usersAdmin'), \DB::raw('usersAdmin.uuid'), '=', \DB::raw('banlist.UUIDadmin'))

                            ->where('banlist.UUIDAdmin', $user->uuid)

                            ->orderBy('banlist.Time', 'desc')
                            ->paginate(10);
                    break;
                case 'reason':
                    $list = \DB::table(\DB::raw('ban_list as banlist'))
                        ->select(\DB::raw('users.uuid, users.login, usersAdmin.login as admin, usersAdmin.uuid as adminUUID, banlist.Reason, banlist.Time, banlist.Temptime'))

                        ->join(\DB::raw('users as users'), \DB::raw('users.uuid'), '=', \DB::raw('banlist.UUID'))
                        ->join(\DB::raw('users as usersAdmin'), \DB::raw('usersAdmin.uuid'), '=', \DB::raw('banlist.UUIDadmin'))

                        ->where('banlist.Reason', $search)

                        ->orderBy('banlist.Time', 'desc')
                        ->paginate(10);
                    break;
            }

            if ($list){
                return Response::json([
                    'success' => true,
                    'bans' => $list
                ]);
            }else{
                return Response::json([
                    'success' => true,
                    'bans' => []
                ]);
            }
        }else{
            $list = \DB::table(\DB::raw('ban_list as banlist'))
                ->select(\DB::raw('users.uuid, users.login, usersAdmin.login as admin, usersAdmin.uuid as adminUUID, banlist.Reason, banlist.Time, banlist.Temptime'))

                ->join(\DB::raw('users as users'), \DB::raw('users.uuid'), '=', \DB::raw('banlist.UUID'))
                ->join(\DB::raw('users as usersAdmin'), \DB::raw('usersAdmin.uuid'), '=', \DB::raw('banlist.UUIDadmin'))

                ->orderBy('banlist.Time', 'desc')
                ->paginate(10);

            return Response::json([
                'bans' => $list
            ]);
        }
    }

    public function stp(Request $request){
        if (\Cache::has('stp_cooldown_' . Auth::user()->id)){
            return Response::json([
                'success' => false,
                'message' => 'Вы не можете пользоваться этой функцией чаще чем 1 раз в 5 минут!'
            ]);
        }

        try{
            /* @var Server $server */
            $server = Server::findOrFail($request->get('server'));
            $server->sendCommand('stp ' . Auth::user()->login);

            \Cache::set('stp_cooldown_' . Auth::user()->id, 1, 5 * 60);
        }catch (\Throwable $exception){
            return Response::json([
                'success' => false,
                'message' => 'Сервер не отвечает :( Попробуйте вновь через 5 минут.'
            ]);
        }

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно телепортированы на спавн!'
        ]);
    }

    public function delprefix(Request $request){
        if(!Auth::user()->hasPermissionTo('prefix.text.delete')) {
            return Response::json(array(
                'success' => false,
                'message' => 'Вы не можете стирать свой префикс!'
            ));
        }

        Server::getActive()->each(function ($item, $key){
            $item->getPermissionManager()->removeUserPrefix(Auth::user()->uuid);
            $item->getPermissionManager()->removeUserSuffix(Auth::user()->uuid);
        });

        return Response::json(array(
            'success' => true,
            'message' => 'Вы успешно очистили свой префикс!'
        ));
    }

    public function prefix(Request $request){
        $server = Server::findOrFail($request->get('server'));

        if(Auth::user()->getActiveGroups($server->id)->count() <= 0) {
            return Response::json(array(
                'success' => false,
                'message' => 'Вы не можете устанавливать префикс на этом сервере!'
            ));
        }

        $colors = array_keys(colorArray());

        $text   = $request->get('text');

        $pcolor = '8';
        $ncolor = '7';
        $mcolor = 'f';

        if(!Auth::user()->hasPermissionTo('prefix.prefix_color.edit')){
            if (Auth::user()->hasRole('vip')) $pcolor = '6';
            if (Auth::user()->hasRole('premium')) $pcolor = '3';
            if (Auth::user()->hasRole('deluxe')) $pcolor = 'a';
            if (Auth::user()->hasRole('legend')) $pcolor = '5';
        }else{
            if (!$pcolor = $request->get('prefix_color')) $pcolor = '8';
            if (!in_array($pcolor, $colors)){
                return Response::json(array(
                    'success' => false,
                    'message' => 'Цвет [&' . $pcolor . '] запрещен!'
                ));
            }
        }

        if(Auth::user()->hasPermissionTo('prefix.nick_color.edit')){
            if (!$ncolor = $request->get('nick_color')) $ncolor = '7';
            if (!in_array($ncolor, $colors)){
                return Response::json(array(
                    'success' => false,
                    'message' => 'Цвет [&' . $ncolor . '] запрещен!'
                ));
            }
        }

        if(Auth::user()->hasPermissionTo('prefix.msg_color.edit')){
            if (!$mcolor = $request->get('msg_color')) $mcolor = 'f';
            if (!in_array($mcolor, $colors)){
                return Response::json(array(
                    'success' => false,
                    'message' => 'Цвет [&' . $mcolor . '] запрещен!'
                ));
            }
        }

        if (mb_strlen($text) > 12){
            return Response::json(array(
                'success' => false,
                'message' => 'Текст префикса должен быть не более 10 символов!'
            ));
        }

        $restrict = collect(explode(',', config('settings.prefix_restrict', 'moder,moderator,admin,модер,админ')));
        $restrict->push('%');
        $restrict->push('&');
        $restrict->push('§');

        if(Str::contains(strtolower($text), $restrict->toArray())){
            return Response::json(array(
                'success' => false,
                'message' => 'В тексте префикса обнаружены запрещенные слова или символы!'
            ));
        }

        if(!Auth::user()->hasPermissionTo('prefix.text.edit')){
            $text = strip_tags(Auth::user()->getDonateRole());
        }

        $prefix = '&8[&' . $pcolor . $text .'&8]&' . $ncolor;
        $suffix = '&' . $mcolor;

        Activity::user_action(Auth::user(), 'setprefix', ['prefix' => $prefix, 'suffix' => $suffix, 'server' => $server ? $server->id : 'all']);

        if($server){
            $server->getPermissionManager()->setUserPrefix(Auth::user(), $prefix);
            $server->getPermissionManager()->setUserSuffix(Auth::user(), $suffix);
        }else{
            Server::getActive()->each(function ($item, $key) use ($prefix, $suffix){
                $item->getPermissionManager()->setUserPrefix(Auth::user(), $prefix);
                $item->getPermissionManager()->setUserSuffix(Auth::user(), $suffix);
            });
        }

        return Response::json(array(
            'success' => true,
            'message' => 'Вы успешно обновили свой префикс!'
        ));
    }

    public function buygroup(Request $request)
    {
        $server = $request->get('server');
        $group = $request->get('group');
        $server = Server::findOrFail($server);

        $allgroups = Auth::user()->getAllGroups($server->id);
        /* @var DonateGroup $group */
        $group = DonateGroup::findOrFail($group);

        $discount = false;
        $orig_price = $group->price;

        $offer = SpecialOffer::getOffer(Auth::user(), 'GROUP', ['group' => $group->id]);
        if ($offer){
            $group->price = $group->price * ((100 - $offer->discount)/100);
        }

        if ($group->discount > 0 && in_datarange($group->discount_start, $group->discount_end)){
            $group->price = $group->price * ((100 - $group->discount)/100);
            $discount = true;
        }

        if(Auth::user()->realmoney < $group->price){
            return Response::json(array(
                'success' => false,
                'message' => 'Недостаточно ' . ($group->price - Auth::user()->realmoney) . ' стримов для покупки!'
            ));
        }

        if ($discount){
            $actions = Activity::list_actions(Auth::user(), 'buygroup', [
                ['time', '>', strtotime($group->discount_start)]
            ]);

            $allcount = 0;

            foreach ($actions as $action){
                $params = json_decode($action->params);

                if ($params->price < $orig_price){
                    $allcount++;

                    if ($params->server === $server->id){
                        $dg_has = DonateGroup::where('name', $params->group)->first();
                        if ($dg_has->sort >= $group->sort){
                            return Response::json(array(
                                'success' => false,
                                'message' => 'Донат-группы на скидках можно приобрести не более одной на каждый сервер, либо группу выше!'
                            ));
                        }
                    }
                }
            }

            if ($allcount >= 10){
                return Response::json(array(
                    'success' => false,
                    'message' => 'Донат-группы на скидках нельзя приобрести более 10 раз!'
                ));
            }
        }

        try {
            Auth::user()->clearCache();
        }catch (\Throwable $exception){}

        if($allgroups->count() <= 0){
            $group->buy(Auth::user(), $server->id);

            if ($offer)
                SpecialOffer::useOffer(Auth::user(), $offer);

            event(new BuyGroupEvent(Auth::user(), $group, $server));

            return Response::json(array(
                'success' => true,
                'message' => 'Вы успешно приобрели группу ' . $group->name . '!'
            ));
        }else{
            $renewal = false;

            $allgroups->each(function ($item, $key) use ($group, &$renewal){
                /* @var UserGroup $item */
                if ($item->getDonateGroup()->id == $group->id) $renewal = true;
            });

            if($renewal){
                $group->buy(Auth::user(), $server->id);

                if ($offer)
                    SpecialOffer::useOffer(Auth::user(), $offer);

                event(new BuyGroupEvent(Auth::user(), $group, $server, true));

                return Response::json(array(
                    'success' => true,
                    'message' => 'Вы успешно продлили группу ' . $group->name . '!'
                ));
            }else{
                $group->buy(Auth::user(), $server->id);

                if ($offer)
                    SpecialOffer::useOffer(Auth::user(), $offer);

                event(new BuyGroupEvent(Auth::user(), $group, $server, false, true));

                return Response::json(array(
                    'success' => true,
                    'message' => 'Вы успешно приобрели группу ' . $group->name . '!'
                ));
            }
        }

    }

    public function exchange(Request $request){
        $count = intval($request->get('count'));

        if ($count > 0 && $count < 5000){
            if (Auth::user()->withdrawRealmoney($count)){
                Activity::user_action(Auth::user(), 'exchange', [
                    'count' => $count,
                    'course' => config('settings.exchange_course')
                ]);

                Auth::user()->addMoney($count * config('settings.exchange_course'));

                event(new ExchangeCoinsEvent(Auth::user(), $count));

                return Response::json(array(
                    'success' => true,
                    'message' => 'Вы успешно обменяли '. $count .' рублей на ' . ($count * config('settings.exchange_course')) . ' монет!'
                ));
            }else return Response::json(array(
                'success' => false,
                'message' => 'Недостаточно суммы на вашем балансе!'
            ));
        }else{
            return Response::json(array(
                'success' => false,
                'message' => 'Введите кол-во от 1 до 5000!'
            ));
        }
    }

    public function sendMoney(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        if ($request->has('count')){
            $count = $request->get('count');

            if ($count > 0 && Auth::user()->withdrawRealmoney($count)){
                $server->getEconomyManager()->addMoney(Auth::user(), $count * 100);

                Activity::user_action(Auth::user(), 'sendserver', [
                    'count' => $count,
                    'server' => $server->id
                ]);

                event(new SendCoinsToServerEvent(Auth::user(), $server, $count));

                return Response::json(array(
                    'success' => true,
                    'message' => 'Вы успешно перевели ' . $count . ' коинов на сервер ' . $server->name . '!'
                ));
            }

            return Response::json(array(
                'success' => false,
                'message' => 'Недостаточно стримов!'
            ));
        }else{
            $count = Auth::user()->money;

            if ($count > 0 && Auth::user()->withdrawMoney($count)){
                $server->getEconomyManager()->addMoney(Auth::user(), $count);

                Activity::user_action(Auth::user(), 'sendserver', [
                    'count' => $count,
                    'server' => $server->id
                ]);

                return Response::json(array(
                    'success' => true,
                    'message' => 'Вы успешно перевели ' . $count . ' коинов на сервер ' . $server->name . '!'
                ));
            }

            return Response::json(array(
                'success' => false,
                'message' => 'Недостаточно коинов!'
            ));
        }
    }

    public function sendplayer(Request $request){
        if(!Auth::user()->hasPermissionTo('money.money.send')){
            return Response::json(array(
                'success' => false,
                'message' => 'Вы не можете пересылать деньги между игроками!'
            ));
        }

        $login = $request->get('player');
        $count = intval($request->get('count'));

        if($count <= 0 || $count > 1000){
            return Response::json(array(
                'success' => false,
                'message' => 'Введите кол-во от 1 до 1000!'
            ));
        }

        $user = User::where('login', $login)->get()->first();
 
        if ($user){
            if ($user != Auth::user()){
                if (!$user->hasPermissionTo('money.money.recieve')){
                    return Response::json(array(
                        'success' => false,
                        'message' => 'Этот игрок пока не может принимать деньги!'
                    ));
                }

                if (Auth::user()->withdrawRealmoney($count)){
                    if(config('settings.verify_moneysend', true)){
                        Auth::user()->addRealmoney($count);

                        VerificatedTask::createTask(Auth::user(), 'email', 'send_realmoney', ['sum' => $count, 'recipient' =>  $user->id, 'comment' => $request->get('comment')]);

                        return Response::json(array(
                            'success' => true,
                            'message' => 'На вашу почту отправлено письмо с подтверждением перевода!'
                        ));
                    }

                    $user->addRealmoney($count);
                    return Response::json(array(
                        'success' => true,
                        'message' => 'Вы успешно перевели игроку ' . $user->login . ' ' . $count . ' рублей!'
                    ));
                }else return Response::json(array(
                    'success' => false,
                    'message' => 'Для перевода необходимо ' . round($count + ($count * 0.15)) . ' рублей на счету!'
                ));
            }else{
                return Response::json(array(
                    'success' => false,
                    'message' => 'Вы не можете переводить деньги самому себе!'
                ));
            }
        }else{
            return Response::json(array(
                'success' => false,
                'message' => 'Такого игрока не существует!'
            ));
        }
    }
}
