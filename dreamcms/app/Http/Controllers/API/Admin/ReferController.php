<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ReferController extends Controller
{
    const COLORS = ['#3c8dbc', '#00c0ef', '#00a65a', '#f39c12', '#f56954', '#d2d6de', '#001F3F', '#39CCCC', '#605ca8', '#ff851b', '#D81B60', '#111111',
        '#357ca5', '#00a7d0', '#008d4c', '#db8b0b', '#d33724', '#b5bbc8', '#001a35', '#30bbbb', '#555299', '#ff7701', '#ca195a', '#000000'];

    public function loadChart(Request $request){
        $chart = $request->get('chart');

        if ($request->has('time')){
            $time = $request->get('time');

            $start = Carbon::createFromTimestamp($time['start']);
            $end = Carbon::createFromTimestamp($time['end']);

            $period = CarbonPeriod::between($start, $end);
        }else{
            $period = CarbonPeriod::between(Carbon::now()->addDays(-13)->startOfDay(), Carbon::now()->endOfDay());
        }

        $interval = $period->getStartDate()->diffAsCarbonInterval($period->getEndDate());
        $days = $period->getStartDate()->diffInDays($period->getEndDate());

        $chart_data = [];

        $colors = collect(self::COLORS);

        $refer = \Auth::user()->id;

        if ($request->get('user') && \Gate::allows('ext', ['refer', 'other.view'])){
            $refer = User::find($request->get('user'))->id;
        }

        switch ($chart){
            case 'donate':
                $data = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(`time`), '%d.%m.%y') as dmy, SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.sum'))) as sum FROM `activity` WHERE action = 'unitpay_add' AND time > ? AND time < ? AND actor_id IN (SELECT id FROM users WHERE refer = ?) GROUP BY dmy ORDER BY `time`", [$period->getStartDate()->getTimestamp(), $period->getEndDate()->getTimestamp(), $refer]));

                $last_period = CarbonPeriod::between($period->getStartDate()->addDays(-$days), $period->getEndDate()->addDays(-$days));

                $data = $data->mapWithKeys(function ($item){ return [$item->dmy => $item->sum]; });

                $today = \DB::select("SELECT SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.sum'))) as sum FROM `activity` WHERE action = 'unitpay_add' AND time > ? AND actor_id IN (SELECT id FROM users WHERE refer = ?)", [Carbon::now()->startOfDay()->getTimestamp(), $refer]);

                $this_period = \DB::select("SELECT SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.sum'))) as sum FROM `activity` WHERE action = 'unitpay_add' AND time > ? AND time < ? AND actor_id IN (SELECT id FROM users WHERE refer = ?)", [$period->getStartDate()->getTimestamp(), $period->getEndDate()->getTimestamp(), $refer]);
                $last_period = \DB::select("SELECT SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.sum'))) as sum FROM `activity` WHERE action = 'unitpay_add' AND time > ? AND time < ? AND actor_id IN (SELECT id FROM users WHERE refer = ?)", [$last_period->getStartDate()->getTimestamp(), $last_period->getEndDate()->getTimestamp(), $refer]);

                $chart_data = [
                    'labels' => $data->keys()->toArray(),
                    'data1' => $data->values()->toArray(),
                    'today' => $today[0]->sum,
                    'this_period' => round($this_period[0]->sum, 2),
                    'last_period' => round($last_period[0]->sum, 2)
                ];
                break;
        }

        return \Response::json([
            'success' => true,
            'chart' => $chart_data
        ]);
    }

    function rgbcode($id){
        return '#'.substr(md5($id), 1, 6);
    }
}