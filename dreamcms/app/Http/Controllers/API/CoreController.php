<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\SpecialOffer;
use App\Models\User;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CoreController extends Controller{
    public function loadInfo(Request $request){
        /* @var User $user */
        $user = auth('api')->user();

        if (Cache::has('core_servers')){
            $servers = Cache::get('core_servers');
        }else{
            $servers = Server::getActive(['id', 'name', 'version', 'online', 'maxonline', 'ecomanager', 'donate']);
            Cache::set('core_servers', $servers, now()->addMinute());
        }

        if (Cache::has('integration_urls')){
            $integration_urls = Cache::get('integration_urls');
        }else{
            $integration_urls = collect();

            foreach (IntegrationController::$providers as $driver => $provider){
                if ($driver === 'telegram') continue;

                foreach (IntegrationController::$methods as $method){
                    $arr = $integration_urls->get($driver, []);
                    $arr[$method] = IntegrationController::getRedirectURL($driver, $method);
                    $integration_urls->put($driver, $arr);
                }
            }

            $integration_urls = $integration_urls->toArray();

            Cache::set('integration_urls', $integration_urls, now()->addHour());
        }

        $record = Cache::remember('record', 60, function (){
            $key = Carbon::now()->toDateString();
            return Cache::store('global')->get('record_' . $key, 0);
        });

        $global_data = [
            'record' => $record,
            'servers' => $servers,
            'integration_urls' => $integration_urls,
        ];

        if ($user && Cache::has('core_user_' . $user->id)){
            $user_data = Cache::get('core_user_' . $user->id);
        }else{
            $user_data = [
                'user' => $user ? $user : null,
                'role' => $user ? $user->getSiteRole() : "Игрок",
                'roles' => $user ? $user->getAllRoles()->pluck('name') : [],
                'permissions'=> $user ? $user->getAllPermissions()->pluck('name') : [],
                'dgroups' => $user ? $user->getActiveGroups() : [],
                'unreadNotifications' => $user ? $user->unreadNotifications()->count() : 0,
                'bans' => $user ? $user->getServerBans() : [],
                'otp' => $user && $user->otp_secret ? true : false
            ];
        }

        if ($user)
            \Cache::set('core_user_' . $user->id, $user_data, now()->addMinute());

        $data = array_merge($user_data, $global_data);

        return \Response::json($data);
    }

    public function ip(Request $request){
        return \Response::make($request->ip() . ' | ' . $request->userAgent());
    }

    public function findUser(Request $request){
        $login = $request->get('login');
        if($login){
            $user = User::where('login', $login)->first(['id', 'login', 'uuid']);

            if ($user){
                return \Response::json([
                    'success' => true,
                    'user' => $user
                ]);
            }

            return \Response::json([
                'success' => false
            ]);
        }

        $q = $request->get('q');

        $data = User::where('login', 'LIKE', '%' . $q . '%')->limit(10)->get(['id', 'uuid', 'login']);
        $user = User::where('login', '=', $q)->get(['id', 'uuid', 'login'])->first();

        if($user) $data->prepend($user);

        return \Response::json([
            'data' => $data
        ]);
    }

    public function getNotifications(Request $request){
        /* @var User $user */
        $user = auth('api')->user();

        $unread = $user->unreadNotifications()->get();
        $notifications = $user->readNotifications()->paginate(15);

        $user->notifications->markAsRead();

        \Cache::delete('core_user_' . $user->id);

        return \Response::json([
            'success' => true,
            'unread' => $unread,
            'notifications' => $notifications,
        ]);
    }

    public function getOffers(Request $request){
        if (!\Auth::user()){
            return [
                'success' => true,
                'offers' => []
            ];
        }

        return [
            'success' => true,
            'offers' => SpecialOffer::forUser(\Auth::user())
        ];
    }
}