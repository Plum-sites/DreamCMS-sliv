<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Gate;
use Illuminate\Http\Request;

class OnlineController extends Controller{

    public function __construct()
    {
        ini_set('memory_limit', '-1');
    
        $this->middleware(function ($request, $next)
        {
            if (Gate::denies('ext', ['admin', 'online.access'])){
                abort(403, 'Недостаточно прав!');
            }
        
            return $next($request);
        });
    }

    public function loadServers(Request $request){
        return \Response::json([
            'servers' => \DB::table('playtime')->selectRaw('DISTINCT `server`')->get()
        ]);
    }

    private static function generateChart($xAxis, $series, $info = [])
    {
        return array_merge([
            'chartOptions' => [
                'xaxis' => [
                    'categories' => $xAxis
                ],
                'labels' => $xAxis
            ],
            'series' => $series
        ], $info);
    }

    public function loadChart(Request $request){
        $chart = $request->get('chart');
        $user = User::find($request->get('user'));
        $server = $request->get('server');

        $range = $request->get('time');

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

        $period = CarbonPeriod::between($start, $end);
    
        $days = collect();
        
        foreach ($period as $day){
            $days->push($day->format('d.m.Y'));
        }
        
        $playtime = \DB::table('playtime')->whereIn('day', $days);
        
        if ($user) $playtime = $playtime->where('uuid', '=', $user->uuid);
        if ($server) $playtime = $playtime->where('server', '=', $server);
    
        $playtime = $playtime->get();
        
        $rows = collect();
        foreach ($playtime as $userday){
            try{
                $userday->sessions = json_decode($userday->sessions, false, 1024);
                if ($userday->sessions != null){
                    $rows->push($userday);
                }else{}
            }catch (\Throwable $e){}
        }
        
        //Server - time
        if ($chart == 'server_time'){
            $data = collect();
    
            foreach ($rows as $row){
                $time = $data->get($row->server, 0);
                foreach ($row->sessions as $session){
                    try{
                        if($session->end <= 0) $session->end = now();
                        $time += ($session->end - $session->start);
                    }catch (\Throwable $e){}
                }
                $data->put($row->server, $time);
            }
    
            $data = $data->map(function($time, $key){
                return round($time / 60 / 60, 2);
            });

            return \Response::json([
                'success' => true,
                'chart' => self::generateChart($data->keys()->toArray(), $data->values()->toArray())
            ]);
        }
    
        //User - time on server
        if ($server && $chart == 'server_user_time'){
            $data = collect();
            
            foreach ($rows as $row){
                $user2 = User::fromUUID($row->uuid);
                
                $time = $data->get($user2->login, 0);
    
                foreach ($row->sessions as $session){
                    try{
                        if($session->end <= 0) $session->end = now();
                        $time += ($session->end - $session->start);
                    }catch (\Throwable $e){}
                }
                
                $data->put($user2->login, $time);
            }
    
            $data = $data->map(function($time, $key){
                return round($time / 60 / 60, 2);
            });

            return \Response::json([
                'success' => true,
                'chart' => self::generateChart($data->keys()->toArray(), $data->values()->toArray())
            ]);
        }
    
        //Server - time on user
        if ($user && $chart == 'user_server_time'){
            $data = collect();
            foreach ($rows as $row){
                $time = $data->get($row->server, 0);
                foreach ($row->sessions as $session){
                    try{
                        if($session->end <= 0) $session->end = now();
                        $time += ($session->end - $session->start);
                    }catch (\Throwable $e){}
                }
                $data->put($row->server, $time);
            }
    
            $data = $data->map(function($time, $key){
                return round($time / 60 / 60, 2);
            });

            return \Response::json([
                'success' => true,
                'chart' => self::generateChart($data->keys()->toArray(), $data->values()->toArray())
            ]);
        }
        
        return \Response::json([
            'success' => false
        ]);
    }
}