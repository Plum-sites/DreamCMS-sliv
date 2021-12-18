<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Forum\Post;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    const COLORS = ['#3c8dbc', '#00c0ef', '#00a65a', '#f39c12', '#f56954', '#d2d6de', '#001F3F', '#39CCCC', '#605ca8', '#ff851b', '#D81B60', '#111111',
        '#357ca5', '#00a7d0', '#008d4c', '#db8b0b', '#d33724', '#b5bbc8', '#001a35', '#30bbbb', '#555299', '#ff7701', '#ca195a', '#000000'];

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

    public function topBuys(Request $request){
        if (\Gate::denies('ext', ['dashboard_profit', 'top_buys.view'])) abort(403);

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

        $data = collect(\DB::select("SELECT shop_items.id, shop_items.name, shop_items.icon, shop_items.type, shop_items.damage, COUNT(activity.id) as count, JSON_UNQUOTE(JSON_EXTRACT(params, '$.item')) as item_id, SUM(IFNULL(JSON_UNQUOTE(JSON_EXTRACT(params, '$.price')), 0)) as sum FROM activity JOIN shop_items ON shop_items.id = JSON_UNQUOTE(JSON_EXTRACT(params, '$.item')) WHERE action = 'buyitem' AND time > ? AND time < ? GROUP BY shop_items.id ORDER BY SUM(IFNULL(JSON_UNQUOTE(JSON_EXTRACT(params, '$.price')), 0)) DESC LIMIT 10", [$start->getTimestamp(), $end->getTimestamp()]));
        $data = $data->map(function ($item){
            $item->icon = '/items/' . $item->type . ($item->damage ? '@' . $item->damage : '') . '.png';
            return $item;
        });

        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function loadStats(Request $request){
        if(\Auth::user()->hasPermissionTo('admin.dashboard_profit.access')){
            return [
                'success' => true,
                'stats' => [
                    [
                        'icon' => 'TrendingUpIcon',
                        'color' => 'light-primary',
                        'title' => \DB::selectOne("SELECT COUNT(DISTINCT actor_id) as count FROM activity WHERE action IN ('unitpay_add', 'enot_add') AND time > ?", [ Carbon::now()->startOfDay()->getTimestamp() ])->count,
                        'subtitle' => 'Пополнили счет',
                        'customClass' => 'mb-2 mb-xl-0',
                    ],
                    [
                        'icon' => 'UserIcon',
                        'color' => 'light-info',
                        'title' => \DB::selectOne("SELECT COUNT(DISTINCT actor_id) as count FROM activity WHERE action IN ('buygroup', 'buyitem') AND time > ?", [ Carbon::now()->startOfDay()->getTimestamp() ])->count,
                        'subtitle' => 'Уникальных покупателей',
                        'customClass' => 'mb-2 mb-xl-0',
                    ],
                    [
                        'icon' => 'BoxIcon',
                        'color' => 'light-danger',
                        'title' => \DB::selectOne("SELECT COUNT(id) as count FROM activity WHERE action IN ('buygroup', 'buyitem') AND time > ?", [ Carbon::now()->startOfDay()->getTimestamp() ])->count,
                        'subtitle' => 'Продаж',
                        'customClass' => 'mb-2 mb-xl-0',
                    ],
                    [
                        'icon' => 'DollarSignIcon',
                        'color' => 'light-success',
                        'title' => \DB::selectOne("SELECT IFNULL(SUM(IFNULL(JSON_UNQUOTE(JSON_EXTRACT(params, '$.price')), 0)), 0) as count FROM activity WHERE action IN ('buygroup', 'buyitem') AND time > ?", [ Carbon::now()->startOfDay()->getTimestamp() ])->count,
                        'subtitle' => 'Сумма продаж',
                        'customClass' => '',
                    ],
                ]
            ];
        }

        return [
            'success' => false
        ];
    }

    public function loadChart(Request $request)
    {
        $chart = $request->get('chart');
        $range = $request->get('range');

        $start = Carbon::now()->subMonths(3)->subRealDays(14)->startOfDay();
        $end = Carbon::now()->subMonths(3)->endOfDay();
        $period = CarbonPeriod::create($start, $end);

        //2020-12-17 — 2020-12-25

        if ($range){
            $ex = explode(' — ', $range);
            if (count($ex) === 2){
                $period = CarbonPeriod::create($ex[0], $ex[1]);
                $start = $period->start->startOfDay();
                $end = $period->end->endOfDay();
            }
        }

        if (\Gate::allows('ext', ['dashboard', 'stat.view'])){
            switch ($chart){
                case 'registers':
                    $data = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(reg_time), '%d.%m.%y') as dmy, COUNT(*) as count FROM users WHERE reg_time > ? AND reg_time < ? GROUP BY dmy ORDER BY reg_time", [$start->getTimestamp(), $end->getTimestamp()]));
                    $data = $data->mapWithKeys(function ($item) { return [$item->dmy => $item->count]; });

                    $chart = self::generateChart($data->keys()->toArray(), [
                        [
                            'name' => 'Регистраций',
                            'data' => $data->values()->toArray()
                        ]
                    ],
                        [
                            'today' => \DB::selectOne("SELECT COUNT(id) as count FROM users WHERE reg_time > ?", [ Carbon::now()->startOfDay()->getTimestamp() ])->count
                        ]);
                    break;

                case 'players':
                    $data = collect(\DB::select("SELECT DATE_FORMAT(updated_at, '%d.%m.%y') as dmy, COUNT(DISTINCT uuid) as count FROM snooper WHERE updated_at > ? AND updated_at < ? GROUP BY dmy ORDER BY updated_at", [$start, $end]));
                    $data = $data->mapWithKeys(function ($item) { return [$item->dmy => $item->count]; });
                    $chart = self::generateChart($data->keys()->toArray(),
                        [
                            [
                                'name' => 'Входов в игру',
                                'data' => $data->values()->toArray()
                            ]
                        ],
                        [
                            'today' => \DB::selectOne("SELECT COUNT(DISTINCT uuid) as count FROM snooper WHERE updated_at > ? OR updated_at", [ Carbon::now()->startOfDay() ])->count
                        ]
                    );
                    break;

                case 'posts':
                    $data = collect(\DB::select("SELECT DATE_FORMAT(created_at, '%d.%m.%y') as dmy, COUNT(id) as count FROM chatter_post WHERE created_at > ? AND created_at < ? GROUP BY dmy ORDER BY updated_at", [$start, $end]));
                    $data = $data->mapWithKeys(function ($item) { return [$item->dmy => $item->count]; });
                    $chart = self::generateChart($data->keys()->toArray(),
                        [
                            [
                                'name' => 'Постов',
                                'data' => $data->values()->toArray()
                            ]
                        ],
                        [
                            'today' => \DB::selectOne("SELECT COUNT(id) as count FROM chatter_post WHERE created_at > ?", [ Carbon::now()->startOfDay() ])->count
                        ]
                    );
                    break;

                case 'moders':
                    $data = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(`time`), '%d.%m.%y') as dmy, COUNT(id) as count FROM moder_requests WHERE `time` > ? AND `time` < ? GROUP BY dmy ORDER BY `time`", [$start->getTimestamp(), $end->getTimestamp()]));
                    $data = $data->mapWithKeys(function ($item) { return [$item->dmy => $item->count]; });
                    $chart = self::generateChart($data->keys()->toArray(),
                        [
                            [
                                'name' => 'Заявок',
                                'data' => $data->values()->toArray()
                            ]
                        ],
                        [
                            'today' => \DB::selectOne("SELECT COUNT(id) as count FROM moder_requests WHERE time > ?", [ Carbon::now()->startOfDay()->getTimestamp() ])->count
                        ]
                    );
                    break;
            }
        }

        if (\Gate::allows('ext', ['dashboard', 'group_buys.view']) && $chart === 'group_buys') {
            $data = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(`time`), '%d.%m.%y') as dmy, COUNT(*) as count FROM activity WHERE time > ? AND time < ? AND action = 'buygroup' GROUP BY dmy ORDER BY `time`", [$start->getTimestamp(), $end->getTimestamp()]));
            $data = $data->mapWithKeys(function ($item) { return [$item->dmy => $item->count]; });
            $chart = self::generateChart($data->keys()->toArray(),
                [
                    [
                        'name' => 'Покупок',
                        'data' => $data->values()->toArray()
                    ]
                ],
                [
                    'today' => \DB::selectOne("SELECT COUNT(id) as count FROM activity WHERE time > ? AND action = 'buygroup'", [ Carbon::now()->startOfDay()->getTimestamp() ])->count
                ]
            );
        }

        if (\Gate::allows('ext', ['dashboard', 'shop_buys.view']) && $chart === 'shop_buys') {
            $data = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(`time`), '%d.%m.%y') as dmy, COUNT(*) as count FROM activity WHERE time > ? AND time < ? AND action = 'buyitem' GROUP BY dmy ORDER BY `time`", [$start->getTimestamp(), $end->getTimestamp()]));
            $data = $data->mapWithKeys(function ($item) { return [$item->dmy => $item->count]; });
            $chart = self::generateChart($data->keys()->toArray(),
                [
                    [
                        'name' => 'Покупок',
                        'data' => $data->values()->toArray()
                    ]
                ],
                [
                    'today' => \DB::selectOne("SELECT COUNT(id) as count FROM activity WHERE time > ? AND action = 'buyitem'", [ Carbon::now()->startOfDay()->getTimestamp() ])->count
                ]
            );
        }

        if (\Gate::allows('ext', ['dashboard', 'api_buys.view']) && $chart === 'api_buys') {
            $data = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(`time`), '%d.%m.%y') as dmy, COUNT(*) as count FROM activity WHERE time > ? AND time < ? AND action = 'api_withdraw' GROUP BY dmy ORDER BY `time`", [$start->getTimestamp(), $end->getTimestamp()]));
            $data = $data->mapWithKeys(function ($item) { return [$item->dmy => $item->count]; });
            $chart = self::generateChart($data->keys()->toArray(),
                [
                    [
                        'name' => 'Покупок',
                        'data' => $data->values()->toArray()
                    ]
                ],
                [
                    'today' => \DB::selectOne("SELECT COUNT(id) as count FROM activity WHERE time > ? AND action = 'api_withdraw'", [ Carbon::now()->startOfDay()->getTimestamp() ])->count
                ]
            );
        }

        if (\Gate::allows('ext', ['dashboard', 'password_chart.view']) && $chart === 'password_chart') {
            $data = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(`time`), '%d.%m.%y') as dmy, COUNT(*) as count FROM activity WHERE time > ? AND time < ? AND action = 'changepass' GROUP BY dmy ORDER BY `time`", [$start->getTimestamp(), $end->getTimestamp()]));
            $data2 = collect(\DB::select("SELECT DATE_FORMAT(`created_at`, '%d.%m.%y') as dmy, COUNT(*) as count FROM password_resets WHERE created_at > ? AND created_at < ? GROUP BY dmy ORDER BY `created_at`", [$start, $end]));

            $data = $data->mapWithKeys(function ($item) {
                return [$item->dmy => $item->count];
            });

            $data2 = $data2->mapWithKeys(function ($item) {
                return [$item->dmy => $item->count];
            });

            $chart = self::generateChart($data->keys()->toArray(),
                [
                    [
                        'name' => 'Успешных',
                        'data' => $data2->values()->toArray()
                    ],
                    [
                        'name' => 'Запросов',
                        'data' => $data->values()->toArray()
                    ]
                ],
                [
                    'today' => \DB::selectOne("SELECT COUNT(*) as count FROM password_resets WHERE created_at > ?", [ Carbon::now()->startOfDay() ])->count
                ]
            );
        }

        if (\Gate::allows('ext', ['dashboard', 'bans_chart.view']) && $chart === 'bans_chart') {
            $data = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(`Time`), '%d.%m.%y') as dmy, COUNT(*) as count FROM ban_list WHERE Time > ? AND Time < ? GROUP BY dmy ORDER BY `Time`", [$start->getTimestamp(), $end->getTimestamp()]));
            $data2 = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(`Temptime`), '%d.%m.%y') as dmy, COUNT(*) as count FROM ban_list WHERE Temptime > ? AND Temptime < ? GROUP BY dmy ORDER BY `Temptime`", [$start->getTimestamp(), $end->getTimestamp()]));

            $data = $data->mapWithKeys(function ($item) {
                return [$item->dmy => $item->count];
            });

            $data2 = $data2->mapWithKeys(function ($item) {
                return [$item->dmy => $item->count];
            });

            $chart = self::generateChart($data->keys()->toArray(),
                [
                    [
                        'name' => 'Начало',
                        'data' => $data->values()->toArray()
                    ],
                    [
                        'name' => 'Окончание',
                        'data' => $data2->values()->toArray()
                    ]
                ],
                [
                    'today' => \DB::selectOne("SELECT COUNT(*) as count FROM ban_list WHERE Time > ?", [ Carbon::now()->startOfDay()->getTimestamp() ])->count
                ]
            );
        }

        if (\Gate::allows('ext', ['dashboard', 'bans_chart.view']) && $chart === 'bans_reasons'){
            $data = collect(\DB::select("SELECT Reason as reason, COUNT(*) as count FROM ban_list GROUP BY reason"));

            $data = $data->filter(function ($item, $key) {
                return $item->count > 5;
            })->mapWithKeys(function ($item) {
                return [$item->reason => $item->count];
            });

            $chart = self::generateChart($data->keys()->toArray(), $data->values()->toArray());
        }

        if (\Gate::allows('ext', ['dashboard', '2fa_chart.view']) && $chart === 'twofa_chart') {
            $data = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(`time`), '%d.%m.%y') as dmy, COUNT(*) as count FROM activity WHERE time > ? AND time < ? AND action = 'enableotp' GROUP BY dmy ORDER BY `time`", [$start->getTimestamp(), $end->getTimestamp()]));

            $data2 = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(`time`), '%d.%m.%y') as dmy, COUNT(*) as count FROM activity WHERE time > ? AND time < ? AND action = 'disableotp' GROUP BY dmy ORDER BY `time`", [$start->getTimestamp(), $end->getTimestamp()]));

            $data = $data->mapWithKeys(function ($item) {
                return [$item->dmy => $item->count];
            });

            $data2 = $data2->mapWithKeys(function ($item) {
                return [$item->dmy => $item->count];
            });

            $chart = self::generateChart($data->keys()->toArray(),
                [
                    [
                        'name' => 'Включений',
                        'data' => $data->values()->toArray()
                    ],
                    [
                        'name' => 'Отключений',
                        'data' => $data2->values()->toArray()
                    ]
                ]
            );
        }

        if (\Gate::allows('ext', ['dashboard_profit', 'daily_chart.view']) && $chart === 'donate') {
            $data = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(`time`), '%d.%m.%y') as dmy, ROUND(IFNULL(SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.profit'))),0) + IFNULL(SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.credited'))), 0)) as sum FROM `activity` WHERE (action = 'unitpay_add' OR action = 'enot_add') AND time > ? AND time < ? GROUP BY dmy ORDER BY `time`", [$start->getTimestamp(), $end->getTimestamp()]));
            $data = $data->mapWithKeys(function ($item) {
                return [$item->dmy => $item->sum];
            });

            $days = $period->getStartDate()->diffInDays($period->getEndDate());
            $last_period = CarbonPeriod::between($period->getStartDate()->subDays($days)->startOfDay(), $period->getEndDate()->subDays($days)->endOfDay());

            $today = \DB::selectOne("SELECT (IFNULL(SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.profit'))),0) + IFNULL(SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.credited'))), 0)) as sum FROM `activity` WHERE (action = 'unitpay_add' OR action = 'enot_add') AND time > ?", [Carbon::now()->startOfDay()->getTimestamp()])->sum;

            $this_period = \DB::selectOne("SELECT (IFNULL(SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.profit'))),0) + IFNULL(SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.credited'))), 0)) as sum FROM `activity` WHERE (action = 'unitpay_add' OR action = 'enot_add') AND time > ? AND time < ?", [$start->getTimestamp(), $end->getTimestamp()])->sum;
            $last_period = \DB::selectOne("SELECT (IFNULL(SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.profit'))),0) + IFNULL(SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.credited'))), 0)) as sum FROM `activity` WHERE (action = 'unitpay_add' OR action = 'enot_add') AND time > ? AND time < ?", [$last_period->getStartDate()->getTimestamp(), $last_period->getEndDate()->getTimestamp()])->sum;

            $chart = self::generateChart($data->keys()->toArray(),
                [
                    [
                        'name' => 'Сумма',
                        'data' => $data->values()->toArray()
                    ]
                ],
                [
                    'today' => round($today),
                    'this_period' => round($this_period),
                    'last_period' => round($last_period),
                ]
            );
        }

        if (\Gate::allows('ext', ['dashboard_profit', 'systems_chart.view']) && $chart === 'pay_systems') {
            $data = collect(\DB::select("SELECT JSON_UNQUOTE(JSON_EXTRACT(params, '$.operator')) as `system`, COUNT(*) as count FROM `activity` WHERE action = 'unitpay_add' AND time > ? AND time < ? GROUP BY `system` ORDER BY `time`", [$start->getTimestamp(), $end->getTimestamp()]));
            $data = $data->mapWithKeys(function ($item) {
                return [$item->system => $item->count];
            });

            $chart = self::generateChart($data->keys()->toArray(), $data->values()->toArray());
        }

        if (\Gate::allows('ext', ['dashboard_profit', 'monthly_chart.view']) && $chart === 'profit_year') {
            $data = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(`time`), '%m.%y') as dmy, SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.sum'))) as sum FROM `activity` WHERE action = 'unitpay_add' GROUP BY dmy ORDER BY `time`"));
            $data = $data->mapWithKeys(function ($item) {
                return [$item->dmy => $item->sum];
            });

            $chart = self::generateChart($data->keys()->toArray(),
                [
                    [
                        'name' => 'Платежей',
                        'data' => $data->values()->toArray()
                    ]
                ]
            );
        }

        if (\Gate::allows('ext', ['dashboard_profit', 'daily_groups.view']) && $chart === 'groups') {
            /*$data = collect(\DB::select("SELECT DATE_FORMAT(FROM_UNIXTIME(`time`), '%m.%y') as dmy, SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.sum'))) as sum FROM `activity` WHERE action = 'unitpay_add' GROUP BY dmy ORDER BY `time`"));
            $data = $data->mapWithKeys(function ($item) {
                return [$item->system => $item->count];
            });

            $chart = self::generateChart($data->keys()->toArray(),
                [
                    [
                        'name' => 'Платежей',
                        'data' => $data->values()->toArray()
                    ]
                ]
            );*/

            $chart = false;
        }

        if (\Gate::allows('ext', ['dashboard_profit', 'active_groups.view']) && $chart === 'groups_active') {
            $data = collect(\DB::select("SELECT `groups`.name as `group`, COUNT(*) as count FROM `usergroups` INNER JOIN `groups` ON `groups`.id = group_id WHERE `usergroups`.expire > ? GROUP BY `group`", [$end->getTimestamp()]));
            $data = $data->mapWithKeys(function ($item) {
                return [$item->group => $item->count];
            });

            $chart = self::generateChart($data->keys()->toArray(), $data->values()->toArray());
        }

        if (\Gate::allows('ext', ['dashboard_profit', 'server_profit.view']) && $chart === 'server_profit') {
            $data = collect(\DB::select("SELECT `server`, SUM(sum) as sum FROM (SELECT `servers`.name as `server`, SUM(groups.price) as sum FROM `activity` INNER JOIN `servers` ON `servers`.id = JSON_UNQUOTE(JSON_EXTRACT(params, '$.server')) JOIN `groups` ON `groups`.name = JSON_UNQUOTE(JSON_EXTRACT(params, '$.group')) WHERE action = 'buygroup' AND activity.time > ? AND activity.time < ? GROUP BY `server` UNION SELECT `servers`.name as `server`, SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.sum'))) as sum FROM `activity` INNER JOIN `servers` ON `servers`.api_token = JSON_UNQUOTE(JSON_EXTRACT(params, '$.token')) WHERE action = 'api_withdraw' AND `servers`.api_token IS NOT NULL AND `servers`.api_token != '' AND activity.time > ? AND activity.time < ? GROUP BY `server` UNION SELECT `servers`.name as `server`, SUM(`groups`.price) as sum FROM `cabinet_logs` INNER JOIN `servers` ON `servers`.id = `cabinet_logs`.server JOIN `groups` ON `groups`.pexname = `cabinet_logs`.value WHERE `cabinet_logs`.`type` = 'group.buy' AND `cabinet_logs`.time > ? AND `cabinet_logs`.time < ? GROUP BY `servers`.`name`) x GROUP BY `server`",
                [$start->getTimestamp(), $end->getTimestamp(), $start->getTimestamp(), $end->getTimestamp(), $start->getTimestamp(), $end->getTimestamp()]
            ));

            $data = $data->mapWithKeys(function ($item) {
                return [$item->server => $item->sum];
            });

            $chart = self::generateChart($data->keys()->toArray(),
                [
                    [
                        'name' => 'Сумма',
                        'data' => $data->values()->toArray()
                    ]
                ]
            );
        }

        if (\Gate::allows('ext', ['dashboard_profit', 'branch_profit.view']) && $chart === 'branch_profit') {
            $data_raw = collect(\DB::select("SELECT `server`, SUM(sum) as sum FROM (SELECT `servers`.name as `server`, SUM(groups.price) as sum FROM `activity` INNER JOIN `servers` ON `servers`.id = JSON_UNQUOTE(JSON_EXTRACT(params, '$.server')) JOIN `groups` ON `groups`.name = JSON_UNQUOTE(JSON_EXTRACT(params, '$.group')) WHERE action = 'buygroup' AND activity.time > ? AND activity.time < ? GROUP BY `server` UNION SELECT `servers`.name as `server`, SUM(JSON_UNQUOTE(JSON_EXTRACT(params, '$.sum'))) as sum FROM `activity` INNER JOIN `servers` ON `servers`.api_token = JSON_UNQUOTE(JSON_EXTRACT(params, '$.token')) WHERE action = 'api_withdraw' AND `servers`.api_token IS NOT NULL AND `servers`.api_token != '' AND activity.time > ? AND activity.time < ? GROUP BY `server` UNION SELECT `servers`.name as `server`, SUM(`groups`.price) as sum FROM `cabinet_logs` INNER JOIN `servers` ON `servers`.id = `cabinet_logs`.server JOIN `groups` ON `groups`.pexname = `cabinet_logs`.value WHERE `cabinet_logs`.`type` = 'group.buy' AND `cabinet_logs`.time > ? AND `cabinet_logs`.time < ? GROUP BY `servers`.`name`) x GROUP BY `server`",
                [$start->getTimestamp(), $end->getTimestamp(), $start->getTimestamp(), $end->getTimestamp(), $start->getTimestamp(), $end->getTimestamp()]
            ));

            $data = collect();

            $data_raw->each(function ($item) use (&$data) {
                $branch = $item->server;
                if (substr_count($item->server, ' ') > 0) {
                    $branch = explode(' ', $item->server)[0];
                }
                $sum = $data->get($branch, 0);
                $sum += $item->sum;

                $data->put($branch, $sum);
            });

            $chart = self::generateChart($data->keys()->toArray(),
                [
                    [
                        'name' => 'Сумма',
                        'data' => $data->values()->toArray()
                    ]
                ]
            );
        }

        return \Response::json([
            'success' => true,
            'chart' => $chart
        ]);
    }

    public function lastPosts(Request $request)
    {
        $posts = Post::with('discussion.category')->orderBy('created_at', 'DESC')->limit(25)->get();

        $posts = $posts->map(function ($post, $key) {
            $discussion = $post->discussion;

            return [
                'id' => $post->id,
                'head' => '/head/' . $post->user->uuid . '/50',
                'login' => $post->user->login,
                'time' => \Carbon\Carbon::parse($post->created_at)->diffForHumans(),
                'body' => mb_strimwidth(strip_tags($post->body), 0, 250, '...'),

                'discussion' => [
                    'title' => $discussion->title,
                    'url' => '/forum/discussion/' . $discussion->category->slug . '/' . $discussion->slug
                ]
            ];
        });

        return \Response::json($posts->toArray());
    }

    function rgbcode($str)
    {
        return '#' . substr(md5($str), 1, 6);
    }
}