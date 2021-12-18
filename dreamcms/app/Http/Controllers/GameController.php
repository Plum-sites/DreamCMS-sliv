<?php

namespace App\Http\Controllers;

use App\Models\CaseChest;
use App\Models\DonateGroup;
use App\Models\Kit;
use App\Models\Server;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function load(Request $request){
        $server = Server::find($request->get('server'));
        $user = \Auth::user();

        // Donate groups

        /* @var $groups Collection */
        $groups = DonateGroup::getActive();
        $groups = $groups->map(function ($group){
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
            return $group;
        });

        // Cases

        $cases = CaseChest::all();

        $cases = $cases->map(function ($case) use ($server){
            /* @var $case CaseChest */
            $case->items = $case->getItemsForServer($server);
            return $case;
        });

        // Kits

        $kits = Kit::all();

        $kits = $kits->map(function ($kit) use ($server){
            /* @var $kit Kit */
            $kit->items = $kit->getItemsForServer($server);
            return $kit;
        });

        // User

        $role = '§fИгрок';
        $allroles = $user->getAllRoles();

        if ($allroles->contains('name', 'vip')) $role = '§a> VIP';
        if ($allroles->contains('name', 'premium')) $role = '§9> Premium';
        if ($allroles->contains('name', 'deluxe')) $role = '§d> Deluxe';
        if ($allroles->contains('name', 'legend')) $role = '§6> Legend';

        return [
            'success' => true,
            'groups' => $groups,
            'cases' => $cases,
            'kits' => $kits,
            'user' => [
                'role' => $role,
                'balance' => $user->realmoney
            ]
        ];
    }

    public function auth(Request $request){
        $user = $this->getUser($request);
        $server = $this->getServer($request);

        if ($user){
            if ($server){
                $token = \Auth::login($user);

                return [
                    'success' => true,
                    'token' => $token,
                    'server' => $server
                ];
            }else{
                return [
                    'success' => false,
                    'message' => 'Данный сервер не привязан! Обратитесь к администрации!'
                ];
            }
        }else{
            return [
                'success' => false,
                'message' => 'Ошибка авторизации! Перезапустите лаунчер!'
            ];
        }
    }

    private function getUser(Request $request){
        $user = User::where([
            ['accessToken', '=', $request->get('token')],
            ['uuid', '=', $request->get('uuid')]
        ])->first();

        return $user;
    }

    private function getServer(Request $request){
        $get = ['id', 'name', 'version', 'online', 'maxonline', 'ecomanager', 'donate', 'shop_id', 'branch'];

        if (Str::contains($request->get('server'), '/')){
            $info = explode('/', $request->get('server'));

            $ipport = explode(':', $info[1]);

            $server = Server::where([
                ['ip', '=', $ipport[0]],
                ['port', '=', $ipport[1]]
            ])->get($get)->first();

            if (!$server){
                $server = Server::where([
                    ['ip', '=', $info[0]],
                    ['port', '=', $ipport[1]]
                ])->get($get)->first();
            }
        }else{
            $ipport = explode(':', $request->get('server'));
            $server = Server::where([
                ['ip', '=', $ipport[0]],
                ['port', '=', $ipport[1]]
            ])->get($get)->first();
        }

        if (!$server){
            abort(403, 'Сервер не найден!');
        }

        return $server;
    }

    public function index(Request $request){
        $user = $this->getUser($request);
        $server = $this->getServer($request);
        $token = \Auth::login($user);

        return view('game.index', [
            'user' => $user,
            'token' => $token,
            'server' => $server
        ]);
    }

}