<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Server;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function get(Request $request){
        $user = User::fromUUID($request->get('user'));

        if (!in_array(trim($request->get('token')), $this->getActiveTokens())){
            return new JsonResponse([
                'error' => 'Неверный токен!'
            ], 403);
        }

        if ($user){
            return new JsonResponse([
                'realmoney' => $user->realmoney,
                'money' => $user->money
            ]);
        }else{
            return new JsonResponse([
                'error' => 'Пользователя не существует!'
            ], 404);
        }
    }

    public function withdraw(Request $request){
        $user = User::fromUUID($request->get('user'));

        if (!in_array(trim($request->get('token')), $this->getActiveTokens())){
            return new JsonResponse([
                'error' => 'Неверный токен!'
            ], 403);
        }

        if ($user){
            $sum = $request->get('sum');

            if($sum <= 0){
                return new JsonResponse([
                    'error' => 'Сумма меньше, либо равна нулю!'
                ], 409);
            }

            if ($user->realmoney >= $sum){
                Activity::user_action($user, 'api_withdraw', ['sum' => $sum, 'token' => $request->get('token')]);
                $user->withdrawRealmoney($sum);

                return new JsonResponse([
                    'result' => 'ok'
                ]);
            }else{
                return new JsonResponse([
                    'error' => 'Недостаточно денег!'
                ], 409);
            }
        }else{
            return new JsonResponse([
                'error' => 'Пользователя не существует!'
            ], 404);
        }
    }
    
    public function getActiveTokens(){
        $tokens = collect();
        
        foreach (Server::getActive() as $server){
            if ($server->api_token && is_string($server->api_token) && !empty($server->api_token)){
                $tokens->push($server->api_token);
            }
        }
        
        return $tokens->toArray();
    }
}
