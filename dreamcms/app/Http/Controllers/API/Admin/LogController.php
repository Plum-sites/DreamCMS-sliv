<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Server;
use App\Models\Shop;
use App\Models\ShopItem;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Models\Audit;

class LogController extends Controller{

    //TODO

    public function userLogs(Request $request){
        if (Gate::denies('ext', ['admin', 'logs.access'])){
            abort(403, 'Недостаточно прав для просмотра!');
        }

        $actions = [];

        $range = $request->get('range');

        $start = Carbon::now()->subMonths(3)->subRealDays(14)->startOfDay();
        $end = Carbon::now()->subMonths(3)->endOfDay();
        $period = CarbonPeriod::create($start, $end);

        if ($range){
            $ex = explode(' — ', $range);
            if (count($ex) === 2){
                $period = CarbonPeriod::create($ex[0], $ex[1]);
                $start = $period->start->startOfDay();
                $end = $period->end->endOfDay();
            }
        }

        $actions = [];

        if ($request->get('user')){
            $user = User::findOrFail($request->get('user'));

            $actions = Activity::where([
                ['time', '>', $start->getTimestamp()],
                ['time', '<', $end->getTimestamp()],
                ['actor_id', '=',$user->id],
                ['actor_type', '=', 'user']
            ])->orderBy('time', 'DESC')->get();
        }elseif ($request->get('type')){
            $actions = Activity::where([
                ['time', '>', $start->getTimestamp()],
                ['time', '<', $end->getTimestamp()],
                ['action', '=', $request->get('type')],
                ['actor_type', '=', 'user']
            ])->orderBy('time', 'DESC')->get();
        }

        return \Response::json(['actions' => $this->processActions($actions)]);
    }

    public function processActions($actions){
        /* @var Collection $actions */

        $actions->map(function ($action){
            try {
                $params = json_decode($action->params);

                $action->params = $params;

                if ($params->ip){
                    $action->ip = geoip($params->ip)->toArray();
                }

                switch ($action->action){
                    case 'api_withdraw':
                        $server = Server::where('api_token', $params->token)->first();
                        $action->server = $server ? $server->only('id', 'name') : null;
                        break;
                    case 'banned_forum':
                        $action->admin = User::find($params->admin, ['id', 'login']);
                        break;
                    case 'sendserver':
                    case 'buygroup':
                    case 'setprefix':
                        $action->server = Server::find($params->server, ['id', 'name']);
                        break;
                    case 'buyitem':
                        $action->server = Shop::find($params->shop, ['name']);
                        break;
                }
            }catch (\Throwable $throwable){}

            return $action;
        });

        return $actions;
    }

    public function adminLogs(Request $request){
        if (Gate::denies('ext', ['admin', 'logs.access'])){
            abort(403, 'Недостаточно прав для просмотра!');
        }

        $time = $request->get('time');
        $start = Carbon::createFromTimestamp($time['start']);
        $end = Carbon::createFromTimestamp($time['end']);

        if ($request->has('user')){
            $user = User::fromLogin($request->get('user'));

            if (!$user)
                return abort(404, 'Пользователь не найден!');

            $actions = Audit::orderBy('time', 'DESC')->where([
                ['time', '>', $start->getTimestamp()],
                ['time', '<', $end->getTimestamp()],
                ['user_id', '=', $user->id]
            ])->get();
        }elseif ($request->has('type')){
            $actions = Audit::orderBy('id', 'DESC')->where([
                ['time', '>', $start->getTimestamp()],
                ['time', '<', $end->getTimestamp()],
                ['auditable_type', '=', $request->get('type')]
            ])->get();
        }else{
            $actions = Audit::orderBy('id', 'DESC')->where([
                ['time', '>', $start->getTimestamp()],
                ['time', '<', $end->getTimestamp()]
            ])->get();
        }

        return \Response::json(['actions' => $actions]);
    }
}